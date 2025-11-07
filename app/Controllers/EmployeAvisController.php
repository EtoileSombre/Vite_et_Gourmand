<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Avis;
use App\Helpers\MongoLogger;

/**
 * Contrôleur pour la modération des avis par les employés
 * ECF DWWM - Vite & Gourmand
 */
class EmployeAvisController extends Controller
{
    private Avis $avisModel;
    private MongoLogger $mongoLogger;

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
        $this->mongoLogger = new MongoLogger();
    }

    /**
     * Liste des avis en attente de modération
     */
    public function index(Request $request): void
    {
        // Récupérer les filtres
        $statut = $request->get('statut', 'en attente');
        
        // Récupérer les avis selon le statut
        if ($statut === 'tous') {
            // Tous les avis avec détails
            $stmt = $this->avisModel->getDb()->prepare("
                SELECT a.*, 
                       u.prenom as client_prenom,
                       u.nom as client_nom,
                       u.email as client_email,
                       c.numero_commande,
                       m.titre as menu_titre
                FROM avis a
                LEFT JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
                LEFT JOIN commande c ON a.numero_commande = c.numero_commande
                LEFT JOIN menu m ON c.menu_id = m.menu_id
                ORDER BY a.created_at DESC
            ");
            $stmt->execute();
            $avis = $stmt->fetchAll();
        } else {
            // Avis par statut avec détails
            $stmt = $this->avisModel->getDb()->prepare("
                SELECT a.*, 
                       u.prenom as client_prenom,
                       u.nom as client_nom,
                       u.email as client_email,
                       c.numero_commande,
                       m.titre as menu_titre
                FROM avis a
                LEFT JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
                LEFT JOIN commande c ON a.numero_commande = c.numero_commande
                LEFT JOIN menu m ON c.menu_id = m.menu_id
                WHERE a.statut = :statut
                ORDER BY a.created_at DESC
            ");
            $stmt->execute(['statut' => $statut]);
            $avis = $stmt->fetchAll();
        }
        $stmt = $this->avisModel->getDb()->prepare("
            SELECT COUNT(*) as total 
            FROM avis 
            WHERE statut = 'en attente'
        ");
        $stmt->execute();
        $countEnAttente = $stmt->fetch()['total'] ?? 0;

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
            // Logger dans MongoDB
            $this->mongoLogger->logAvisModeration([
                'avis_id' => (int)$avisId,
                'action' => 'approuvé',
                'employe_id' => Session::get('user_id'),
                'employe_prenom' => Session::get('user_prenom'),
                'date' => date('Y-m-d H:i:s')
            ]);

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
            // Logger dans MongoDB avec motif
            $this->mongoLogger->logAvisModeration([
                'avis_id' => (int)$avisId,
                'action' => 'rejeté',
                'motif' => $motif,
                'employe_id' => Session::get('user_id'),
                'employe_prenom' => Session::get('user_prenom'),
                'date' => date('Y-m-d H:i:s')
            ]);

            Session::set('success', 'Avis rejeté.');
        } else {
            Session::set('error', 'Erreur lors du rejet de l\'avis.');
        }

        header('Location: /employe/avis');
        exit;
    }
}
