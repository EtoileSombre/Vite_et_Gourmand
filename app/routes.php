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
$router->get('/contact', 'App\Controllers\ContactController', 'index');
$router->post('/contact', 'App\Controllers\ContactController', 'index');

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

// Gestion des utilisateurs (admin)
$router->get('/admin/utilisateurs', 'App\Controllers\AdminController', 'users');

// Gestion des commandes (admin)
$router->get('/admin/commandes', 'App\Controllers\AdminController', 'commandes');

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
