<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Commande;
use App\Models\CommandeMenu;
use App\Models\Menu;
use App\Core\Request;
use App\Core\Session;
use App\Helpers\MongoLogger;

class CommandeController extends Controller
{
    public function index()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        $commandeModel = new Commande();
        $commandeMenuModel = new CommandeMenu();
        $commandes = $commandeModel->findByUser($userId);
        
        // Enrichir chaque commande avec ses lignes de menus
        foreach ($commandes as &$commande) {
            $commande['lignesMenus'] = $commandeMenuModel->findByCommande($commande['numero_commande']);
            $commande['totalPersonnes'] = $commandeMenuModel->getTotalPersonnes($commande['numero_commande']);
        }
        
        $this->render('commandes/index', ['commandes' => $commandes]);
    }

    public function create()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        // Récupérer les informations de l'utilisateur pour auto-remplissage
        $userModel = new \App\Models\User();
        $user = $userModel->findById($userId);
        
        if (!$user) {
            Session::set('error', 'Utilisateur introuvable.');
            $this->redirect('/');
        }

        $menuModel = new Menu();
        $menus = $menuModel->findActiveWithPhotos();
        
        // Récupérer le menu pré-sélectionné depuis l'URL
        $request = new Request();
        $menuIdFromUrl = $request->get('menu_id');
        
        // Si un menu est pré-sélectionné, récupérer ses détails
        $menuPreselectionne = null;
        if ($menuIdFromUrl) {
            $menuPreselectionne = $menuModel->findActiveById((int)$menuIdFromUrl);
        }
        
        $boissonsIds = $request->get('boissons');
        $materielsIds = $request->get('materiels');
        
        $boissonsSelectionnees = [];
        $materielsSelectionnes = [];
        
        // Récupérer les détails des boissons
        if ($boissonsIds) {
            $ids = explode(',', $boissonsIds);
            $boissonsSelectionnees = $menuModel->getBoissonsByIds($ids);
        }
        
        // Récupérer les détails du matériel
        if ($materielsIds) {
            $ids = explode(',', $materielsIds);
            $materielsSelectionnes = $menuModel->getMaterielsByIds($ids);
        }
        
        $this->render('commandes/create', [
            'user' => $user,
            'menus' => $menus,
            'menuPreselectionne' => $menuPreselectionne,
            'menuIdFromUrl' => $menuIdFromUrl,
            'boissonsSelectionnees' => $boissonsSelectionnees,
            'materielsSelectionnes' => $materielsSelectionnes
        ]);
    }

    public function store()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        $request = new Request();
        $menuId = $request->post('menu_id');
        $nombrePersonnes = $request->post('nombre_personnes');
        $dateLivraison = $request->post('date_prestation'); // Correction: c'est date_prestation dans le formulaire
        $heureLivraison = $request->post('heure_livraison');
        $adresseLivraison = $request->post('adresse_livraison');
        $lieuLivraison = $adresseLivraison; 

        $villeLivraison = $request->post('ville_livraison') ?: 'Bordeaux'; 

        $codePostalLivraison = $request->post('code_postal_livraison') ?: '';
        $distanceKm = floatval($request->post('distance_km') ?: 0);
        $pretMateriel = $request->post('pret_materiel') ? 1 : 0;

        // Générer un numéro de commande unique
        $numeroCommande = 'CMD' . date('Ymd') . '-' . str_pad($userId, 4, '0', STR_PAD_LEFT) . '-' . uniqid();

        // Récupérer les infos du menu pour les calculs
        $menuModel = new Menu();
        $menu = $menuModel->findById($menuId);
        
        if (!$menu) {
            Session::set('error', 'Menu introuvable.');
            $this->redirect('/commande/nouvelle');
        }

        // CALCULS AUTOMATIQUES

        // 1. Prix de base du menu
        $prixMenu = $menu['prix_par_personne'] * $nombrePersonnes;

        // 2. Calcul de la réduction de 10% si +5 personnes par rapport au minimum
        $reductionAppliquee = 0;
        $nombrePersonneMinimum = $menu['nombre_personne_minimum'];
        if ($nombrePersonnes >= ($nombrePersonneMinimum + 5)) {
            $reductionAppliquee = $prixMenu * 0.10; // 10% de réduction
        }

        // 3. Calcul des frais de livraison
        // 5€ forfait + 0,59€/km
        $fraisLivraison = 5.00 + ($distanceKm * 0.59);

        // 4. Prix total = Prix menu - Réduction + Frais de livraison
        $totalMenus = $prixMenu - $reductionAppliquee;
        $prixTotal = $totalMenus + $fraisLivraison;

        // CRÉATION DE LA COMMANDE (EN-TÊTE)
        
        $commandeModel = new Commande();
        $commandeModel->create([
            'numero_commande' => $numeroCommande,
            'utilisateur_id' => $userId,
            'date_prestation' => $dateLivraison,
            'heure_livraison' => $heureLivraison,
            'lieu_livraison' => $lieuLivraison,
            'ville_livraison' => $villeLivraison,
            'code_postal_livraison' => $codePostalLivraison,
            'distance_km' => $distanceKm,
            'prix_livraison' => $fraisLivraison,
            'total_final' => $prixTotal,
            'pret_materiel' => $pretMateriel,
            'statut' => 'en_attente'
        ]);

        // AJOUT DES LIGNES DE MENU
        
        $commandeMenuModel = new \App\Models\CommandeMenu();
        $commandeMenuModel->addMenuToCommande(
            $numeroCommande,
            $menuId,
            $nombrePersonnes,
            $menu['prix_par_personne'],
            $reductionAppliquee
        );

        // ENVOI EMAIL DE CONFIRMATION
        
        $userEmail = Session::get('user_email');
        $userPrenom = Session::get('user_prenom');
        
        if ($userEmail && $userPrenom && $menu) {
            require_once __DIR__ . '/../config/mail.php';
            
            // Préparer les lignes de menus pour l'email
            $lignesMenus = [[
                'menu_nom' => $menu['titre'],
                'nombre_personne' => $nombrePersonnes,
                'prix_par_personne' => $menu['prix_par_personne'],
                'total_ligne' => $totalMenus
            ]];
            
            $detailsCommande = [
                'lignesMenus' => $lignesMenus,
                'date_prestation' => $dateLivraison,
                'heure_livraison' => $heureLivraison,
                'adresse_livraison' => $adresseLivraison,
                'reduction' => $reductionAppliquee,
                'frais_livraison' => $fraisLivraison,
                'prix_total' => $prixTotal,
                'pret_materiel' => $pretMateriel
            ];
            
            sendOrderConfirmationEmail($userEmail, $userPrenom, $numeroCommande, $detailsCommande);
        }

        // LOGGING MONGODB
        
        require_once __DIR__ . '/../config/mongodb.php';
        $mongoStats = new \App\Config\MongoStats();
        $mongoStats->logCommande($numeroCommande, [
            'menu_id' => $menuId,
            'prix_total' => $prixTotal,
            'nombre_personne' => $nombrePersonnes,
            'statut' => 'en_attente'
        ]);

        // Message de succès avec détails
        $message = 'Votre commande a été enregistrée avec succès ! Un email de confirmation vous a été envoyé.';
        if ($reductionAppliquee > 0) {
            $message .= sprintf(' Une réduction de 10%% (%.2f €) a été appliquée !', $reductionAppliquee);
        }
        
        Session::set('success', $message);
        $this->redirect('/mes-commandes');
    }

    /**
     * Formulaire de modification de commande
     */
    public function edit()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        $request = new Request();
        $numeroCommande = $request->get('numero');

        $commandeModel = new Commande();
        $commande = $commandeModel->findByNumero($numeroCommande);

        // Vérifier que la commande appartient à l'utilisateur
        if (!$commande || $commande['utilisateur_id'] != $userId) {
            Session::set('error', 'Commande introuvable.');
            $this->redirect('/mes-commandes');
            return;
        }

        // Vérifier que la commande peut être modifiée (uniquement si en_attente)
        if ($commande['statut'] !== 'en_attente') {
            Session::set('error', 'Cette commande ne peut plus être modifiée car elle a été acceptée par notre équipe.');
            $this->redirect('/mes-commandes');
            return;
        }

        // Enrichir avec lignesMenus
        $commandeMenuModel = new CommandeMenu();
        $commande['lignesMenus'] = $commandeMenuModel->findByCommande($numeroCommande);
        $commande['totalPersonnes'] = $commandeMenuModel->getTotalPersonnes($numeroCommande);

        $menuModel = new Menu();
        $menus = $menuModel->findAll();

        $this->render('commandes/edit', [
            'commande' => $commande,
            'menus' => $menus
        ]);
    }

    /**
     * Mise à jour d'une commande
     */
    public function update()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        $request = new Request();
        $numeroCommande = $request->post('numero_commande');
        $nombrePersonnes = $request->post('nombre_personnes');
        $dateLivraison = $request->post('date_livraison');

        $commandeModel = new Commande();
        $commande = $commandeModel->findByNumero($numeroCommande);

        // Vérifier que la commande appartient à l'utilisateur
        if (!$commande || $commande['utilisateur_id'] != $userId) {
            Session::set('error', 'Commande introuvable.');
            $this->redirect('/mes-commandes');
            return;
        }

        // Vérifier que la commande peut être modifiée (uniquement si en_attente)
        if ($commande['statut'] !== 'en_attente') {
            Session::set('error', 'Cette commande ne peut plus être modifiée car elle a été acceptée.');
            $this->redirect('/mes-commandes');
            return;
        }

        // Récupérer les lignes de menus
        $commandeMenuModel = new \App\Models\CommandeMenu();
        $lignesMenus = $commandeMenuModel->findByCommande($numeroCommande);
        $commandeModel->updateByNumero($numeroCommande, [
            'date_prestation' => $dateLivraison
        ]);

        // Si nombre de personnes changé, mettre à jour la première ligne de menu
        if (!empty($lignesMenus) && $nombrePersonnes != $lignesMenus[0]['nombre_personne']) {
            $commandeMenuModel->updateLigne(
                $lignesMenus[0]['commande_menu_id'],
                $nombrePersonnes,
                $lignesMenus[0]['prix_par_personne'],
                $lignesMenus[0]['reduction']
            );
            
            // Recalculer le total de la commande
            $totalMenus = $commandeMenuModel->getTotalMenus($numeroCommande);
            $nouveauTotal = $totalMenus + $commande['prix_livraison'];
            
            $commandeModel->updateByNumero($numeroCommande, [
                'total_final' => $nouveauTotal
            ]);
        }

        // Envoyer l'email de modification
        $userEmail = Session::get('user_email');
        $userPrenom = Session::get('user_prenom');
        
        if ($userEmail && $userPrenom) {
            require_once __DIR__ . '/../config/mail.php';
            
            $lignesMenus = $commandeMenuModel->findByCommande($numeroCommande);
            
            $detailsCommande = [
                'lignesMenus' => $lignesMenus,
                'date_prestation' => $dateLivraison
            ];
            
            sendOrderUpdateEmail($userEmail, $userPrenom, $numeroCommande, $detailsCommande);
        }

        $this->redirect('/mes-commandes');
    }

    /**
     * Annulation d'une commande
     */
    public function cancel()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        $request = new Request();
        $numeroCommande = $request->get('numero');

        $commandeModel = new Commande();
        $commande = $commandeModel->findByNumero($numeroCommande);

        // Vérifier que la commande appartient à l'utilisateur
        if (!$commande || $commande['utilisateur_id'] != $userId) {
            Session::set('error', 'Commande introuvable.');
            $this->redirect('/mes-commandes');
            return;
        }

        // Vérifier que la commande peut être annulée (uniquement si en_attente)
        if ($commande['statut'] !== 'en_attente') {
            Session::set('error', 'Cette commande ne peut plus être annulée car elle a déjà été acceptée par notre équipe. Veuillez nous contacter.');
            $this->redirect('/mes-commandes');
            return;
        }
        $commandeModel->updateByNumero($numeroCommande, ['statut' => 'annulee']);
        
        // Enregistrer dans l'historique de suivi
        $suiviModel = new \App\Models\SuiviCommande();
        $suiviModel->enregistrerChangement(
            $numeroCommande,
            $commande['statut'],
            'annulee',
            $userId,
            'Annulation par l\'utilisateur'
        );

        // Envoyer les emails de notification
        try {
            require_once __DIR__ . '/../config/mail.php';
            
            // Récupérer les infos utilisateur
            $userModel = new \App\Models\User();
            $user = $userModel->findById($userId);
            
            if ($user) {
                // Email à l'utilisateur
                sendCancellationEmailToUser(
                    $user['email'],
                    $user['prenom'],
                    $numeroCommande
                );
                
                // Email au restaurant
                sendCancellationEmailToRestaurant(
                    $numeroCommande,
                    $user['nom'] . ' ' . $user['prenom'],
                    $user['email']
                );
            }
        } catch (\Exception $e) {
            error_log("Erreur envoi emails annulation : " . $e->getMessage());
        }

        Session::set('success', 'Votre commande a été annulée avec succès. Un email de confirmation vous a été envoyé.');
        $this->redirect('/mes-commandes');
    }
    
    /**
     * Afficher le détail d'une commande avec son historique de suivi
     */
    public function show(Request $request): void
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
            return;
        }

        $numeroCommande = $request->get('numero');
        if (!$numeroCommande) {
            Session::set('error', 'Commande introuvable');
            $this->redirect('/mes-commandes');
            return;
        }

        $commandeModel = new Commande();
        $commande = $commandeModel->findByNumero($numeroCommande);

        if (!$commande || $commande['utilisateur_id'] != $userId) {
            Session::set('error', 'Commande introuvable');
            $this->redirect('/mes-commandes');
            return;
        }

        // Enrichir avec les lignes de menus
        $commandeMenuModel = new CommandeMenu();
        $commande['lignesMenus'] = $commandeMenuModel->findByCommande($numeroCommande);
        $commande['totalPersonnes'] = $commandeMenuModel->getTotalPersonnes($numeroCommande);

        // Récupérer l'historique de suivi
        $suiviModel = new \App\Models\SuiviCommande();
        $historique = $suiviModel->getHistorique($numeroCommande);

        // Vérifier si un avis a déjà été donné
        $avisModel = new \App\Models\Avis();
        $avisExistant = $avisModel->findByCommandeAndUser($numeroCommande, $userId);

        $this->render('commandes/show', [
            'title' => 'Détail de la commande #' . $numeroCommande,
            'commande' => $commande,
            'historique' => $historique,
            'avisExistant' => $avisExistant
        ]);
    }
}
