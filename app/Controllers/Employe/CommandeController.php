<?php

namespace App\Controllers\Employe;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Repository\CommandeRepositoryInterface;
use App\Repository\CommandeMenuRepositoryInterface;
use App\Repository\SuiviCommandeRepositoryInterface;
use App\Repository\BoissonRepositoryInterface;
use App\Repository\MaterielRepositoryInterface;
use App\Factory\RepositoryFactory;
use App\Factory\ServiceFactory;
use App\Services\CommandeService;
use App\Services\Exceptions\CommandeException;
use App\Models\Commande;

/**
 * Contrôleur Employé - Gestion des Commandes
 * Changement de statuts avec obligation de contacter l'utilisateur
 */
class CommandeController extends Controller
{
    private CommandeRepositoryInterface $commandeRepository;
    private CommandeMenuRepositoryInterface $commandeMenuRepository;
    private SuiviCommandeRepositoryInterface $suiviCommandeRepository;
    private BoissonRepositoryInterface $boissonRepository;
    private MaterielRepositoryInterface $materielRepository;
    private CommandeService $commandeService;

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

        // Utilisation de la Factory pour créer le repository
        $factory = RepositoryFactory::getInstance();
        $this->commandeRepository = $factory->createCommandeRepository();
        $this->commandeMenuRepository = $factory->createCommandeMenuRepository();
        $this->suiviCommandeRepository = $factory->createSuiviCommandeRepository();
        $this->boissonRepository = $factory->createBoissonRepository();
        $this->materielRepository = $factory->createMaterielRepository();
        $this->commandeService = ServiceFactory::getInstance()->createCommandeService();
    }

    public function index(Request $request): void
    {
        $filterStatut = $request->get('statut') ?? 'all';
        $filterUtilisateur = $request->get('utilisateur') ?? '';
        $filterAujourdhui = $request->get('filter') === 'aujourdhui';
        if ($filterAujourdhui) {
            $commandes = $this->commandeRepository->findByDate(date('Y-m-d'));
        } elseif ($filterStatut !== 'all') {
            $commandes = $this->commandeRepository->findByStatuts([$filterStatut]);
        } else {
            $commandes = $this->commandeRepository->findAllWithDetails();
        }

        // Filtrer par utilisateur si nécessaire
        if (!empty($filterUtilisateur)) {
            $commandes = array_filter($commandes, function($cmd) use ($filterUtilisateur) {
                return stripos($cmd->getUtilisateurPrenom() ?? '', $filterUtilisateur) !== false ||
                       stripos($cmd->getUtilisateurEmail() ?? '', $filterUtilisateur) !== false;
            });
        }

        // Enrichir chaque commande avec ses lignes de menus, matériel et boissons
        foreach ($commandes as $cmd) {
            $cmd->setLignesMenus($this->commandeMenuRepository->findByCommande($cmd->getNumeroCommande()));
            $cmd->setTotalPersonnes($this->commandeMenuRepository->getTotalPersonnes($cmd->getNumeroCommande()));
            $cmd->setLignesMateriels($this->materielRepository->getByCommande($cmd->getNumeroCommande()));
            $cmd->setTotalCaution($this->materielRepository->getTotalCautionByCommande($cmd->getNumeroCommande()));
            $cmd->setLignesBoissons($this->boissonRepository->getByCommande($cmd->getNumeroCommande()));
            $cmd->setTotalBoissons($this->boissonRepository->getTotalByCommande($cmd->getNumeroCommande()));
            // Afficher le premier menu comme info principale
            if (!empty($cmd->getLignesMenus())) {
                $cmd->setMenuTitre($cmd->getLignesMenus()[0]->getMenuNom() ?? 'Menu');
            }
        }

        $this->render('employe/commandes/index', [
            'title' => 'Gestion des Commandes',
            'commandes' => $commandes,
            'filterStatut' => $filterStatut,
            'filterUtilisateur' => $filterUtilisateur,
            'filterAujourdhui' => $filterAujourdhui,
            'statuts' => \App\Repository\CommandeRepository::STATUTS
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

        $commande = $this->commandeRepository->findByNumero($numeroCommande);

        if (!$commande) {
            Session::set('flash_error', "Commande introuvable");
            $this->redirect('/employe/commandes');
            return;
        }

        // Enrichir avec lignesMenus
        $commande->setLignesMenus($this->commandeMenuRepository->findByCommande($numeroCommande));
        $commande->setTotalPersonnes($this->commandeMenuRepository->getTotalPersonnes($numeroCommande));

        // Si POST : traiter le changement de statut
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify()) {
                Session::set('flash_error', 'Erreur de sécurité.');
                $this->redirect('/employe/commandes/view?id=' . $numeroCommande);
                return;
            }
            $this->processStatusChange($numeroCommande, $commande);
            return;
        }

        $this->redirect('/employe/commandes/view?id=' . $numeroCommande);
    }

    /**
     * Traiter le changement de statut avec validation du contact utilisateur
     */
    private function processStatusChange(string $numeroCommande, Commande $commande): void
    {
        $nouveauStatut = $_POST['nouveau_statut'] ?? '';

        try {
            $result = $this->commandeService->changeStatutByEmploye(
                (int) Session::get('user_id'),
                $numeroCommande,
                (string) $nouveauStatut
            );
        } catch (CommandeException $e) {
            Session::set('flash_error', $e->getMessage());
            $this->redirect('/employe/commandes/view?id=' . $numeroCommande);
            return;
        }

        // Envoi des emails automatiques selon le nouveau statut
        require_once __DIR__ . '/../../config/mail.php';

        if ($nouveauStatut === 'acceptee') {
            $sent = sendOrderAcceptedEmail(
                $commande->getUtilisateurEmail(),
                $commande->getUtilisateurPrenom(),
                $numeroCommande,
                $commande->getDatePrestation()
            );
            if (!$sent) {
                error_log("Échec envoi email acceptation commande #$numeroCommande à " . $commande->getUtilisateurEmail());
            }
        }

        if ($nouveauStatut === 'terminee') {
            $sent = sendOrderCompletedEmail(
                $commande->getUtilisateurEmail(),
                $commande->getUtilisateurPrenom(),
                $numeroCommande,
                $commande->getMenuTitre() ?? 'Menu'
            );
            if (!$sent) {
                error_log("Échec envoi email terminaison commande #$numeroCommande à " . $commande->getUtilisateurEmail());
            }
        }

        if ($nouveauStatut === 'attente_retour_materiel') {
            $materiels = $this->materielRepository->getByCommande($numeroCommande);
            $dateRetour = $commande->getDateRestitutionMateriel() ?? $commande->getDatePrestation();
            $sent = sendMaterialReturnReminderEmail(
                $commande->getUtilisateurEmail(),
                $commande->getUtilisateurPrenom(),
                $numeroCommande,
                $materiels,
                $dateRetour
            );
            if (!$sent) {
                error_log("Échec envoi email rappel matériel commande #$numeroCommande à " . $commande->getUtilisateurEmail());
            }
        }

        Session::set('flash_success', "Statut de la commande mis à jour avec succès !");
        $this->redirect('/employe/commandes/view?id=' . $numeroCommande);
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

        $commande = $this->commandeRepository->findByNumero($numeroCommande);

        if (!$commande) {
            Session::set('flash_error', "Commande introuvable");
            $this->redirect('/employe/commandes');
            return;
        }

        // Récupérer les lignes de menus, matériel et boissons de cette commande
        $lignesMenus = $this->commandeMenuRepository->findByCommande($numeroCommande);
        $totalPersonnes = $this->commandeMenuRepository->getTotalPersonnes($numeroCommande);
        $lignesMateriels = $this->materielRepository->getByCommande($numeroCommande);
        $totalCaution = $this->materielRepository->getTotalCautionByCommande($numeroCommande);
        $lignesBoissons = $this->boissonRepository->getByCommande($numeroCommande);
        $totalBoissons = $this->boissonRepository->getTotalByCommande($numeroCommande);
        $totalMenus = 0;
        foreach ($lignesMenus as $ligne) {
            $totalMenus += $ligne->getTotalLigne();
        }
        
        // Ajouter le total des menus à la commande
        $commande->setTotalMenus($totalMenus);
        $commande->setLignesMenus($lignesMenus);
        $commande->setTotalPersonnes($totalPersonnes);

        // Récupérer l'historique de suivi
        $suivis = $this->suiviCommandeRepository->getHistorique($numeroCommande);

        $this->render('employe/commandes/view', [
            'title' => 'Détails de la Commande',
            'commande' => $commande,
            'suivis' => $suivis,
            'statuts' => \App\Repository\CommandeRepository::STATUTS,
            'lignesMateriels' => $lignesMateriels,
            'totalCaution' => $totalCaution,
            'lignesBoissons' => $lignesBoissons,
            'totalBoissons' => $totalBoissons
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

        if (!csrf_verify()) {
            Session::set('flash_error', 'Erreur de sécurité.');
            $this->redirect('/employe/commandes');
            return;
        }

        $numeroCommande = $_POST['numero_commande'] ?? '';

        $data = [
            'mode_contact'           => $_POST['mode_contact_edit'] ?? '',
            'motif'                  => $_POST['motif_modification'] ?? '',
            'annuler'                => isset($_POST['annuler_commande']) && $_POST['annuler_commande'] == '1',
            'date_prestation'        => $_POST['date_prestation'] ?? '',
            'heure_livraison'        => $_POST['heure_livraison'] ?? '',
            'lieu_livraison'         => $_POST['lieu_livraison'] ?? '',
            'ville_livraison'        => $_POST['ville_livraison'] ?? '',
            'code_postal_livraison'  => $_POST['code_postal_livraison'] ?? '',
            'instructions_speciales' => $_POST['instructions_speciales'] ?? '',
            'quantites_menus'        => $_POST['quantite_menu'] ?? [],
        ];

        // Vérification du consentement de contact utilisateur (exigence UI)
        if (!isset($_POST['contacte_utilisateur_edit'])) {
            Session::set('flash_error', "Vous devez confirmer avoir contacté l'utilisateur");
            $this->redirect('/employe/commandes/view?id=' . $numeroCommande);
            return;
        }

        try {
            $result = $this->commandeService->editCommandeByEmploye(
                (int) Session::get('user_id'),
                (string) $numeroCommande,
                $data
            );
        } catch (CommandeException $e) {
            Session::set('flash_error', $e->getMessage());
            $this->redirect(
                $numeroCommande
                    ? '/employe/commandes/view?id=' . $numeroCommande
                    : '/employe/commandes'
            );
            return;
        }

        Session::set(
            'flash_success',
            $result['annulee']
                ? 'Commande annulée avec succès !'
                : 'Commande modifiée avec succès !'
        );
        $this->redirect('/employe/commandes/view?id=' . $numeroCommande);
    }
}
