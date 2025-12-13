<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Menu;
use App\Helpers\MongoLogger;

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
        $menus = $this->menuModel->findActiveWithPhotos();
        $themes = $this->menuModel->getAllThemes();
        $regimes = $this->menuModel->getAllRegimes();

        // Log MongoDB
        require_once __DIR__ . '/../config/mongodb.php';
        $mongoStats = new \App\Config\MongoStats();
        $mongoStats->logUserActivity('view_menus_list', Session::get('user_id'), [
            'count' => count($menus)
        ]);

        $this->render('menus/index', [
            'menus' => $menus,
            'themes' => $themes,
            'regimes' => $regimes,
            'title' => 'Nos Menus'
        ]);
    }

    // Détail d'un menu
    public function show(Request $request): void
    {
        $id = $request->get('id');

        if (!$id) {
            Session::set('error', 'Identifiant de menu manquant');
            $this->redirect('/menus');
            return;
        }

        $menu = $this->menuModel->findActiveById((int)$id);

        if (!$menu) {
            Session::set('error', 'Menu introuvable ou indisponible');
            $this->redirect('/menus');
            return;
        }

        $boissons = $this->menuModel->getAllBoissons();
        $materiels = $this->menuModel->getAllMateriel();
        $photos = $this->menuModel->getPhotosMenu((int)$id);

        // Log MongoDB
        require_once __DIR__ . '/../config/mongodb.php';
        $mongoStats = new \App\Config\MongoStats();
        $mongoStats->logMenuView((int)$id, ['titre' => $menu['titre']]);

        // Afficher la vue
        $this->render('menus/show', [
            'menu' => $menu,
            'boissons' => $boissons,
            'materiels' => $materiels,
            'photos' => $photos,
            'title' => $menu['titre']
        ]);
    }
}
