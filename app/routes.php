<?php
/**
 * Fichier de routes
 * Définit toutes les routes de l'application
 */

use App\Core\Router;

// Créer une instance du routeur
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
// $router->get('/contact', 'App\Controllers\ContactController', 'index');
// $router->post('/contact', 'App\Controllers\ContactController', 'send');

// ==========================================
// ROUTES AUTHENTIFICATION
// ==========================================

// Connexion
// $router->get('/login', 'App\Controllers\Auth\LoginController', 'showForm');
// $router->post('/login', 'App\Controllers\Auth\LoginController', 'login');

// Inscription
// $router->get('/register', 'App\Controllers\Auth\RegisterController', 'showForm');
// $router->post('/register', 'App\Controllers\Auth\RegisterController', 'register');

// Déconnexion
// $router->get('/logout', 'App\Controllers\Auth\LoginController', 'logout');

// ==========================================
// ROUTES PROTÉGÉES (CLIENT CONNECTÉ)
// ==========================================

// Commandes (nécessite authentification)
// $router->get('/mes-commandes', 'App\Controllers\CommandeController', 'index', [
//     'App\Middlewares\AuthMiddleware'
// ]);

// $router->get('/commande', 'App\Controllers\CommandeController', 'show', [
//     'App\Middlewares\AuthMiddleware'
// ]);

// $router->get('/commander', 'App\Controllers\CommandeController', 'create', [
//     'App\Middlewares\AuthMiddleware'
// ]);

// $router->post('/commander', 'App\Controllers\CommandeController', 'store', [
//     'App\Middlewares\AuthMiddleware'
// ]);

// Avis
// $router->post('/avis', 'App\Controllers\AvisController', 'store', [
//     'App\Middlewares\AuthMiddleware'
// ]);

// ==========================================
// ROUTES ADMIN (NÉCESSITE RÔLE ADMIN)
// ==========================================

// Dashboard admin
// $router->get('/admin', 'App\Controllers\Admin\DashboardController', 'index', [
//     'App\Middlewares\AuthMiddleware',
//     'App\Middlewares\AdminMiddleware'
// ]);

// Gestion des menus (admin)
// $router->get('/admin/menus', 'App\Controllers\Admin\MenuAdminController', 'index', [
//     'App\Middlewares\AuthMiddleware',
//     'App\Middlewares\AdminMiddleware'
// ]);

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
