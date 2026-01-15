<?php
/** Fichier de routes / Définit toutes les routes de l'application */

use App\Core\Router;

$router = new Router();

// ROUTES PUBLIQUES

// Page d'accueil
$router->get('/', 'App\Controllers\Public\HomeController', 'index');

// Menus
$router->get('/menus', 'App\Controllers\Public\MenuController', 'index');
$router->get('/menu', 'App\Controllers\Public\MenuController', 'show');

// API Menus (filtrage asynchrone)
$router->get('/api/menus/filter', 'App\Controllers\Public\MenuController', 'apiFilter');

// Contact
$router->get('/contact', 'App\Controllers\Public\ContactController', 'index');
$router->post('/contact', 'App\Controllers\Public\ContactController', 'index');

// Pages légales
$router->get('/mentions-legales', 'App\Controllers\Public\LegalController', 'mentionsLegales');
$router->get('/cgv', 'App\Controllers\Public\LegalController', 'cgv');

// Avis
$router->get('/donner-avis', 'App\\Controllers\\Utilisateur\\AvisController', 'create');
$router->post('/avis', 'App\\Controllers\\Utilisateur\\AvisController', 'store');

// ROUTES AUTHENTIFICATION

// Connexion
$router->get('/login', 'App\Controllers\Auth\AuthController', 'login');
$router->post('/login', 'App\Controllers\Auth\AuthController', 'login');

// Inscription
$router->get('/register', 'App\Controllers\Auth\AuthController', 'register');
$router->post('/register', 'App\Controllers\Auth\AuthController', 'register');

// Déconnexion
$router->get('/logout', 'App\Controllers\Auth\AuthController', 'logout');

// Mot de passe oublié
$router->get('/forgot-password', 'App\Controllers\Auth\AuthController', 'forgotPassword');
$router->post('/forgot-password', 'App\Controllers\Auth\AuthController', 'forgotPassword');

// Réinitialisation du mot de passe
$router->get('/reset-password', 'App\Controllers\Auth\AuthController', 'resetPassword');
$router->post('/reset-password', 'App\Controllers\Auth\AuthController', 'resetPassword');

// ROUTES PROTÉGÉES (UTILISATEUR CONNECTÉ)

// Commandes (nécessite authentification)
$router->get('/mes-commandes', 'App\\Controllers\\Utilisateur\\CommandeController', 'index');
$router->get('/commande/details', 'App\\Controllers\\Utilisateur\\CommandeController', 'show');
$router->get('/commande/nouvelle', 'App\\Controllers\\Utilisateur\\CommandeController', 'create');
$router->post('/commande/nouvelle', 'App\\Controllers\\Utilisateur\\CommandeController', 'store');
$router->get('/commande/modifier', 'App\\Controllers\\Utilisateur\\CommandeController', 'edit');
$router->post('/commande/modifier', 'App\\Controllers\\Utilisateur\\CommandeController', 'update');
$router->get('/commande/annuler', 'App\\Controllers\\Utilisateur\\CommandeController', 'cancel');

// Avis (nécessite authentification)
$router->get('/avis/create', 'App\\Controllers\\Utilisateur\\AvisController', 'create');
$router->post('/avis/create', 'App\\Controllers\\Utilisateur\\AvisController', 'store');

// Profil utilisateur
$router->get('/profil', 'App\\Controllers\\Utilisateur\\ProfilController', 'index');
$router->post('/profil', 'App\\Controllers\\Utilisateur\\ProfilController', 'index');
// ROUTES ADMIN (NÉCESSITE RÔLE ADMIN)

// Dashboard admin
$router->get('/admin', 'App\Controllers\Admin\DashboardController', 'dashboard');

// Statistiques MongoDB (admin)
$router->get('/admin/stats', 'App\Controllers\Admin\StatsController', 'index');

// Gestion des messages de contact (admin)
$router->get('/admin/contacts', 'App\Controllers\Admin\ContactController', 'index');
$router->post('/admin/contacts/change-status', 'App\Controllers\Admin\ContactController', 'changeStatus');
$router->post('/admin/contacts/delete', 'App\Controllers\Admin\ContactController', 'delete');

// Gestion des utilisateurs (admin)
$router->get('/admin/utilisateurs', 'App\Controllers\Admin\DashboardController', 'users');
$router->post('/admin/utilisateurs/creer-employe', 'App\Controllers\Admin\DashboardController', 'createEmploye');
$router->post('/admin/utilisateurs/desactiver', 'App\Controllers\Admin\DashboardController', 'deactivateUser');
$router->post('/admin/utilisateurs/activer', 'App\Controllers\Admin\DashboardController', 'activateUser');

// Gestion des commandes (admin)
$router->get('/admin/commandes', 'App\Controllers\Admin\DashboardController', 'commandes');

// Gestion des horaires (admin)
$router->get('/admin/horaires', 'App\Controllers\Admin\HoraireController', 'index');
$router->post('/admin/horaires/update', 'App\Controllers\Admin\HoraireController', 'update');

// ROUTES EMPLOYÉ (NÉCESSITE RÔLE EMPLOYÉ OU ADMIN)

// Dashboard employé
$router->get('/employe', 'App\Controllers\Employe\DashboardController', 'index');

// Gestion des commandes (employé)
$router->get('/employe/commandes', 'App\Controllers\Employe\CommandeController', 'index');
$router->get('/employe/commandes/change-status', 'App\Controllers\Employe\CommandeController', 'changeStatus');
$router->post('/employe/commandes/change-status', 'App\Controllers\Employe\CommandeController', 'changeStatus');
$router->get('/employe/commandes/view', 'App\Controllers\Employe\CommandeController', 'view');
$router->post('/employe/commandes/edit', 'App\Controllers\Employe\CommandeController', 'edit');

// Modération des avis (employé)
$router->get('/employe/avis', 'App\Controllers\Employe\AvisController', 'index');
$router->post('/employe/avis/approve', 'App\Controllers\Employe\AvisController', 'approve');
$router->post('/employe/avis/reject', 'App\Controllers\Employe\AvisController', 'reject');
// ROUTES ADMIN/EMPLOYÉ - GESTION MENUS

// Liste des menus (admin/employé)
$router->get('/admin/menus', 'App\Controllers\Admin\MenuController', 'index');

// Créer un menu
$router->get('/admin/menus/create', 'App\Controllers\Admin\MenuController', 'create');
$router->post('/admin/menus/store', 'App\Controllers\Admin\MenuController', 'store');

// Modifier un menu
$router->get('/admin/menus/edit', 'App\Controllers\Admin\MenuController', 'edit');
$router->post('/admin/menus/update', 'App\Controllers\Admin\MenuController', 'update');

// Désactiver/Réactiver un menu
$router->post('/admin/menus/delete', 'App\Controllers\Admin\MenuController', 'delete');
$router->post('/admin/menus/activate', 'App\Controllers\Admin\MenuController', 'activate');

// ROUTES ADMIN/EMPLOYÉ - GESTION PLATS

// Liste des plats (admin/employé)
$router->get('/admin/plats', 'App\Controllers\Admin\PlatController', 'index');

// Créer un plat
$router->get('/admin/plats/create', 'App\Controllers\Admin\PlatController', 'create');
$router->post('/admin/plats/store', 'App\Controllers\Admin\PlatController', 'store');

// Modifier un plat
$router->get('/admin/plats/edit', 'App\Controllers\Admin\PlatController', 'edit');
$router->post('/admin/plats/update', 'App\Controllers\Admin\PlatController', 'update');

// Supprimer un plat
$router->post('/admin/plats/delete', 'App\Controllers\Admin\PlatController', 'delete');

return $router;
