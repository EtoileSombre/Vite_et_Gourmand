<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Helpers\MongoLogger;

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

        // Récupérer les statistiques MongoDB
        $statsGlobales = MongoLogger::getStatsGlobales();
        $topMenus = MongoLogger::getTopMenus(5);
        $commandesParJour = MongoLogger::getCommandesParJour(30);

        // Préparer les données pour Chart.js
        $chartData = $this->prepareChartData($commandesParJour, $topMenus);

        $this->render('admin/stats', [
            'title' => 'Statistiques MongoDB',
            'statsGlobales' => $statsGlobales,
            'topMenus' => $topMenus,
            'chartData' => $chartData
        ]);
    }

    /**
     * Prépare les données pour les graphiques Chart.js
     */
    private function prepareChartData(array $commandesParJour, array $topMenus): array
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
            ]
        ];
    }
}
