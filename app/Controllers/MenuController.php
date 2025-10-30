<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Menu;
use App\Services\MongoStats;

/**
 * Contrôleur Menu
 * Gère l'affichage des menus
 */
class MenuController extends Controller
{
    private Menu $menuModel;
    private MongoStats $stats;

    public function __construct()
    {
        $this->menuModel = new Menu();
        
        // Charger MongoStats si disponible
        if (class_exists('MongoStats')) {
            require_once __DIR__ . '/../config/MongoStats.php';
            $this->stats = new MongoStats();
        }
    }

    /**
     * Affiche la liste des menus
     * 
     * @param Request $request
     * @return void
     */
    public function index(Request $request): void
    {
        // Récupérer tous les menus actifs
        $menus = $this->menuModel->findActive();

        // Logger la consultation dans MongoDB (si disponible)
        if (isset($this->stats) && $this->stats->isAvailable()) {
            $this->stats->logMenuView('liste_menus', [
                'count' => count($menus),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }

        // Afficher la vue
        $this->render('menus/index', [
            'menus' => $menus,
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

        // Logger la consultation dans MongoDB (si disponible)
        if (isset($this->stats) && $this->stats->isAvailable()) {
            $this->stats->logMenuView($id, [
                'titre' => $menu['titre'],
                'prix' => $menu['prix'],
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }

        // Afficher la vue
        $this->render('menus/show', [
            'menu' => $menu,
            'title' => $menu['titre']
        ]);
    }
}
