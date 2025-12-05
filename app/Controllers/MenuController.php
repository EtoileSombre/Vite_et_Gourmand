<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Menu;
use App\Helpers\MongoLogger;

/**
 * Contrôleur Menu
 * Gère l'affichage des menus
 */
class MenuController extends Controller
{
    private Menu $menuModel;

    public function __construct()
    {
        $this->menuModel = new Menu();
    }

    // Liste des menus disponibles
    public function index(Request $request): void
    {
        // Récupérer tous les menus actifs
        $menus = $this->menuModel->findActive();

        // Récupérer tous les thèmes pour le filtre
        $themes = $this->menuModel->getAllThemes();

        // Récupérer tous les régimes pour le filtre
        $regimes = $this->menuModel->getAllRegimes();

        // Logger la consultation dans MongoDB
        require_once __DIR__ . '/../config/mongodb.php';
        $mongoStats = new \App\Config\MongoStats();
        $mongoStats->logUserActivity('view_menus_list', Session::get('user_id'), [
            'count' => count($menus)
        ]);

        // Afficher la vue
        $this->render('menus/index', [
            'menus' => $menus,
            'themes' => $themes,
            'regimes' => $regimes,
            'title' => 'Nos Menus'
        ]);
    }

    /**
     * Affiche le détail d'un menu
     * 
     * @param Request $request
     * @return void
     */
    public function show(Request $request): void
    {
        // Récupérer l'ID du menu
        $id = $request->get('id');

        if (!$id) {
            $this->redirect('/menus');
            return;
        }

        // Récupérer le menu
        $menu = $this->menuModel->findActiveById((int)$id);

        if (!$menu) {
            $this->redirect('/menus');
            return;
        }

        // Logger la consultation du menu dans MongoDB
        require_once __DIR__ . '/../config/mongodb.php';
        $mongoStats = new \App\Config\MongoStats();
        $mongoStats->logMenuView((int)$id, ['titre' => $menu['titre']]);

        // Afficher la vue
        $this->render('menus/show', [
            'menu' => $menu,
            'title' => $menu['titre']
        ]);
    }
}
