<?php
/**
 * Fichier de routes
 * Définit toutes les routes de l'application
 */

use App\Core\Router;

$router = new Router();

// ==========================================
// ROUTES PUBLIQUES
// ==========================================

// Page d'accueil
$router->get('/', 'App\Controllers\HomeController', 'index');

// Menus
$router->get('/menus', 'App\Controllers\MenuController', 'index');
$router->get('/menu', 'App\Controllers\MenuController', 'show');

// Contact
$router->get('/contact', 'App\Controllers\ContactController', 'index');
$router->post('/contact', 'App\Controllers\ContactController', 'index');

// Pages légales
$router->get('/mentions-legales', 'App\Controllers\LegalController', 'mentionsLegales');
$router->get('/cgv', 'App\Controllers\LegalController', 'cgv');

// Avis
$router->get('/donner-avis', 'App\Controllers\AvisController', 'create');
$router->post('/avis', 'App\Controllers\AvisController', 'store');

// ==========================================
// ROUTES AUTHENTIFICATION
// ==========================================

// Connexion
$router->get('/login', 'App\Controllers\AuthController', 'login');
$router->post('/login', 'App\Controllers\AuthController', 'login');

// Inscription
$router->get('/register', 'App\Controllers\AuthController', 'register');
$router->post('/register', 'App\Controllers\AuthController', 'register');

// Déconnexion
$router->get('/logout', 'App\Controllers\AuthController', 'logout');

// Mot de passe oublié
$router->get('/forgot-password', 'App\Controllers\AuthController', 'forgotPassword');
$router->post('/forgot-password', 'App\Controllers\AuthController', 'forgotPassword');

// Réinitialisation du mot de passe
$router->get('/reset-password', 'App\Controllers\AuthController', 'resetPassword');
$router->post('/reset-password', 'App\Controllers\AuthController', 'resetPassword');

// ==========================================
// ROUTES PROTÉGÉES (CLIENT CONNECTÉ)
// ==========================================

// Commandes (nécessite authentification)
$router->get('/mes-commandes', 'App\Controllers\CommandeController', 'index');
$router->get('/commande/nouvelle', 'App\Controllers\CommandeController', 'create');
$router->post('/commande/nouvelle', 'App\Controllers\CommandeController', 'store');
$router->get('/commande/modifier', 'App\Controllers\CommandeController', 'edit');
$router->post('/commande/modifier', 'App\Controllers\CommandeController', 'update');
$router->get('/commande/annuler', 'App\Controllers\CommandeController', 'cancel');
// ]);

// Profil utilisateur
$router->get('/profil', 'App\Controllers\ProfilController', 'index');
$router->post('/profil', 'App\Controllers\ProfilController', 'index');

// Avis
// $router->post('/avis', 'App\Controllers\AvisController', 'store', [
//     'App\Middlewares\AuthMiddleware'
// ]);

// ==========================================
// ROUTES ADMIN (NÉCESSITE RÔLE ADMIN)
// ==========================================

// Dashboard admin
$router->get('/admin', 'App\Controllers\AdminController', 'dashboard');

// Statistiques MongoDB (admin)
$router->get('/admin/stats', 'App\Controllers\StatsController', 'index');

// Gestion des utilisateurs (admin)
$router->get('/admin/utilisateurs', 'App\Controllers\AdminController', 'users');

// Gestion des commandes (admin)
$router->get('/admin/commandes', 'App\Controllers\AdminController', 'commandes');

// ==========================================
// ROUTES EMPLOYÉ (NÉCESSITE RÔLE EMPLOYÉ OU ADMIN)
// ==========================================

// Dashboard employé
$router->get('/employe', 'App\Controllers\EmployeController', 'index');

// Gestion des commandes (employé)
$router->get('/employe/commandes', 'App\Controllers\EmployeCommandeController', 'index');
$router->get('/employe/commandes/change-status', 'App\Controllers\EmployeCommandeController', 'changeStatus');
$router->post('/employe/commandes/change-status', 'App\Controllers\EmployeCommandeController', 'changeStatus');
$router->get('/employe/commandes/view', 'App\Controllers\EmployeCommandeController', 'view');

// Modération des avis (employé)
$router->get('/employe/avis', 'App\Controllers\EmployeAvisController', 'index');
$router->post('/employe/avis/approve', 'App\Controllers\EmployeAvisController', 'approve');
$router->post('/employe/avis/reject', 'App\Controllers\EmployeAvisController', 'reject');

// ==========================================
// ROUTES ADMIN/EMPLOYÉ - GESTION MENUS
// ==========================================

// Liste des menus (admin/employé)
$router->get('/admin/menus', 'App\Controllers\MenuAdminController', 'index');

// Créer un menu
$router->get('/admin/menus/create', 'App\Controllers\MenuAdminController', 'create');
$router->post('/admin/menus/store', 'App\Controllers\MenuAdminController', 'store');

// Modifier un menu
$router->get('/admin/menus/edit', 'App\Controllers\MenuAdminController', 'edit');
$router->post('/admin/menus/update', 'App\Controllers\MenuAdminController', 'update');

// Désactiver/Réactiver un menu
$router->post('/admin/menus/delete', 'App\Controllers\MenuAdminController', 'delete');
$router->post('/admin/menus/activate', 'App\Controllers\MenuAdminController', 'activate');

// $router->get('/admin/menu/create', 'App\Controllers\Admin\MenuAdminController', 'create', [
//     'App\Middlewares\AuthMiddleware',
//     'App\Middlewares\AdminMiddleware'
// ]);

// $router->post('/admin/menu', 'App\Controllers\Admin\MenuAdminController', 'store', [
//     'App\Middlewares\AuthMiddleware',
//     'App\Middlewares\AdminMiddleware'
// ]);

// ==========================================
// API REST (optionnel)
// ==========================================

// $router->get('/api/menus', 'App\Controllers\Api\MenuApiController', 'index');
// $router->get('/api/menu', 'App\Controllers\Api\MenuApiController', 'show');

return $router;
