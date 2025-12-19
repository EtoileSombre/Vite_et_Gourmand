<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Commande;
use App\Models\CommandeMenu;
use App\Config\MongoStats;

/**
 * Contrôleur Employé - Gestion des Commandes
 * Changement de statuts avec obligation de contacter le client
 */
class EmployeCommandeController extends Controller
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

    /**
     * Liste des commandes avec filtres
     */
    public function index(Request $request): void
    {
        // Récupérer les filtres
        $filterStatut = $request->get('statut') ?? 'all';
        $filterClient = $request->get('client') ?? '';
        $filterAujourdhui = $request->get('filter') === 'aujourdhui';

        // Construire la requête selon les filtres
        if ($filterAujourdhui) {
            $commandes = $this->commandeModel->findByDate(date('Y-m-d'));
        } elseif ($filterStatut !== 'all') {
            $commandes = $this->commandeModel->findByStatuts([$filterStatut]);
        } else {
            $commandes = $this->commandeModel->findAllWithDetails();
        }

        // Filtrer par client si nécessaire
        if (!empty($filterClient)) {
            $commandes = array_filter($commandes, function($cmd) use ($filterClient) {
                return stripos($cmd['client_prenom'] ?? '', $filterClient) !== false ||
                       stripos($cmd['client_email'] ?? '', $filterClient) !== false;
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
            'filterClient' => $filterClient,
            'filterAujourdhui' => $filterAujourdhui
        ]);
    }

    /**
     * Formulaire de changement de statut
     * OBLIGATION ECF : Contacter le client AVANT toute modification
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

        // Afficher le formulaire
        $this->render('employe/commandes/change-status', [
            'title' => 'Modifier la Commande',
            'commande' => $commande
        ]);
    }

    /**
     * Traiter le changement de statut avec validation du contact client
     */
    private function processStatusChange(string $numeroCommande, array $commande): void
    {
        $errors = [];

        // Récupérer les données du formulaire
        $nouveauStatut = $_POST['nouveau_statut'] ?? '';
        $motifContact = trim($_POST['motif_contact'] ?? '');
        $modeContact = $_POST['mode_contact'] ?? '';
        $contacteClient = isset($_POST['contacte_client']);

        // Validation
        if (empty($nouveauStatut)) {
            $errors[] = "Le nouveau statut est obligatoire";
        }

        // OBLIGATION ECF : Vérifier le contact client pour modifications/annulations
        $requiresContact = in_array($nouveauStatut, ['refusee', 'annulee']) || 
                          ($commande['statut'] !== 'en_attente' && $nouveauStatut !== $commande['statut']);

        if ($requiresContact) {
            if (!$contacteClient) {
                $errors[] = "Vous devez confirmer avoir contacté le client avant de modifier cette commande";
            }
            if (empty($motifContact)) {
                $errors[] = "Le motif de contact est obligatoire";
            }
            if (empty($modeContact)) {
                $errors[] = "Le mode de contact est obligatoire (GSM ou Email)";
            }
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

        // Ajouter les informations de contact si nécessaire
        if ($requiresContact) {
            $updateData['motif_annulation'] = $motifContact;
            $updateData['mode_contact_annulation'] = $modeContact;
        }

        $success = $this->commandeModel->updateByNumero($numeroCommande, $updateData);

        if ($success) {
            // Logger dans MongoDB
            require_once __DIR__ . '/../config/mongodb.php';
            $mongoStats = new \App\Config\MongoStats();
            $mongoStats->logUserActivity('change_order_status', Session::get('user_id'), [
                'numero_commande' => $numeroCommande,
                'ancien_statut' => $commande['statut'],
                'nouveau_statut' => $nouveauStatut,
                'mode_contact' => $modeContact ?? 'N/A',
                'motif' => $motifContact ?? 'N/A'
            ]);

            // Envoyer les emails automatiques selon le nouveau statut
            require_once __DIR__ . '/../config/mail.php';
            
            // Email #1 : Commande acceptée (validée)
            if ($nouveauStatut === 'validee') {
                error_log("Envoi email commande validée à: " . $commande['client_email']);
                $emailSent = sendOrderAcceptedEmail(
                    $commande['client_email'],
                    $commande['client_prenom'],
                    $numeroCommande,
                    $commande['date_prestation']
                );
                error_log("Email envoyé: " . ($emailSent ? 'OUI' : 'NON'));
            }
            
            // Email #2 : Commande terminée (avec invitation avis)
            if ($nouveauStatut === 'terminee') {
                error_log("Envoi email commande terminée à: " . $commande['client_email']);
                $emailSent = sendOrderCompletedEmail(
                    $commande['client_email'],
                    $commande['client_prenom'],
                    $numeroCommande,
                    $commande['menu_titre'] ?? 'Menu'
                );
                error_log("Email envoyé: " . ($emailSent ? 'OUI' : 'NON'));
            }

            Session::set('flash_success', "Statut de la commande mis à jour avec succès !");
            
            // Rediriger vers la liste des commandes
            $this->redirect('/employe/commandes');
        } else {
            Session::set('flash_error', "Erreur lors de la mise à jour du statut");
            $this->redirect('/employe/commandes/change-status?id=' . $numeroCommande);
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

        $this->render('employe/commandes/view', [
            'title' => 'Détails de la Commande',
            'commande' => $commande,
            'lignesMenus' => $lignesMenus,
            'totalPersonnes' => $totalPersonnes
        ]);
    }
}
