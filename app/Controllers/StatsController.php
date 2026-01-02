<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Config\MongoStats;
use App\Models\Menu;

class StatsController extends Controller
{
    /**
     * Dashboard des statistiques MongoDB
     */
    public function index()
    {
        // Vérifier que l'utilisateur est admin
        $userRole = Session::get('user_role');
        if (!$userRole || $userRole !== 'administrateur') {
            $this->redirect('/');
            return;
        }

        // Récupérer les paramètres de filtres CA
        $filtreMenuId = isset($_GET['menu_id']) && $_GET['menu_id'] !== '' ? (int)$_GET['menu_id'] : null;
        $filtreDateDebut = isset($_GET['date_debut']) && $_GET['date_debut'] !== '' ? $_GET['date_debut'] : null;
        $filtreDateFin = isset($_GET['date_fin']) && $_GET['date_fin'] !== '' ? $_GET['date_fin'] : null;

        // Récupérer les statistiques MongoDB
        require_once __DIR__ . '/../config/mongodb.php';
        $mongoStats = new \App\Config\MongoStats();
        
        $statsGlobales = $mongoStats->getGlobalStats();
        $topMenus = $mongoStats->getTopMenus(5);
        
        // Commandes par menu depuis MongoDB
        $commandesParMenu = $mongoStats->getCommandesParMenu($filtreMenuId, $filtreDateDebut, $filtreDateFin);
        
        // CA par menu avec filtres depuis MongoDB
        $caParMenu = $mongoStats->getCAParMenu($filtreMenuId, $filtreDateDebut, $filtreDateFin);

        // Récupérer tous les menus pour le dropdown
        $menuModel = new Menu();
        $allMenus = $menuModel->findAll();

        // Préparer les données pour Chart.js
        $chartData = $this->prepareChartData($commandesParMenu, $topMenus, $caParMenu, $allMenus);

        $this->render('admin/stats', [
            'title' => 'Statistiques MongoDB',
            'statsGlobales' => $statsGlobales,
            'topMenus' => $topMenus,
            'commandesParMenu' => $commandesParMenu,
            'chartData' => $chartData,
            'allMenus' => $allMenus,
            'caParMenu' => $caParMenu,
            'filtreMenuId' => $filtreMenuId,
            'filtreDateDebut' => $filtreDateDebut,
            'filtreDateFin' => $filtreDateFin
        ]);
    }

    /**
     * Prépare les données pour les graphiques Chart.js
     */
    private function prepareChartData(array $commandesParMenu, array $topMenus, array $caParMenu, array $allMenus): array
    {
        // Préparer données top menus (vues)
        $titresMenus = [];
        $vuesMenus = [];
        $couleursMenus = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'];

        foreach ($topMenus as $index => $menu) {
            $titresMenus[] = $menu['titre'] ?? 'Menu #' . $menu['_id'];
            $vuesMenus[] = $menu['total_vues'];
        }

        // Préparer données commandes par menu (graphique comparatif)
        $menusCommandes = [];
        $commandesValues = [];
        $personnesValues = [];
        $couleursCommandes = [
            '#007bff', '#28a745', '#17a2b8', '#ffc107', '#dc3545', 
            '#6610f2', '#20c997', '#fd7e14', '#e83e8c', '#6c757d'
        ];

        // Créer un mapping menu_id => titre
        $menuTitres = [];
        foreach ($allMenus as $menu) {
            $menuTitres[$menu['menu_id']] = $menu['titre'];
        }

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
            'menus' => [
                'labels' => $titresMenus,
                'data' => $vuesMenus,
                'colors' => $couleursMenus
            ],
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
