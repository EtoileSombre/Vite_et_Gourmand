<?php

namespace App\Controllers\Employe;

use App\Core\Controller;
use App\Core\Session;
use App\Repository\CommandeRepositoryInterface;
use App\Repository\AvisRepositoryInterface;
use App\Repository\CommandeMenuRepositoryInterface;
use App\Factory\RepositoryFactory;

/**
 * Contrôleur Employé
 * Dashboard et vue d'ensemble pour les employés
 */
class DashboardController extends Controller
{
    private CommandeRepositoryInterface $commandeRepository;
    private AvisRepositoryInterface $avisRepository;
    private CommandeMenuRepositoryInterface $commandeMenuRepository;

    public function __construct()
    {
        // Utilisation de la Factory pour créer les repositories
        $factory = RepositoryFactory::getInstance();
        $this->commandeRepository = $factory->createCommandeRepository();
        $this->avisRepository = $factory->createAvisRepository();
        $this->commandeMenuRepository = $factory->createCommandeMenuRepository();

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
        // Statistiques du jour
        $aujourdhui = date('Y-m-d');
        
        // Commandes en attente (tous statuts sauf annulée et terminée)
        $commandesEnAttente = $this->getCommandesEnAttente();
        
        // Enrichir avec lignesMenus
        foreach ($commandesEnAttente as &$cmd) {
            $cmd['lignesMenus'] = $this->commandeMenuRepository->findByCommande($cmd['numero_commande']);
            $cmd['totalPersonnes'] = $this->commandeMenuRepository->getTotalPersonnes($cmd['numero_commande']);
            // Afficher le premier menu comme menu_nom
            if (!empty($cmd['lignesMenus'])) {
                $cmd['menu_nom'] = $cmd['lignesMenus'][0]['menu_nom'] ?? 'Menu';
            }
        }
        
        // Commandes du jour
        $commandesDuJour = $this->getCommandesDuJour($aujourdhui);
        
        // Avis en attente de modération
        $avisEnAttente = $this->getAvisEnAttente();

        // Stats rapides
        $stats = [
            'commandes_en_attente' => count($commandesEnAttente),
            'commandes_aujourdhui' => count($commandesDuJour),
            'avis_non_moderes' => count($avisEnAttente)
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
    private function getCommandesEnAttente(): array
    {
        return $this->commandeRepository->findByStatuts(['en_attente', 'acceptee', 'en_preparation']);
    }

    /**
     * Récupère les commandes du jour (par date de prestation)
     */
    private function getCommandesDuJour(string $date): array
    {
        return $this->commandeRepository->findByDate($date);
    }

    /**
     * Récupère les avis en attente de modération
     */
    private function getAvisEnAttente(): array
    {
        return $this->avisRepository->findPending();
    }
}
