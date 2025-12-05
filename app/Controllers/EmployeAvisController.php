<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
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
            header('Location: /');
            exit;
        }

        $this->avisModel = new Avis();
    }

    /**
     * Liste des avis en attente de modération
     */
    public function index(Request $request): void
    {
        // Récupérer les filtres
        $statut = $request->get('statut', 'en attente');
        
        // Récupérer les avis selon le statut via le modèle
        if ($statut === 'tous') {
            $avis = $this->avisModel->findAllWithDetails();
        } else {
            $avis = $this->avisModel->findByStatutWithDetails($statut);
        }
        $countEnAttente = $this->avisModel->countByStatut('en attente');

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
    public function approve(Request $request): void
    {
        $avisId = $request->post('avis_id');

        if (!$avisId) {
            Session::set('error', 'Avis non trouvé.');
            header('Location: /employe/avis');
            exit;
        }
        $success = $this->avisModel->updateStatus((int)$avisId, 'publié');

        if ($success) {
            Session::set('success', 'Avis approuvé et publié avec succès.');
        } else {
            Session::set('error', 'Erreur lors de l\'approbation de l\'avis.');
        }

        header('Location: /employe/avis');
        exit;
    }

    /**
     * Rejeter un avis (passer en statut "rejeté")
     */
    public function reject(Request $request): void
    {
        $avisId = $request->post('avis_id');
        $motif = $request->post('motif', '');

        if (!$avisId) {
            Session::set('error', 'Avis non trouvé.');
            header('Location: /employe/avis');
            exit;
        }
        $success = $this->avisModel->updateStatus((int)$avisId, 'rejeté');

        if ($success) {
            Session::set('success', 'Avis rejeté.');
        } else {
            Session::set('error', 'Erreur lors du rejet de l\'avis.');
        }

        header('Location: /employe/avis');
        exit;
    }
}
