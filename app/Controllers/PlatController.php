<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Plat;

/**
 * Contrôleur Plat
 * Gestion des plats par les employés et administrateurs (CRUD)
 */
class PlatController extends Controller
{
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
    }

    /**
     * Affiche la liste des plats
     */
    public function index()
    {
        // Récupérer le filtre par type si présent
        $typeFiltre = $_GET['type'] ?? null;

        // Récupérer tous les plats
        $plats = Plat::findAllPlats($typeFiltre);

        // Statistiques par type
        $stats = Plat::countByType();

        $this->render('admin/plats/index', [
            'title' => 'Gestion des plats',
            'plats' => $plats,
            'stats' => $stats,
            'typeFiltre' => $typeFiltre,
            'typesPlat' => Plat::getTypesPlat()
        ]);
    }

    /**
     * Affiche le formulaire de création d'un plat
     */
    public function create()
    {
        $this->render('admin/plats/create', [
            'title' => 'Créer un plat',
            'typesPlat' => Plat::getTypesPlat()
        ]);
    }

    /**
     * Enregistre un nouveau plat
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/plats');
            return;
        }

        // Validation
        $errors = [];
        $titrePlat = trim($_POST['titre_plat'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $typePlat = $_POST['type_plat'] ?? 'Plat';
        $photo = trim($_POST['photo'] ?? '');

        if (empty($titrePlat)) {
            $errors[] = "Le titre du plat est obligatoire.";
        }

        if (!in_array($typePlat, Plat::getTypesPlat())) {
            $errors[] = "Type de plat invalide.";
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            $this->redirect('/admin/plats/create');
            return;
        }

        // Créer le plat
        $platId = Plat::createPlat([
            'titre_plat' => $titrePlat,
            'description' => $description,
            'type_plat' => $typePlat,
            'photo' => $photo
        ]);

        if ($platId) {
            Session::set('success', "Le plat « $titrePlat » a été créé avec succès.");
        } else {
            Session::set('error', "Une erreur est survenue lors de la création du plat.");
        }

        $this->redirect('/admin/plats');
    }

    /**
     * Affiche le formulaire d'édition d'un plat
     */
    public function edit()
    {
        $platId = (int)($_GET['id'] ?? 0);
        $plat = Plat::findPlatById($platId);

        if (!$plat) {
            $this->setFlash('error', 'Plat introuvable.');
            $this->redirect('/admin/plats');
            return;
        }

        $this->render('admin/plats/edit', [
            'title' => 'Modifier un plat',
            'plat' => $plat,
            'typesPlat' => Plat::getTypesPlat()
        ]);
    }

    /**
     * Met à jour un plat
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/plats');
            return;
        }

        $platId = (int)($_POST['plat_id'] ?? 0);
        $plat = Plat::findPlatById($platId);

        if (!$plat) {
            Session::set('error', 'Plat introuvable.');
            $this->redirect('/admin/plats');
            return;
        }

        // Validation
        $errors = [];
        $titrePlat = trim($_POST['titre_plat'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $typePlat = $_POST['type_plat'] ?? 'Plat';
        $photo = trim($_POST['photo'] ?? '');

        if (empty($titrePlat)) {
            $errors[] = "Le titre du plat est obligatoire.";
        }

        if (!in_array($typePlat, Plat::getTypesPlat())) {
            $errors[] = "Type de plat invalide.";
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            $this->redirect('/admin/plats/edit?id=' . $platId);
            return;
        }
        $success = Plat::updatePlat($platId, [
            'titre_plat' => $titrePlat,
            'description' => $description,
            'type_plat' => $typePlat,
            'photo' => $photo
        ]);

        if ($success) {
            Session::set('success', "Le plat « $titrePlat » a été modifié avec succès.");
        } else {
            Session::set('error', "Une erreur est survenue lors de la modification.");
        }

        $this->redirect('/admin/plats');
    }

    /**
     * Supprime un plat
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/plats');
            return;
        }

        $platId = (int)($_POST['plat_id'] ?? 0);
        $plat = Plat::findPlatById($platId);

        if (!$plat) {
            Session::set('error', 'Plat introuvable.');
            $this->redirect('/admin/plats');
            return;
        }

        $success = Plat::deletePlat($platId);

        if ($success) {
            Session::set('success', "Le plat « {$plat['titre_plat']} » a été supprimé.");
        } else {
            Session::set('error', "Une erreur est survenue lors de la suppression.");
        }

        $this->redirect('/admin/plats');
    }
}
