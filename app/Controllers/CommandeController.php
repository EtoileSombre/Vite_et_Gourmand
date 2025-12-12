<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Commande;
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
        $commandes = $commandeModel->findByUser($userId);
        
        $this->render('commandes/index', ['commandes' => $commandes]);
    }

    public function create()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        $menuModel = new Menu();
        $menus = $menuModel->findAll();
        
        // Récupérer les boissons et matériels depuis l'URL
        $request = new Request();
        $boissonsIds = $request->get('boissons');
        $materielsIds = $request->get('materiels');
        
        $boissonsSelectionnees = [];
        $materielsSelectionnes = [];
        
        // Récupérer les détails des boissons
        if ($boissonsIds) {
            $ids = explode(',', $boissonsIds);
            require_once __DIR__ . '/../Core/Database.php';
            $db = \App\Core\Database::getInstance()->getConnection();
            
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $db->prepare("SELECT * FROM boisson WHERE boisson_id IN ($placeholders)");
            $stmt->execute($ids);
            $boissonsSelectionnees = $stmt->fetchAll();
        }
        
        // Récupérer les détails du matériel
        if ($materielsIds) {
            $ids = explode(',', $materielsIds);
            require_once __DIR__ . '/../Core/Database.php';
            $db = \App\Core\Database::getInstance()->getConnection();
            
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $db->prepare("SELECT * FROM materiel WHERE materiel_id IN ($placeholders)");
            $stmt->execute($ids);
            $materielsSelectionnes = $stmt->fetchAll();
        }
        
        $this->render('commandes/create', [
            'menus' => $menus,
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
        $dateLivraison = $request->post('date_livraison');
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
        $prixTotal = $prixMenu - $reductionAppliquee + $fraisLivraison;

        // CRÉATION DE LA COMMANDE
        
        $commandeModel = new Commande();
        $commandeModel->create([
            'numero_commande' => $numeroCommande,
            'utilisateur_id' => $userId,
            'menu_id' => $menuId,
            'nombre_personne' => $nombrePersonnes,
            'date_commande' => date('Y-m-d H:i:s'),
            'date_prestation' => $dateLivraison,
            'heure_livraison' => $heureLivraison,
            'lieu_livraison' => $lieuLivraison,
            'ville_livraison' => $villeLivraison,
            'code_postal_livraison' => $codePostalLivraison,
            'distance_km' => $distanceKm,
            'prix_menu' => $prixMenu,
            'prix_livraison' => $fraisLivraison,
            'pret_materiel' => $pretMateriel,
            'statut' => 'en attente'
        ]);

        // ENVOI EMAIL DE CONFIRMATION
        
        $userEmail = Session::get('user_email');
        $userPrenom = Session::get('user_prenom');
        
        if ($userEmail && $userPrenom && $menu) {
            require_once __DIR__ . '/../config/mail.php';
            
            $detailsCommande = [
                'menu_nom' => $menu['titre'],
                'nombre_personne' => $nombrePersonnes,
                'date_prestation' => $dateLivraison,
                'heure_livraison' => $heureLivraison,
                'adresse_livraison' => $adresseLivraison,
                'prix_par_personne' => $menu['prix_par_personne'],
                'prix_menu' => $prixMenu,
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
            'statut' => 'validée'
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
            $this->redirect('/mes-commandes');
        }

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
            $this->redirect('/mes-commandes');
        }
        $commandeModel->updateByNumero($numeroCommande, [
            'nombre_personne' => $nombrePersonnes,
            'date_prestation' => $dateLivraison
        ]);

        // Envoyer l'email de modification
        $userEmail = Session::get('user_email');
        $userPrenom = Session::get('user_prenom');
        
        if ($userEmail && $userPrenom) {
            require_once __DIR__ . '/../config/mail.php';
            
            $detailsCommande = [
                'menu_nom' => $commande['menu_nom'],
                'nombre_personne' => $nombrePersonnes,
                'date_prestation' => $dateLivraison,
                'prix_par_personne' => $commande['menu_prix']
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
            $this->redirect('/mes-commandes');
        }
        $commandeModel->updateByNumero($numeroCommande, ['statut' => 'annulée']);

        $this->redirect('/mes-commandes');
    }
}
