<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Commande;
use App\Models\Menu;
use App\Core\Session;

class AdminController extends Controller
{
    /**
     * Dashboard admin avec statistiques
     */
    public function dashboard()
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            $this->redirect('/');
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
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            $this->redirect('/');
        }

        $userModel = new User();
        $users = $userModel->findAll();

        $this->render('admin/users', ['users' => $users]);
    }

    /**
     * Gestion des commandes
     */
    public function commandes()
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            $this->redirect('/');
        }

        $commandeModel = new Commande();
        $commandes = $commandeModel->findAll();

        $this->render('admin/commandes', ['commandes' => $commandes]);
    }
}
