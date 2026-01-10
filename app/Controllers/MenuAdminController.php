<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Menu;

/**
 * Contrôleur Admin Menu
 * Gestion des menus par les employés et administrateurs (CRUD)
 */
class MenuAdminController extends Controller
{
    private Menu $menuModel;

    public function __construct()
    {
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

        $this->menuModel = new Menu();
    }

    /**
     * Liste de tous les menus (actifs et inactifs) pour gestion
     */
    public function index(): void
    {
        $menus = $this->menuModel->findAll(); // Tous les menus, pas seulement actifs

        // Charger les plats pour chaque menu
        foreach ($menus as &$menu) {
            $menu['plats'] = $this->menuModel->getPlatsForMenu($menu['menu_id']);
        }

        $this->render('admin/menus/index', [
            'menus' => $menus,
            'title' => 'Gestion des Menus'
        ]);
    }

    /**
     * Formulaire de création d'un nouveau menu
     */
    public function create(): void
    {
        // Charger tous les plats disponibles
        $platModel = new \App\Models\Plat();
        $plats = $platModel::findAllPlats();

        $this->render('admin/menus/create', [
            'plats' => $plats,
            'title' => 'Créer un Menu'
        ]);
    }

    /**
     * Enregistrement d'un nouveau menu
     */
    public function store(Request $request): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/menus');
            return;
        }

        $errors = [];

        // Validation
        $titre = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prixParPersonne = trim($_POST['prix_par_personne'] ?? '');
        $nombrePersonnesMin = trim($_POST['nombre_personnes_min'] ?? '');
        $theme = trim($_POST['theme'] ?? '');
        $imageUrl = trim($_POST['image_url'] ?? '');

        if (empty($titre)) {
            $errors[] = "Le titre est obligatoire";
        }
        if (empty($prixParPersonne) || !is_numeric($prixParPersonne) || $prixParPersonne <= 0) {
            $errors[] = "Le prix par personne doit être un nombre positif";
        }
        if (empty($nombrePersonnesMin) || !is_numeric($nombrePersonnesMin) || $nombrePersonnesMin <= 0) {
            $errors[] = "Le nombre de personnes minimum doit être un nombre positif";
        }

        if (!empty($errors)) {
            Session::set('flash_error', implode('<br>', $errors));
            $this->redirect('/admin/menus/create');
            return;
        }

        // Créer le menu
        $data = [
            'titre' => $titre,
            'description' => $description,
            'prix_par_personne' => $prixParPersonne,
            'nombre_personne_minimum' => $nombrePersonnesMin,
            'quantite_restante' => $_POST['quantite_restante'] ?? 100 // Stock disponible
        ];

        $menuId = $this->menuModel->create($data);

        if ($menuId) {
            // Ajouter les plats au menu
            $platsSelectionnes = $_POST['plats'] ?? [];
            if (!empty($platsSelectionnes)) {
                $this->menuModel->syncPlats($menuId, $platsSelectionnes);
            }

            Session::set('flash_success', "Menu créé avec succès !");
            $this->redirect('/admin/menus');
        } else {
            Session::set('flash_error', "Erreur lors de la création du menu");
            $this->redirect('/admin/menus/create');
        }
    }

    /**
     * Formulaire de modification d'un menu
     */
    public function edit(Request $request): void
    {
        $id = $request->get('id');

        if (!$id) {
            $this->redirect('/admin/menus');
            return;
        }

        $menu = $this->menuModel->findById((int)$id);

        if (!$menu) {
            Session::set('flash_error', "Menu introuvable");
            $this->redirect('/admin/menus');
            return;
        }

        // Charger tous les plats disponibles
        $platModel = new \App\Models\Plat();
        $plats = $platModel::findAllPlats();

        // Charger les plats actuellement associés à ce menu
        $platIds = $this->menuModel->getPlatIdsForMenu((int)$id);

        $this->render('admin/menus/edit', [
            'menu' => $menu,
            'plats' => $plats,
            'platIds' => $platIds,
            'title' => 'Modifier le Menu'
        ]);
    }

    /**
     * Mise à jour d'un menu existant
     */
    public function update(Request $request): void
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

        $menu = $this->menuModel->findById((int)$id);

        if (!$menu) {
            Session::set('flash_error', "Menu introuvable");
            $this->redirect('/admin/menus');
            return;
        }

        $errors = [];

        // Validation
        $titre = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prixParPersonne = trim($_POST['prix_par_personne'] ?? '');
        $nombrePersonnesMin = trim($_POST['nombre_personne_minimum'] ?? '');
        $regimeAlimentaire = $_POST['regime'] ?? null;
        $quantiteRestante = $_POST['quantite_restante'] ?? 0;

        if (empty($titre)) {
            $errors[] = "Le titre est obligatoire";
        }
        if (empty($prixParPersonne) || !is_numeric($prixParPersonne) || $prixParPersonne <= 0) {
            $errors[] = "Le prix par personne doit être un nombre positif";
        }
        if (empty($nombrePersonnesMin) || !is_numeric($nombrePersonnesMin) || $nombrePersonnesMin <= 0) {
            $errors[] = "Le nombre de personnes minimum doit être un nombre positif";
        }

        if (!empty($errors)) {
            Session::set('flash_error', implode('<br>', $errors));
            $this->redirect('/admin/menus/edit?id=' . $id);
            return;
        }

        // Mettre à jour le menu
        $data = [
            'titre' => $titre,
            'description' => $description,
            'prix_par_personne' => $prixParPersonne,
            'nombre_personne_minimum' => $nombrePersonnesMin,
            'quantite_restante' => $quantiteRestante
        ];

        $success = $this->menuModel->update((int)$id, $data);

        if ($success) {
            // Mettre à jour les plats du menu
            $platsSelectionnes = $_POST['plats'] ?? [];
            $this->menuModel->syncPlats((int)$id, $platsSelectionnes);

            Session::set('flash_success', "Menu mis à jour avec succès !");
            $this->redirect('/admin/menus');
        } else {
            Session::set('flash_error', "Erreur lors de la mise à jour du menu");
            $this->redirect('/admin/menus/edit?id=' . $id);
        }
    }

    /**
     * Suppression d'un menu (soft delete)
     */
    public function delete(Request $request): void
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

        $menu = $this->menuModel->findById((int)$id);

        if (!$menu) {
            Session::set('flash_error', "Menu introuvable");
            $this->redirect('/admin/menus');
            return;
        }

        // Soft delete (désactivation plutôt que suppression - on met quantité à 0)
        $success = $this->menuModel->update((int)$id, ['quantite_restante' => 0]);

        if ($success) {
            Session::set('flash_success', "Menu désactivé avec succès !");
        } else {
            Session::set('flash_error', "Erreur lors de la désactivation du menu");
        }

        $this->redirect('/admin/menus');
    }

    /**
     * Réactivation d'un menu
     */
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

        $success = $this->menuModel->update((int)$id, ['quantite_restante' => 100]); // Réactiver avec stock de 100

        if ($success) {
            Session::set('flash_success', "Menu réactivé avec succès !");
        } else {
            Session::set('flash_error', "Erreur lors de la réactivation du menu");
        }

        $this->redirect('/admin/menus');
    }
}
