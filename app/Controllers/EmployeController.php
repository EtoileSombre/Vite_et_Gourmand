<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Commande;
use App\Models\Avis;

/**
 * Contrôleur Employé
 * Dashboard et vue d'ensemble pour les employés
 */
class EmployeController extends Controller
{
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
    }

    /**
     * Dashboard employé
     * Vue d'ensemble : commandes du jour, avis en attente, stats rapides
     */
    public function index(): void
    {
        $commandeModel = new Commande();
        $avisModel = new Avis();

        // Statistiques du jour
        $aujourdhui = date('Y-m-d');
        
        // Commandes en attente (tous statuts sauf annulée et terminée)
        $commandesEnAttente = $this->getCommandesEnAttente($commandeModel);
        
        // Commandes du jour
        $commandesDuJour = $this->getCommandesDuJour($commandeModel, $aujourdhui);
        
        // Avis en attente de modération
        $avisEnAttente = $this->getAvisEnAttente($avisModel);

        // Stats rapides
        $stats = [
            'commandes_en_attente' => count($commandesEnAttente),
            'commandes_aujourdhui' => count($commandesDuJour),
            'avis_a_moderer' => count($avisEnAttente)
        ];

        $this->render('employe/dashboard', [
            'title' => 'Dashboard Employé',
            'stats' => $stats,
            'commandesEnAttente' => array_slice($commandesEnAttente, 0, 5), // 5 dernières
            'avisEnAttente' => array_slice($avisEnAttente, 0, 3) // 3 derniers
        ]);
    }

    /**
     * Récupère les commandes en attente de traitement
     */
    private function getCommandesEnAttente(Commande $model): array
    {
        // Utiliser une méthode du modèle plutôt qu'accéder directement à $db
        return $model->findByStatuts(['en attente', 'validée', 'en préparation']);
    }

    /**
     * Récupère les commandes du jour (par date de prestation)
     */
    private function getCommandesDuJour(Commande $model, string $date): array
    {
        return $model->findByDate($date);
    }

    /**
     * Récupère les avis en attente de modération
     */
    private function getAvisEnAttente(Avis $model): array
    {
        return $model->findByStatut('en attente');
    }
}
