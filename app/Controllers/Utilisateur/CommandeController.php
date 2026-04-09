<?php

namespace App\Controllers\Utilisateur;

use App\Core\Controller;
use App\Repository\CommandeRepositoryInterface;
use App\Repository\MenuRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Repository\AvisRepositoryInterface;
use App\Repository\CommandeMenuRepositoryInterface;
use App\Repository\SuiviCommandeRepositoryInterface;
use App\Repository\BoissonRepositoryInterface;
use App\Repository\MaterielRepositoryInterface;
use App\Factory\RepositoryFactory;
use App\Core\Request;
use App\Core\Session;
use App\MongoDB\MongoStats;

class CommandeController extends Controller
{
    private CommandeRepositoryInterface $commandeRepository;
    private MenuRepositoryInterface $menuRepository;
    private UserRepositoryInterface $userRepository;
    private AvisRepositoryInterface $avisRepository;
    private CommandeMenuRepositoryInterface $commandeMenuRepository;
    private SuiviCommandeRepositoryInterface $suiviCommandeRepository;
    private BoissonRepositoryInterface $boissonRepository;
    private MaterielRepositoryInterface $materielRepository;

    public function __construct()
    {
        parent::__construct();
        // Utilisation de la Factory pour créer les repositories
        $factory = RepositoryFactory::getInstance();
        $this->commandeRepository = $factory->createCommandeRepository();
        $this->menuRepository = $factory->createMenuRepository();
        $this->userRepository = $factory->createUserRepository();
        $this->avisRepository = $factory->createAvisRepository();
        $this->commandeMenuRepository = $factory->createCommandeMenuRepository();
        $this->suiviCommandeRepository = $factory->createSuiviCommandeRepository();
        $this->boissonRepository = $factory->createBoissonRepository();
        $this->materielRepository = $factory->createMaterielRepository();
    }

    /**
     * Calculer la réduction totale d'une commande à partir de ses lignes de menu
     */
    private function calculateTotalReduction(array $lignesMenus): float
    {
        $reductionTotale = 0;
        foreach ($lignesMenus as $ligne) {
            $reductionTotale += floatval($ligne->getReduction());
        }
        return $reductionTotale;
    }

    /**
     * Calculer le sous-total des menus (somme des lignes)
     */
    private function calculateSousTotal(array $lignesMenus): float
    {
        $sousTotal = 0;
        foreach ($lignesMenus as $ligne) {
            $sousTotal += floatval($ligne->getTotalLigne());
        }
        return $sousTotal;
    }

    public function index()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        $commandes = $this->commandeRepository->findByUser($userId);
        
        // Enrichir chaque commande avec ses lignes de menus
        foreach ($commandes as $commande) {
            $commande->setLignesMenus($this->commandeMenuRepository->findByCommande($commande->getNumeroCommande()));
            $commande->setTotalPersonnes($this->commandeMenuRepository->getTotalPersonnes($commande->getNumeroCommande()));
            $commande->setReductionTotale($this->calculateTotalReduction($commande->getLignesMenus()));
        }
        
        $this->render('utilisateur/commandes/index', [
            'commandes' => $commandes,
            'statuts' => \App\Repository\CommandeRepository::STATUTS
        ]);
    }

    public function create()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        // Récupérer les informations de l'utilisateur pour auto-remplissage
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            Session::set('error', 'Utilisateur introuvable.');
            $this->redirect('/');
        }

        $menus = $this->menuRepository->findActiveWithPhotos();
        
        // Récupérer TOUTES les boissons et matériels disponibles
        $boissons = $this->boissonRepository->findAllAvailable();
        $materiels = $this->materielRepository->findAllAvailable();
        
        // Récupérer le menu pré-sélectionné depuis l'URL
        $request = new Request();
        $menuIdFromUrl = $request->get('menu_id');
        
        // Si un menu est pré-sélectionné, récupérer ses détails
        $menuPreselectionne = null;
        if ($menuIdFromUrl) {
            $menuPreselectionne = $this->menuRepository->findActiveById((int)$menuIdFromUrl);
        }
        
        $this->render('utilisateur/commandes/create', [
            'user' => $user,
            'menus' => $menus,
            'menuPreselectionne' => $menuPreselectionne,
            'menuIdFromUrl' => $menuIdFromUrl,
            'boissons' => $boissons,
            'materiels' => $materiels
        ]);
    }

    public function store()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        if (!csrf_verify()) {
            Session::set('flash_error', 'Erreur de sécurité. Veuillez réessayer.');
            $this->redirect('/commander');
            return;
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
        $numeroCommande = 'C-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));

        // Récupérer les infos du menu pour les calculs
        $menu = $this->menuRepository->findById($menuId);
        
        if (!$menu) {
            Session::set('error', 'Menu introuvable.');
            $this->redirect('/commande/nouvelle');
        }

        // CALCULS AUTOMATIQUES

        // 1. Prix de base du menu
        $prixMenu = $menu->getPrixParPersonne() * $nombrePersonnes;

        // 2. Calcul de la réduction de 10% si +5 personnes par rapport au minimum
        $reductionAppliquee = 0;
        $nombrePersonneMinimum = $menu->getNombrePersonneMinimum();
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
        
        $this->commandeRepository->create([
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
        
        $this->commandeMenuRepository->addMenuToCommande(
            $numeroCommande,
            $menuId,
            $nombrePersonnes,
            $menu->getPrixParPersonne(),
            $reductionAppliquee
        );

        // AJOUT DES BOISSONS
        $boissonsData = $request->post('boissons');
        if (!empty($boissonsData) && is_array($boissonsData)) {
            try {
                foreach ($boissonsData as $boisson) {
                    $this->boissonRepository->addBoissonToCommande(
                        $numeroCommande,
                        (int)$boisson['id'],
                        (int)$boisson['quantite'],
                        (float)$boisson['prix_unitaire']
                    );
                }
            } catch (\Exception $e) {
                error_log("Erreur ajout boissons: " . $e->getMessage());
            }
        }

        // AJOUT DU MATÉRIEL
        $materielsData = $request->post('materiels');
        if (!empty($materielsData) && is_array($materielsData)) {
            try {
                // Date de retour prévue = date prestation + 10 jours
                $dateRetourPrevue = date('Y-m-d H:i:s', strtotime($dateLivraison . ' +10 days'));
                
                foreach ($materielsData as $materiel) {
                    $this->materielRepository->addMaterielToCommande(
                        $numeroCommande,
                        (int)$materiel['id'],
                        (int)$materiel['quantite'],
                        (float)$materiel['caution_unitaire'],
                        $dateRetourPrevue
                    );
                    $this->materielRepository->decrementQuantite(
                        (int)$materiel['id'],
                        (int)$materiel['quantite']
                    );
                }
            } catch (\Exception $e) {
                error_log("Erreur ajout matériel: " . $e->getMessage());
            }
        }

        // ENVOI EMAIL DE CONFIRMATION
        
        $userEmail = Session::get('user_email');
        $userPrenom = Session::get('user_prenom');
        
        if ($userEmail && $userPrenom && $menu) {
            require_once __DIR__ . '/../../config/mail.php';
            
            // Préparer les lignes de menus pour l'email
            $lignesMenus = [[
                'menu_nom' => $menu->getTitre(),
                'nombre_personne' => $nombrePersonnes,
                'prix_par_personne' => $menu->getPrixParPersonne(),
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
        $mongoStats = new MongoStats();
        $mongoStats->logCommande($numeroCommande, [
            'menu_id' => $menuId,
            'prix_total' => $prixTotal,
            'nombre_personne' => $nombrePersonnes,
            'statut' => 'en_attente'
        ]);

        error_log("[COMMANDE] Création : numero={$numeroCommande}, user_id={$userId}, menu_id={$menuId}, personnes={$nombrePersonnes}, total={$prixTotal}€");
        
        Session::set('commande_numero', $numeroCommande);
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

        $commande = $this->commandeRepository->findByNumero($numeroCommande);

        // Vérifier que la commande appartient à l'utilisateur
        if (!$commande || $commande->getUtilisateurId() != $userId) {
            Session::set('error', 'Commande introuvable.');
            $this->redirect('/mes-commandes');
            return;
        }

        // Vérifier que la commande peut être modifiée (uniquement si en_attente)
        if ($commande->getStatut() !== 'en_attente') {
            Session::set('error', 'Cette commande ne peut plus être modifiée car elle a été acceptée par notre équipe.');
            $this->redirect('/mes-commandes');
            return;
        }

        // Enrichir avec lignesMenus
        $commande->setLignesMenus($this->commandeMenuRepository->findByCommande($numeroCommande));
        $commande->setTotalPersonnes($this->commandeMenuRepository->getTotalPersonnes($numeroCommande));

        $menus = $this->menuRepository->findAll();

        $this->render('utilisateur/commandes/edit', [
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

        if (!csrf_verify()) {
            Session::set('flash_error', 'Erreur de sécurité. Veuillez réessayer.');
            $this->redirect('/mes-commandes');
            return;
        }

        $request = new Request();
        $numeroCommande = $request->post('numero_commande');
        $nombrePersonnes = $request->post('nombre_personnes');
        $dateLivraison = $request->post('date_livraison');

        $commande = $this->commandeRepository->findByNumero($numeroCommande);

        // Vérifier que la commande appartient à l'utilisateur
        if (!$commande || $commande->getUtilisateurId() != $userId) {
            Session::set('error', 'Commande introuvable.');
            $this->redirect('/mes-commandes');
            return;
        }

        // Vérifier que la commande peut être modifiée (uniquement si en_attente)
        if ($commande->getStatut() !== 'en_attente') {
            Session::set('error', 'Cette commande ne peut plus être modifiée car elle a été acceptée.');
            $this->redirect('/mes-commandes');
            return;
        }

        // Récupérer les lignes de menus
        $lignesMenus = $this->commandeMenuRepository->findByCommande($numeroCommande);
        $this->commandeRepository->updateByNumero($numeroCommande, [
            'date_prestation' => $dateLivraison
        ]);

        // Si nombre de personnes changé, mettre à jour la première ligne de menu
        if (!empty($lignesMenus) && $nombrePersonnes != $lignesMenus[0]->getNombrePersonne()) {
            $this->commandeMenuRepository->updateLigne(
                $lignesMenus[0]->getCommandeMenuId(),
                $nombrePersonnes,
                $lignesMenus[0]->getPrixParPersonne(),
                $lignesMenus[0]->getReduction()
            );
            
            // Recalculer le total de la commande
            $totalMenus = $this->commandeMenuRepository->getTotalMenus($numeroCommande);
            $nouveauTotal = $totalMenus + $commande->getPrixLivraison();
            
            $this->commandeRepository->updateByNumero($numeroCommande, [
                'total_final' => $nouveauTotal
            ]);
        }

        // Envoyer l'email de modification
        $userEmail = Session::get('user_email');
        $userPrenom = Session::get('user_prenom');
        
        if ($userEmail && $userPrenom) {
            require_once __DIR__ . '/../../config/mail.php';
            
            $lignesMenus = $this->commandeMenuRepository->findByCommande($numeroCommande);
            
            $detailsCommande = [
                'lignesMenus' => array_map(fn($l) => [
                    'menu_nom' => $l->getMenuNom(),
                    'nombre_personne' => $l->getNombrePersonne(),
                    'prix_par_personne' => $l->getPrixParPersonne(),
                    'total_ligne' => $l->getTotalLigne(),
                ], $lignesMenus),
                'date_prestation' => $dateLivraison
            ];
            
            sendOrderUpdateEmail($userEmail, $userPrenom, $numeroCommande, $detailsCommande);
        }

        error_log("[COMMANDE] Modification : numero={$numeroCommande}, user_id={$userId}, date={$dateLivraison}, personnes={$nombrePersonnes}");

        Session::set('commande_modifiee', true);
        $this->redirect('/commande/modifier?numero=' . urlencode($numeroCommande));
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

        $commande = $this->commandeRepository->findByNumero($numeroCommande);

        // Vérifier que la commande appartient à l'utilisateur
        if (!$commande || $commande->getUtilisateurId() != $userId) {
            Session::set('error', 'Commande introuvable.');
            $this->redirect('/mes-commandes');
            return;
        }

        // Vérifier que la commande peut être annulée (uniquement si en_attente)
        if ($commande->getStatut() !== 'en_attente') {
            Session::set('error', 'Cette commande ne peut plus être annulée car elle a déjà été acceptée par notre équipe. Veuillez nous contacter.');
            $this->redirect('/mes-commandes');
            return;
        }
        $this->commandeRepository->updateByNumero($numeroCommande, ['statut' => 'annulee']);

        error_log("[COMMANDE] Annulation : numero={$numeroCommande}, user_id={$userId}");
        
        // Enregistrer dans l'historique de suivi
        $this->suiviCommandeRepository->enregistrerChangement(
            $numeroCommande,
            $commande->getStatut(),
            'annulee',
            $userId,
            'Annulation par l\'utilisateur'
        );

        // Envoyer les emails de notification
        try {
            require_once __DIR__ . '/../../config/mail.php';
            
            // Récupérer les infos utilisateur
            $user = $this->userRepository->findById($userId);
            
            if ($user) {
                // Email à l'utilisateur
                sendCancellationEmailToUser(
                    $user->getEmail(),
                    $user->getPrenom(),
                    $numeroCommande
                );
                
                // Email au restaurant
                sendCancellationEmailToRestaurant(
                    $numeroCommande,
                    $user->getNom() . ' ' . $user->getPrenom(),
                    $user->getEmail()
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

        $commande = $this->commandeRepository->findByNumero($numeroCommande);

        if (!$commande || $commande->getUtilisateurId() != $userId) {
            Session::set('error', 'Commande introuvable');
            $this->redirect('/mes-commandes');
            return;
        }

        // Enrichir avec les lignes de menus, matériel et boissons
        $commande->setLignesMenus($this->commandeMenuRepository->findByCommande($numeroCommande));
        $commande->setTotalPersonnes($this->commandeMenuRepository->getTotalPersonnes($numeroCommande));
        $commande->setReductionTotale($this->calculateTotalReduction($commande->getLignesMenus()));
        $commande->setSousTotal($this->calculateSousTotal($commande->getLignesMenus()));
        $commande->setLignesMateriels($this->materielRepository->getByCommande($numeroCommande));
        $commande->setTotalCaution($this->materielRepository->getTotalCautionByCommande($numeroCommande));
        $commande->setLignesBoissons($this->boissonRepository->getByCommande($numeroCommande));
        $commande->setTotalBoissons($this->boissonRepository->getTotalByCommande($numeroCommande));

        // Récupérer l'historique de suivi
        $historique = $this->suiviCommandeRepository->getHistorique($numeroCommande);

        // Vérifier si un avis a déjà été donné
        $avisExistant = $this->avisRepository->findByCommandeAndUser($numeroCommande, $userId);

        $this->render('utilisateur/commandes/show', [
            'title' => 'Détail de la commande #' . $numeroCommande,
            'commande' => $commande,
            'historique' => $historique,
            'avisExistant' => $avisExistant,
            'statuts' => \App\Repository\CommandeRepository::STATUTS
        ]);
    }
}
