<?php

namespace App\Controllers\Employe;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Commande;
use App\Models\CommandeMenu;
use App\Config\MongoStats;

/**
 * Contrôleur Employé - Gestion des Commandes
 * Changement de statuts avec obligation de contacter l'utilisateur
 */
class CommandeController extends Controller
{
    private Commande $commandeModel;

    public function __construct()
    {
        // Vérifier que l'utilisateur est connecté et a le rôle employé ou admin
        if (!Session::has('user_id')) {
            header('Location: /login');
            exit;
        }

        $userRole = Session::get('user_role');
        if (!in_array($userRole, ['employé', 'administrateur'])) {
            header('Location: /');
            exit;
        }

        $this->commandeModel = new Commande();
    }

    public function index(Request $request): void
    {
        $filterStatut = $request->get('statut') ?? 'all';
        $filterUtilisateur = $request->get('utilisateur') ?? '';
        $filterAujourdhui = $request->get('filter') === 'aujourdhui';
        if ($filterAujourdhui) {
            $commandes = $this->commandeModel->findByDate(date('Y-m-d'));
        } elseif ($filterStatut !== 'all') {
            $commandes = $this->commandeModel->findByStatuts([$filterStatut]);
        } else {
            $commandes = $this->commandeModel->findAllWithDetails();
        }

        // Filtrer par utilisateur si nécessaire
        if (!empty($filterUtilisateur)) {
            $commandes = array_filter($commandes, function($cmd) use ($filterUtilisateur) {
                return stripos($cmd['utilisateur_prenom'] ?? '', $filterUtilisateur) !== false ||
                       stripos($cmd['utilisateur_email'] ?? '', $filterUtilisateur) !== false;
            });
        }

        // Enrichir chaque commande avec ses lignes de menus
        $commandeMenuModel = new CommandeMenu();
        foreach ($commandes as &$cmd) {
            $cmd['lignesMenus'] = $commandeMenuModel->findByCommande($cmd['numero_commande']);
            $cmd['totalPersonnes'] = $commandeMenuModel->getTotalPersonnes($cmd['numero_commande']);
            // Afficher le premier menu comme info principale
            if (!empty($cmd['lignesMenus'])) {
                $cmd['menu_titre'] = $cmd['lignesMenus'][0]['menu_nom'] ?? 'Menu';
            }
        }

        $this->render('employe/commandes/index', [
            'title' => 'Gestion des Commandes',
            'commandes' => $commandes,
            'filterStatut' => $filterStatut,
            'filterUtilisateur' => $filterUtilisateur,
            'filterAujourdhui' => $filterAujourdhui
        ]);
    }

    /**
     * Formulaire de changement de statut - Contacter l'utilisateur AVANT toute modification
     */
    public function changeStatus(Request $request): void
    {
        $numeroCommande = $request->get('id');

        if (!$numeroCommande) {
            Session::set('flash_error', "Commande introuvable");
            $this->redirect('/employe/commandes');
            return;
        }

        $commande = $this->commandeModel->findByNumero($numeroCommande);

        if (!$commande) {
            Session::set('flash_error', "Commande introuvable");
            $this->redirect('/employe/commandes');
            return;
        }

        // Enrichir avec lignesMenus
        $commandeMenuModel = new CommandeMenu();
        $commande['lignesMenus'] = $commandeMenuModel->findByCommande($numeroCommande);
        $commande['totalPersonnes'] = $commandeMenuModel->getTotalPersonnes($numeroCommande);

        // Si POST : traiter le changement de statut
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processStatusChange($numeroCommande, $commande);
            return;
        }

        $this->redirect('/employe/commandes/view?id=' . $numeroCommande);
    }

    /**
     * Traiter le changement de statut avec validation du contact utilisateur
     */
    private function processStatusChange(string $numeroCommande, array $commande): void
    {
        $errors = [];

        // Récupérer les données du formulaire
        $nouveauStatut = $_POST['nouveau_statut'] ?? '';
        $motifContact = trim($_POST['motif_contact'] ?? '');
        $modeContact = $_POST['mode_contact'] ?? '';
        $contacteUtilisateur = isset($_POST['contacte_utilisateur']);

        // Validation
        if (empty($nouveauStatut)) {
            $errors[] = "Le nouveau statut est obligatoire";
        }

        // Vérifier le contact utilisateur UNIQUEMENT pour refus/annulations
        // Accepter une commande en attente ne nécessite PAS de contact
        $requiresContact = false;
        
        // Contact obligatoire UNIQUEMENT si : annulation explicite
        if (in_array($nouveauStatut, ['annulee'])) {
            $requiresContact = true;
        }

        if ($requiresContact) {
            $errors[] = "Contact client obligatoire veuillez utiliser le formulaire \"Modifier Commande\" pour annuler cette commande";
        }

        if (!empty($errors)) {
            Session::set('flash_error', implode('<br>', $errors));
            $this->redirect('/employe/commandes/change-status?id=' . $numeroCommande);
            return;
        }

        // Mettre à jour le statut
        $updateData = [
            'statut' => $nouveauStatut
        ];

        // Si la commande passe à "terminée", marquer la restitution du matériel comme effectuée
        if ($nouveauStatut === 'terminee') {
            $updateData['restitution_materiel'] = 1;
        }

        // Ajouter les informations de contact si nécessaire
        if ($requiresContact) {
            $updateData['motif_annulation'] = "[Contact: $modeContact] $motifContact";
        }

        $success = $this->commandeModel->updateByNumero($numeroCommande, $updateData);

        if ($success) {
            // Enregistrer dans l'historique de suivi
            $suiviModel = new \App\Models\SuiviCommande();
            $suiviModel->enregistrerChangement(
                $numeroCommande,
                $commande['statut'],
                $nouveauStatut,
                Session::get('user_id'),
                $motifContact ?: null
            );

            // Logger dans MongoDB
            $mongoStats = new \App\Config\MongoStats();
            $mongoStats->logUserActivity('change_order_status', Session::get('user_id'), [
                'numero_commande' => $numeroCommande,
                'ancien_statut' => $commande['statut'],
                'nouveau_statut' => $nouveauStatut,
                'mode_contact' => $modeContact ?? 'N/A',
                'motif' => $motifContact ?? 'N/A'
            ]);

            // Envoyer les emails automatiques selon le nouveau statut
            require_once __DIR__ . '/../../config/mail.php';
            
            // Email #1 : Commande acceptée
            if ($nouveauStatut === 'acceptee') {
                error_log("Envoi email commande acceptée à: " . $commande['utilisateur_email']);
                $emailSent = sendOrderAcceptedEmail(
                    $commande['utilisateur_email'],
                    $commande['utilisateur_prenom'],
                    $numeroCommande,
                    $commande['date_prestation']
                );
                error_log("Email envoyé: " . ($emailSent ? 'OUI' : 'NON'));
            }
            
            // Email #2 : Commande terminée (avec invitation avis)
            if ($nouveauStatut === 'terminee') {
                error_log("Envoi email commande terminée à: " . $commande['utilisateur_email']);
                $emailSent = sendOrderCompletedEmail(
                    $commande['utilisateur_email'],
                    $commande['utilisateur_prenom'],
                    $numeroCommande,
                    $commande['menu_titre'] ?? 'Menu'
                );
                error_log("Email envoyé: " . ($emailSent ? 'OUI' : 'NON'));
            }
            
            // Email #3 : Attente retour matériel (rappel 10 jours, pénalité 600€)
            if ($nouveauStatut === 'attente_retour_materiel') {
                error_log("Envoi email rappel retour matériel à: " . $commande['utilisateur_email']);
                $emailSent = sendMaterialReturnReminderEmail(
                    $commande['utilisateur_email'],
                    $commande['utilisateur_prenom'],
                    $numeroCommande,
                    $commande['date_prestation']
                );
                error_log("Email envoyé: " . ($emailSent ? 'OUI' : 'NON'));
            }

            Session::set('flash_success', "Statut de la commande mis à jour avec succès !");
            
            // Rediriger vers la page de détail de la commande
            $this->redirect('/employe/commandes/view?id=' . $numeroCommande);
        } else {
            Session::set('flash_error', "Erreur lors de la mise à jour du statut");
            $this->redirect('/employe/commandes/view?id=' . $numeroCommande);
        }
    }

    /**
     * Voir les détails d'une commande
     */
    public function view(Request $request): void
    {
        $numeroCommande = $request->get('id');

        if (!$numeroCommande) {
            Session::set('flash_error', "Commande introuvable");
            $this->redirect('/employe/commandes');
            return;
        }

        $commande = $this->commandeModel->findByNumero($numeroCommande);

        if (!$commande) {
            Session::set('flash_error', "Commande introuvable");
            $this->redirect('/employe/commandes');
            return;
        }

        // Récupérer les lignes de menus de cette commande
        $commandeMenuModel = new \App\Models\CommandeMenu();
        $lignesMenus = $commandeMenuModel->findByCommande($numeroCommande);
        $totalPersonnes = $commandeMenuModel->getTotalPersonnes($numeroCommande);

        // Calculer le total des menus
        $totalMenus = 0;
        foreach ($lignesMenus as $ligne) {
            $totalMenus += $ligne['total_ligne'] ?? 0;
        }
        
        // Ajouter le total des menus à la commande
        $commande['total_menus'] = $totalMenus;
        $commande['lignesMenus'] = $lignesMenus;
        $commande['totalPersonnes'] = $totalPersonnes;

        $this->render('employe/commandes/view', [
            'title' => 'Détails de la Commande',
            'commande' => $commande
        ]);
    }

    /**
     * Modifier les détails d'une commande (après contact client obligatoire)
     */
    public function edit(Request $request): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Session::set('flash_error', 'Méthode non autorisée');
            $this->redirect('/employe/commandes');
            return;
        }

        $errors = [];
        $numeroCommande = $_POST['numero_commande'] ?? '';

        // Vérifier que la commande existe
        $commande = $this->commandeModel->findByNumero($numeroCommande);
        if (!$commande) {
            Session::set('flash_error', 'Commande introuvable');
            $this->redirect('/employe/commandes');
            return;
        }

        // Vérifier que la commande n'est pas terminée/annulée
        if (in_array($commande['statut'], ['terminee', 'annulee', 'refusee'])) {
            Session::set('flash_error', 'Cette commande ne peut plus être modifiée');
            $this->redirect('/employe/commandes/view?id=' . $numeroCommande);
            return;
        }

        // Récupérer les données du formulaire
        $datePrestation = $_POST['date_prestation'] ?? '';
        $heureLivraison = $_POST['heure_livraison'] ?? '';
        $lieuLivraison = trim($_POST['lieu_livraison'] ?? '');
        $villeLivraison = trim($_POST['ville_livraison'] ?? '');
        $codePostal = trim($_POST['code_postal_livraison'] ?? '');
        $instructionsSpeciales = trim($_POST['instructions_speciales'] ?? '');
        $quantitesMenus = $_POST['quantite_menu'] ?? [];
        
        // Validation du contact utilisateur
        $contacteUtilisateur = isset($_POST['contacte_utilisateur_edit']);
        $modeContact = $_POST['mode_contact_edit'] ?? '';
        $motifModification = trim($_POST['motif_modification'] ?? '');

        if (!$contacteUtilisateur) {
            $errors[] = "Vous devez confirmer avoir contacté l'utilisateur";
        }
        if (empty($modeContact)) {
            $errors[] = "Le mode de contact est obligatoire";
        }
        if (strlen($motifModification) < 10) {
            $errors[] = "Le motif de modification doit contenir au moins 10 caractères";
        }

        // Validation des données
        if (empty($datePrestation)) {
            $errors[] = "La date de prestation est obligatoire";
        }
        if (empty($heureLivraison)) {
            $errors[] = "L'heure de livraison est obligatoire";
        }
        if (empty($lieuLivraison)) {
            $errors[] = "Le lieu de livraison est obligatoire";
        }
        if (empty($villeLivraison)) {
            $errors[] = "La ville est obligatoire";
        }
        if (empty($codePostal)) {
            $errors[] = "Le code postal est obligatoire";
        }

        if (!empty($errors)) {
            Session::set('flash_error', implode('<br>', $errors));
            $this->redirect('/employe/commandes/view?id=' . $numeroCommande);
            return;
        }

        // Mettre à jour la commande
        $updateData = [
            'date_prestation' => $datePrestation,
            'heure_livraison' => $heureLivraison,
            'lieu_livraison' => $lieuLivraison,
            'ville_livraison' => $villeLivraison,
            'code_postal_livraison' => $codePostal,
            'instructions_speciales' => $instructionsSpeciales,
            'motif_annulation' => "[Contact: $modeContact - MODIFICATION] $motifModification"
        ];

        $success = $this->commandeModel->updateByNumero($numeroCommande, $updateData);

        if ($success) {
            // Mettre à jour les quantités des menus
            $commandeMenuModel = new CommandeMenu();
            foreach ($quantitesMenus as $menuId => $quantite) {
                $commandeMenuModel->updateQuantite($numeroCommande, $menuId, (int)$quantite);
            }

            // Enregistrer dans l'historique
            $suiviModel = new \App\Models\SuiviCommande();
            $suiviModel->enregistrerChangement(
                $numeroCommande,
                $commande['statut'],
                $commande['statut'], // Même statut
                Session::get('user_id'),
                "[MODIFICATION] $motifModification"
            );

            // Logger dans MongoDB
            $mongoStats = new \App\Config\MongoStats();
            $mongoStats->logUserActivity('edit_order', Session::get('user_id'), [
                'numero_commande' => $numeroCommande,
                'mode_contact' => $modeContact,
                'motif' => $motifModification
            ]);

            Session::set('flash_success', 'Commande modifiée avec succès !');
        } else {
            Session::set('flash_error', 'Erreur lors de la modification de la commande');
        }

        $this->redirect('/employe/commandes/view?id=' . $numeroCommande);
    }
}
