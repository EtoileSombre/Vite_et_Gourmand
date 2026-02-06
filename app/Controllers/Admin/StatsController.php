<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Stats\MongoStats;
use App\Models\Menu;

class StatsController extends Controller
{
    public function index()
    {
        // Vérification accès administrateur
        $userRole = Session::get('user_role');
        if (!$userRole || $userRole !== 'administrateur') {
            $this->redirect('/');
            return;
        }

        // Récupération des paramètres de filtres
        $filtreMenuId = isset($_GET['menu_id']) && $_GET['menu_id'] !== '' ? (int)$_GET['menu_id'] : null;
        $filtreDateDebut = isset($_GET['date_debut']) && $_GET['date_debut'] !== '' ? $_GET['date_debut'] : null;
        $filtreDateFin = isset($_GET['date_fin']) && $_GET['date_fin'] !== '' ? $_GET['date_fin'] : null;

        // Statistiques MongoDB
        $mongoStats = new MongoStats();
        
        // Graph Commandes : AVEC filtre menu + dates
        $commandesParMenu = $mongoStats->getCommandesParMenu($filtreMenuId, $filtreDateDebut, $filtreDateFin);
        
        // Tableau CA : AVEC filtre menu + dates (analyse détaillée)
        $caParMenu = $mongoStats->getCAParMenu($filtreMenuId, $filtreDateDebut, $filtreDateFin);

        // Récupérer tous les menus pour le dropdown de filtres
        $menuModel = new Menu();
        $allMenus = $menuModel->findAll();

        // Préparer les données pour Chart.js
        $chartData = $this->prepareChartData($commandesParMenu, $caParMenu, $allMenus);

        $this->render('admin/stats', [
            'title' => 'Statistiques MongoDB - Commandes et CA',
            'commandesParMenu' => $commandesParMenu,
            'caParMenu' => $caParMenu,
            'chartData' => $chartData,
            'allMenus' => $allMenus,
            'filtreMenuId' => $filtreMenuId,
            'filtreDateDebut' => $filtreDateDebut,
            'filtreDateFin' => $filtreDateFin
        ]);
    }

    /**
     * Prépare les données pour les graphiques Chart.js
     */
    private function prepareChartData(
        array $commandesParMenu,
        array $caParMenu, 
        array $allMenus
    ): array
    {
        // Créer un mapping menu_id => titre
        $menuTitres = [];
        foreach ($allMenus as $menu) {
            $menuTitres[$menu['menu_id']] = $menu['titre'];
        }

        // Préparer données commandes par menu
        $menusCommandes = [];
        $commandesValues = [];
        $personnesValues = [];
        $couleursCommandes = [
            '#007bff', '#28a745', '#17a2b8', '#ffc107', '#dc3545', 
            '#6610f2', '#20c997', '#fd7e14', '#e83e8c', '#6c757d'
        ];

        foreach ($commandesParMenu as $index => $data) {
            $menuId = $data['_id'];
            $menusCommandes[] = $menuTitres[$menuId] ?? 'Menu #' . $menuId;
            $commandesValues[] = $data['nombre_commandes'];
            $personnesValues[] = $data['total_personnes'] ?? 0;
        }

        // Préparer données CA par menu
        $menusCa = [];
        $caValues = [];
        $commandesCAValues = [];
        $couleursCa = [
            '#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6610f2', 
            '#20c997', '#fd7e14', '#e83e8c', '#6c757d', '#007bff'
        ];

        foreach ($caParMenu as $index => $data) {
            $menuId = $data['_id'];
            $menusCa[] = $menuTitres[$menuId] ?? 'Menu #' . $menuId;
            $caValues[] = round($data['chiffre_affaires'], 2);
            $commandesCAValues[] = $data['nombre_commandes'];
        }

        return [
            'commandesParMenu' => [
                'labels' => $menusCommandes,
                'commandes' => $commandesValues,
                'personnes' => $personnesValues,
                'colors' => $couleursCommandes
            ],
            'ca' => [
                'labels' => $menusCa,
                'data' => $caValues,
                'commandes' => $commandesCAValues,
                'colors' => $couleursCa
            ]
        ];
    }
}
