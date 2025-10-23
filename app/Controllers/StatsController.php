<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Helpers\MongoLogger;
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
        $statsGlobales = MongoLogger::getStatsGlobales();
        $topMenus = MongoLogger::getTopMenus(5);
        $commandesParJour = MongoLogger::getCommandesParJour(30);
        
        // Récupérer le CA par menu avec filtres
        $caParMenu = MongoLogger::getCaParMenu($filtreMenuId, $filtreDateDebut, $filtreDateFin);

        // Récupérer tous les menus pour le dropdown
        $menuModel = new Menu();
        $allMenus = $menuModel->findAll();

        // Préparer les données pour Chart.js
        $chartData = $this->prepareChartData($commandesParJour, $topMenus, $caParMenu, $allMenus);

        $this->render('admin/stats', [
            'title' => 'Statistiques MongoDB',
            'statsGlobales' => $statsGlobales,
            'topMenus' => $topMenus,
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
    private function prepareChartData(array $commandesParJour, array $topMenus, array $caParMenu, array $allMenus): array
    {
        // Préparer données commandes par jour
        $datesCommandes = [];
        $nombresCommandes = [];
        $chiffresAffaires = [];

        foreach ($commandesParJour as $data) {
            $datesCommandes[] = date('d/m', strtotime($data['_id']));
            $nombresCommandes[] = $data['nombre_commandes'];
            $chiffresAffaires[] = round($data['chiffre_affaires'], 2);
        }

        // Préparer données top menus
        $titresMenus = [];
        $vuesMenus = [];
        $couleursMenus = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'];

        foreach ($topMenus as $index => $menu) {
            $titresMenus[] = $menu['titre'] ?? 'Menu #' . $menu['_id'];
            $vuesMenus[] = $menu['total_vues'];
        }

        // Préparer données CA par menu
        $menusCa = [];
        $caValues = [];
        $commandesValues = [];
        $couleursCa = [
            '#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6610f2', 
            '#20c997', '#fd7e14', '#e83e8c', '#6c757d', '#007bff'
        ];

        // Créer un mapping menu_id => titre
        $menuTitres = [];
        foreach ($allMenus as $menu) {
            $menuTitres[$menu['menu_id']] = $menu['titre'];
        }

        foreach ($caParMenu as $index => $data) {
            $menuId = $data['_id'];
            $menusCa[] = $menuTitres[$menuId] ?? 'Menu #' . $menuId;
            $caValues[] = round($data['chiffre_affaires'], 2);
            $commandesValues[] = $data['nombre_commandes'];
        }

        return [
            'commandes' => [
                'labels' => $datesCommandes,
                'data' => $nombresCommandes,
                'ca' => $chiffresAffaires
            ],
            'menus' => [
                'labels' => $titresMenus,
                'data' => $vuesMenus,
                'colors' => $couleursMenus
            ],
            'ca' => [
                'labels' => $menusCa,
                'data' => $caValues,
                'commandes' => $commandesValues,
                'colors' => $couleursCa
            ]
        ];
    }
}
