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

    /**
     * Affiche la liste des menus
     * 
     * @return void
     */
    public function index(Request $request): void
    {
        // Récupérer tous les menus actifs
        $menus = $this->menuModel->findActive();

        // Logger la consultation dans MongoDB
        MongoLogger::logUserActivity('view_menus_list', Session::get('user_id'), [
            'count' => count($menus)
        ]);

        // Afficher la vue
        $this->render('menus/index', [
            'menus' => $menus,
            'title' => 'Nos Menus'
        ]);
    }

    /**
     * Affiche le détail d'un menu
     * 
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
        MongoLogger::logMenuView((int)$id, Session::get('user_id'), $menu['titre']);

        // Afficher la vue
        $this->render('menus/show', [
            'menu' => $menu,
            'title' => $menu['titre']
        ]);
    }
}
