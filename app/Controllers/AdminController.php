<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\User;
use App\Models\Commande;
use App\Models\CommandeMenu;
use App\Models\Menu;
use App\Core\Session;

class AdminController extends Controller
{
    /**
     * Dashboard admin avec statistiques
     */
    public function dashboard()
    {
        $userRole = Session::get('user_role');
        if (!$userRole || $userRole !== 'administrateur') {
            $this->redirect('/');
            return;
        }

        // Récupérer les statistiques
        $userModel = new User();
        $commandeModel = new Commande();
        $menuModel = new Menu();

        $totalUsers = count($userModel->findAll());
        $totalCommandes = count($commandeModel->findAll());
        $totalMenus = count($menuModel->findAll());

        // Récupérer les dernières commandes
        $dernieresCommandes = $commandeModel->findAll();
        // Limiter aux 10 dernières
        $dernieresCommandes = array_slice($dernieresCommandes, 0, 10);
        
        // Enrichir avec lignesMenus
        $commandeMenuModel = new CommandeMenu();
        foreach ($dernieresCommandes as &$cmd) {
            $cmd['lignesMenus'] = $commandeMenuModel->findByCommande($cmd['numero_commande']);
            $cmd['totalPersonnes'] = $commandeMenuModel->getTotalPersonnes($cmd['numero_commande']);
            // Afficher le premier menu comme menu_nom
            if (!empty($cmd['lignesMenus'])) {
                $cmd['menu_nom'] = $cmd['lignesMenus'][0]['menu_nom'] ?? 'Menu';
            }
        }

        $this->render('admin/dashboard', [
            'totalUsers' => $totalUsers,
            'totalCommandes' => $totalCommandes,
            'totalMenus' => $totalMenus,
            'dernieresCommandes' => $dernieresCommandes
        ]);
    }

    /**
     * Gestion des utilisateurs
     */
    public function users()
    {
        $userRole = Session::get('user_role');
        if (!$userRole || $userRole !== 'administrateur') {
            $this->redirect('/');
            return;
        }

        $userModel = new User();
        $users = $userModel->findAllWithRole();

        $this->render('admin/users', ['users' => $users]);
    }

    /*Créer un compte employé*/
    public function createEmploye(Request $request)
    {
        $userRole = Session::get('user_role');
        if (!$userRole || $userRole !== 'administrateur') {
            $this->redirect('/');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/utilisateurs');
            return;
        }

        $errors = [];

        // Récupération des données
        $email = trim($_POST['email'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $nom = trim($_POST['nom'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validation
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide";
        }
        if (empty($prenom)) {
            $errors[] = "Le prénom est obligatoire";
        }
        if (empty($nom)) {
            $errors[] = "Le nom est obligatoire";
        }
        if (empty($password) || strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
        }
        if ($password !== $passwordConfirm) {
            $errors[] = "Les mots de passe ne correspondent pas";
        }

        // Vérifier que l'email n'existe pas
        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            $errors[] = "Cet email est déjà utilisé";
        }

        if (!empty($errors)) {
            Session::set('flash_error', implode('<br>', $errors));
            $this->redirect('/admin/utilisateurs');
            return;
        }

        // Créer le compte employé (role_id = 2)
        $userId = $userModel->createEmployeWithPassword($email, $prenom, $nom, $password);

        if ($userId) {
            // Envoyer l'email de notification (SANS le mot de passe)
            require_once __DIR__ . '/../config/mail.php';
            $emailSent = sendEmployeeAccountCreatedEmail($email, $prenom, $nom);

            Session::set('flash_success', "Compte employé créé avec succès ! Email de notification envoyé.");
            
            // Logger dans MongoDB
            require_once __DIR__ . '/../config/mongodb.php';
            $mongoStats = new \App\Config\MongoStats();
            $mongoStats->logUserActivity('create_employee', Session::get('user_id'), [
                'employee_id' => $userId,
                'employee_email' => $email,
                'created_by' => Session::get('user_email')
            ]);
        } else {
            Session::set('flash_error', "Erreur lors de la création du compte");
        }

        $this->redirect('/admin/utilisateurs');
    }

    /*Désactiver un compte employé*/
    public function deactivateUser(Request $request)
    {
        $userRole = Session::get('user_role');
        if (!$userRole || $userRole !== 'administrateur') {
            $this->redirect('/');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/utilisateurs');
            return;
        }

        $utilisateurId = $_POST['utilisateur_id'] ?? null;

        if (!$utilisateurId) {
            Session::set('flash_error', "Utilisateur introuvable");
            $this->redirect('/admin/utilisateurs');
            return;
        }

        $userModel = new User();
        $success = $userModel->deactivate($utilisateurId);

        if ($success) {
            Session::set('flash_success', "Compte employé désactivé avec succès");
            
            // Logger dans MongoDB
            require_once __DIR__ . '/../config/mongodb.php';
            $mongoStats = new \App\Config\MongoStats();
            $mongoStats->logUserActivity('deactivate_employee', Session::get('user_id'), [
                'employee_id' => $utilisateurId,
                'deactivated_by' => Session::get('user_email')
            ]);
        } else {
            Session::set('flash_error', "Erreur lors de la désactivation");
        }

        $this->redirect('/admin/utilisateurs');
    }

    /*Activer un compte employé*/
    public function activateUser(Request $request)
    {
        $userRole = Session::get('user_role');
        if (!$userRole || $userRole !== 'administrateur') {
            $this->redirect('/');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/utilisateurs');
            return;
        }

        $utilisateurId = $_POST['utilisateur_id'] ?? null;

        if (!$utilisateurId) {
            Session::set('flash_error', "Utilisateur introuvable");
            $this->redirect('/admin/utilisateurs');
            return;
        }

        $userModel = new User();
        $success = $userModel->activate($utilisateurId);

        if ($success) {
            Session::set('flash_success', "Compte employé réactivé avec succès");
            
            // Logger dans MongoDB
            require_once __DIR__ . '/../config/mongodb.php';
            $mongoStats = new \App\Config\MongoStats();
            $mongoStats->logUserActivity('activate_employee', Session::get('user_id'), [
                'employee_id' => $utilisateurId,
                'activated_by' => Session::get('user_email')
            ]);
        } else {
            Session::set('flash_error', "Erreur lors de l'activation");
        }

        $this->redirect('/admin/utilisateurs');
    }

    /*Gestion des commandes*/
    public function commandes()
    {
        $userRole = Session::get('user_role');
        if (!$userRole || $userRole !== 'administrateur') {
            $this->redirect('/');
            return;
        }

        $commandeModel = new Commande();
        $commandes = $commandeModel->findAll();

        $this->render('admin/commandes', ['commandes' => $commandes]);
    }
}
