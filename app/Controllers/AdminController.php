<?php

namespace App\Controllers;

use App\Core\Controller;
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
        $users = $userModel->findAll();

        $this->render('admin/users', ['users' => $users]);
    }

    /**
     * Gestion des commandes
     */
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
