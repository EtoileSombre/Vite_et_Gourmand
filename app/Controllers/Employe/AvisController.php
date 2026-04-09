<?php

namespace App\Controllers\Employe;

use App\Core\Controller;
use App\Core\Session;
use App\Repository\AvisRepositoryInterface;
use App\Factory\RepositoryFactory;

/**
 * Contrôleur pour la modération des avis par les employés
 * ECF DWWM - Vite & Gourmand
 */
class AvisController extends Controller
{
    private AvisRepositoryInterface $avisRepository;

    public function __construct()
    {
        // Vérification que l'utilisateur est employé ou admin
        $role = Session::get('user_role');
        if (!in_array($role, ['employé', 'administrateur'])) {
            Session::set('error', 'Accès refusé. Réservé aux employés.');
            $this->redirect('/');
            return;
        }

        // Utilisation de la Factory pour créer le repository
        $factory = RepositoryFactory::getInstance();
        $this->avisRepository = $factory->createAvisRepository();
    }

    /**
     * Liste des avis en attente de modération
     */
    public function index(): void
    {
        // Récupérer les filtres
        $statut = $_GET['statut'] ?? 'en_attente';
        
        // Récupérer les avis selon le statut via le repository
        if ($statut === 'tous') {
            $avis = $this->avisRepository->findAllWithDetails();
        } else {
            $avis = $this->avisRepository->findByStatutWithDetails($statut);
        }
        $countEnAttente = $this->avisRepository->countByStatut('en_attente');

        $this->render('employe/avis/index', [
            'title' => 'Modération des Avis',
            'avis' => $avis,
            'statut_filtre' => $statut,
            'count_en_attente' => $countEnAttente
        ]);
    }

    /**
     * Approuver un avis (passer en statut "publié")
     */
    public function approve(): void
    {
        if (!csrf_verify()) {
            Session::set('error', 'Erreur de sécurité.');
            $this->redirect('/employe/avis');
            return;
        }

        $avisId = $_POST['avis_id'] ?? null;

        if (!$avisId) {
            Session::set('error', 'Avis non trouvé.');
            $this->redirect('/employe/avis');
            return;
        }
        $success = $this->avisRepository->updateStatus((int)$avisId, 'publie');

        if ($success) {
            error_log("[AVIS] Approbation : avis_id={$avisId}, par=" . Session::get('user_email'));
            Session::set('success', 'Avis approuvé et publié avec succès.');
        } else {
            error_log("[AVIS] Échec approbation : avis_id={$avisId}");
            Session::set('error', 'Erreur lors de l\'approbation de l\'avis.');
        }

        $this->redirect('/employe/avis');
    }

    /**
     * Rejeter un avis (passer en statut "rejeté")
     */
    public function reject(): void
    {
        if (!csrf_verify()) {
            Session::set('error', 'Erreur de sécurité.');
            $this->redirect('/employe/avis');
            return;
        }

        $avisId = $_POST['avis_id'] ?? null;
        $motif = $_POST['motif'] ?? '';

        if (!$avisId) {
            Session::set('error', 'Avis non trouvé.');
            $this->redirect('/employe/avis');
            return;
        }
        $success = $this->avisRepository->updateStatus((int)$avisId, 'rejete');

        if ($success) {
            error_log("[AVIS] Rejet : avis_id={$avisId}, motif={$motif}, par=" . Session::get('user_email'));
            Session::set('success', 'Avis rejeté.');
        } else {
            error_log("[AVIS] Échec rejet : avis_id={$avisId}");
            Session::set('error', 'Erreur lors du rejet de l\'avis.');
        }

        $this->redirect('/employe/avis');
    }
}
