<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Factory\ServiceFactory;
use App\Services\MenuService;
use App\Services\Exceptions\MenuException;

/**
 * Contrôleur Admin Menu
 * Gestion des menus par les employés et administrateurs (CRUD)
 */
class MenuController extends Controller
{
    private MenuService $menuService;

    public function __construct()
    {
        if (!Session::has('user_id')) {
            header('Location: /login');
            exit;
        }

        $userRole = Session::get('user_role');
        if (!in_array($userRole, ['employé', 'administrateur'])) {
            header('Location: /');
            exit;
        }

        $this->menuService = ServiceFactory::getInstance()->createMenuService();
    }

    public function index(): void
    {
        $this->render('admin/menus/index', [
            'menus' => $this->menuService->listAllWithPlats(),
            'title' => 'Gestion des Menus',
        ]);
    }

    public function create(): void
    {
        $this->render('admin/menus/create', [
            'plats' => $this->menuService->listPlatsWithAllergenes(),
            'title' => 'Créer un Menu',
        ]);
    }

    public function store(Request $request): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/menus');
            return;
        }
        if (!csrf_verify()) {
            Session::set('flash_error', 'Erreur de sécurité.');
            $this->redirect('/admin/menus/create');
            return;
        }

        $data = [
            'titre'                   => $_POST['titre'] ?? '',
            'description'             => $_POST['description'] ?? '',
            'prix_par_personne'       => $_POST['prix_par_personne'] ?? '',
            'nombre_personne_minimum' => $_POST['nombre_personne_minimum'] ?? '',
            'quantite_restante'       => $_POST['quantite_restante'] ?? 100,
        ];
        $platsSelectionnes = $_POST['plats'] ?? [];

        try {
            $menuId = $this->menuService->createMenu($data, $platsSelectionnes);
        } catch (MenuException $e) {
            Session::set('flash_error', $e->getMessage());
            $this->redirect('/admin/menus/create');
            return;
        }

        error_log(sprintf(
            "[ADMIN] Création menu : id=%d, titre=%s, prix=%s€/pers, par=%s",
            $menuId,
            $data['titre'],
            $data['prix_par_personne'],
            Session::get('user_email')
        ));
        Session::set('flash_success', "Menu créé avec succès !");
        $this->redirect('/admin/menus');
    }

    public function edit(Request $request): void
    {
        $id = $request->get('id');
        if (!$id) {
            $this->redirect('/admin/menus');
            return;
        }

        try {
            $payload = $this->menuService->loadForEdit((int) $id);
        } catch (MenuException $e) {
            Session::set('flash_error', $e->getMessage());
            $this->redirect('/admin/menus');
            return;
        }

        $this->render('admin/menus/edit', [
            'menu'    => $payload['menu'],
            'plats'   => $payload['plats'],
            'platIds' => $payload['platIds'],
            'title'   => 'Modifier le Menu',
        ]);
    }

    public function update(Request $request): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/menus');
            return;
        }
        if (!csrf_verify()) {
            Session::set('flash_error', 'Erreur de sécurité.');
            $this->redirect('/admin/menus');
            return;
        }

        $id = $_POST['menu_id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/menus');
            return;
        }

        $data = [
            'titre'                   => $_POST['titre'] ?? '',
            'description'             => $_POST['description'] ?? '',
            'prix_par_personne'       => $_POST['prix_par_personne'] ?? '',
            'nombre_personne_minimum' => $_POST['nombre_personne_minimum'] ?? '',
            'quantite_restante'       => $_POST['quantite_restante'] ?? 0,
        ];
        $platsSelectionnes = $_POST['plats'] ?? [];

        try {
            $this->menuService->updateMenu((int) $id, $data, $platsSelectionnes);
        } catch (MenuException $e) {
            Session::set('flash_error', $e->getMessage());
            $this->redirect('/admin/menus/edit?id=' . $id);
            return;
        }

        error_log(sprintf(
            "[ADMIN] Modification menu : id=%s, titre=%s, prix=%s€/pers, par=%s",
            $id,
            $data['titre'],
            $data['prix_par_personne'],
            Session::get('user_email')
        ));
        Session::set('flash_success', "Menu mis à jour avec succès !");
        $this->redirect('/admin/menus');
    }

    public function delete(Request $request): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/menus');
            return;
        }
        if (!csrf_verify()) {
            Session::set('flash_error', 'Erreur de sécurité.');
            $this->redirect('/admin/menus');
            return;
        }

        $id = $_POST['menu_id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/menus');
            return;
        }

        try {
            $menu = $this->menuService->deleteMenu((int) $id);
        } catch (MenuException $e) {
            Session::set('flash_error', $e->getMessage());
            $this->redirect('/admin/menus');
            return;
        }

        error_log(sprintf(
            "[ADMIN] Suppression menu : id=%s, titre=%s, par=%s",
            $id,
            $menu->getTitre(),
            Session::get('user_email')
        ));
        Session::set('flash_success', "Menu supprimé avec succès !");
        $this->redirect('/admin/menus');
    }

    public function activate(Request $request): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/menus');
            return;
        }

        $id = $_POST['menu_id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/menus');
            return;
        }

        try {
            $this->menuService->reactivate((int) $id);
            Session::set('flash_success', "Menu réactivé avec succès !");
        } catch (MenuException $e) {
            Session::set('flash_error', $e->getMessage());
        }

        $this->redirect('/admin/menus');
    }
}
