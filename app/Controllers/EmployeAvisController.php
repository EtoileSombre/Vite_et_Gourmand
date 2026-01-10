<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Avis;

/**
 * Contrôleur pour la modération des avis par les employés
 * ECF DWWM - Vite & Gourmand
 */
class EmployeAvisController extends Controller
{
    private Avis $avisModel;

    public function __construct()
    {
        // Vérification que l'utilisateur est employé ou admin
        $role = Session::get('user_role');
        if (!in_array($role, ['employé', 'administrateur'])) {
            Session::set('error', 'Accès refusé. Réservé aux employés.');
            $this->redirect('/');
            return;
        }

        $this->avisModel = new Avis();
    }

    /**
     * Liste des avis en attente de modération
     */
    public function index(): void
    {
        // Récupérer les filtres
        $statut = $_GET['statut'] ?? 'en_attente';
        
        // Récupérer les avis selon le statut via le modèle
        if ($statut === 'tous') {
            $avis = $this->avisModel->findAllWithDetails();
        } else {
            $avis = $this->avisModel->findByStatutWithDetails($statut);
        }

        // Compter les avis en attente
        $countEnAttente = $this->avisModel->countByStatut('en_attente');

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
        $avisId = $_POST['avis_id'] ?? null;

        if (!$avisId) {
            Session::set('error', 'Avis non trouvé.');
            $this->redirect('/employe/avis');
            return;
        }

        // Mettre à jour le statut
        $success = $this->avisModel->updateStatus((int)$avisId, 'publie');

        if ($success) {
            Session::set('success', 'Avis approuvé et publié avec succès.');
        } else {
            Session::set('error', 'Erreur lors de l\'approbation de l\'avis.');
        }

        $this->redirect('/employe/avis');
    }

    /**
     * Rejeter un avis (passer en statut "rejeté")
     */
    public function reject(): void
    {
        $avisId = $_POST['avis_id'] ?? null;
        $motif = $_POST['motif'] ?? '';

        if (!$avisId) {
            Session::set('error', 'Avis non trouvé.');
            $this->redirect('/employe/avis');
            return;
        }

        // Mettre à jour le statut
        $success = $this->avisModel->updateStatus((int)$avisId, 'rejete');

        if ($success) {
            Session::set('success', 'Avis rejeté.');
        } else {
            Session::set('error', 'Erreur lors du rejet de l\'avis.');
        }

        $this->redirect('/employe/avis');
    }
}
