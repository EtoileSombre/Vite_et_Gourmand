<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Factory\ServiceFactory;
use App\Services\AdminService;
use App\Services\Exceptions\AdminException;

class DashboardController extends Controller
{
    private AdminService $adminService;

    public function __construct()
    {
        $this->adminService = ServiceFactory::getInstance()->createAdminService();
    }

    /**
     * Garde d'accès admin.
     */
    private function requireAdmin(): bool
    {
        if (Session::get('user_role') !== 'administrateur') {
            $this->redirect('/');
            return false;
        }
        return true;
    }

    public function dashboard()
    {
        if (!$this->requireAdmin()) return;

        $stats = $this->adminService->getDashboardStats();
        $this->render('admin/dashboard', $stats);
    }

    public function users()
    {
        if (!$this->requireAdmin()) return;

        $this->render('admin/users', [
            'users' => $this->adminService->listStaffUsers(),
        ]);
    }

    public function createEmploye(Request $request)
    {
        if (!$this->requireAdmin()) return;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/utilisateurs');
            return;
        }
        if (!csrf_verify()) {
            Session::set('flash_error', 'Erreur de sécurité.');
            $this->redirect('/admin/utilisateurs');
            return;
        }

        try {
            $this->adminService->createEmploye(
                [
                    'email'            => $_POST['email'] ?? '',
                    'prenom'           => $_POST['prenom'] ?? '',
                    'nom'              => $_POST['nom'] ?? '',
                    'password'         => $_POST['password'] ?? '',
                    'password_confirm' => $_POST['password_confirm'] ?? '',
                ],
                (int) Session::get('user_id'),
                (string) Session::get('user_email')
            );
            Session::set('flash_success', "Compte employé créé avec succès ! Email de notification envoyé.");
        } catch (AdminException $e) {
            Session::set('flash_error', $e->getMessage());
        }

        $this->redirect('/admin/utilisateurs');
    }

    public function deactivateUser(Request $request)
    {
        if (!$this->requireAdmin()) return;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/utilisateurs');
            return;
        }
        if (!csrf_verify()) {
            Session::set('flash_error', 'Erreur de sécurité.');
            $this->redirect('/admin/utilisateurs');
            return;
        }

        $utilisateurId = $_POST['utilisateur_id'] ?? null;
        if (!$utilisateurId) {
            Session::set('flash_error', "Utilisateur introuvable");
            $this->redirect('/admin/utilisateurs');
            return;
        }

        try {
            $this->adminService->deactivateUser(
                (int) $utilisateurId,
                (int) Session::get('user_id'),
                (string) Session::get('user_email')
            );
            Session::set('flash_success', "Compte employé désactivé avec succès");
        } catch (AdminException $e) {
            Session::set('flash_error', $e->getMessage());
        }

        $this->redirect('/admin/utilisateurs');
    }

    public function activateUser(Request $request)
    {
        if (!$this->requireAdmin()) return;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/utilisateurs');
            return;
        }
        if (!csrf_verify()) {
            Session::set('flash_error', 'Erreur de sécurité.');
            $this->redirect('/admin/utilisateurs');
            return;
        }

        $utilisateurId = $_POST['utilisateur_id'] ?? null;
        if (!$utilisateurId) {
            Session::set('flash_error', "Utilisateur introuvable");
            $this->redirect('/admin/utilisateurs');
            return;
        }

        try {
            $this->adminService->activateUser(
                (int) $utilisateurId,
                (int) Session::get('user_id'),
                (string) Session::get('user_email')
            );
            Session::set('flash_success', "Compte employé réactivé avec succès");
        } catch (AdminException $e) {
            Session::set('flash_error', $e->getMessage());
        }

        $this->redirect('/admin/utilisateurs');
    }

    public function commandes()
    {
        if (!$this->requireAdmin()) return;

        $this->render('admin/commandes', [
            'commandes' => $this->adminService->listAllCommandes(),
            'statuts'   => \App\Repository\CommandeRepository::STATUTS,
        ]);
    }
}
