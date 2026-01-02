<?php
/** Fichier de routes / Définit toutes les routes de l'application */

use App\Core\Router;

$router = new Router();

// ROUTES PUBLIQUES

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

// ROUTES AUTHENTIFICATION

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

// ROUTES PROTÉGÉES (UTILISATEUR CONNECTÉ)

// Commandes (nécessite authentification)
$router->get('/mes-commandes', 'App\Controllers\CommandeController', 'index');
$router->get('/commande/nouvelle', 'App\Controllers\CommandeController', 'create');
$router->post('/commande/nouvelle', 'App\Controllers\CommandeController', 'store');
$router->get('/commande/modifier', 'App\Controllers\CommandeController', 'edit');
$router->post('/commande/modifier', 'App\Controllers\CommandeController', 'update');
$router->get('/commande/annuler', 'App\Controllers\CommandeController', 'cancel');

// Profil utilisateur
$router->get('/profil', 'App\Controllers\ProfilController', 'index');
$router->post('/profil', 'App\Controllers\ProfilController', 'index');
// ROUTES ADMIN (NÉCESSITE RÔLE ADMIN)

// Dashboard admin
$router->get('/admin', 'App\Controllers\AdminController', 'dashboard');

// Statistiques MongoDB (admin)
$router->get('/admin/stats', 'App\Controllers\StatsController', 'index');

// Gestion des messages de contact (admin)
$router->get('/admin/contacts', 'App\Controllers\AdminContactController', 'index');
$router->post('/admin/contacts/change-status', 'App\Controllers\AdminContactController', 'changeStatus');
$router->post('/admin/contacts/delete', 'App\Controllers\AdminContactController', 'delete');

// Gestion des utilisateurs (admin)
$router->get('/admin/utilisateurs', 'App\Controllers\AdminController', 'users');
$router->post('/admin/utilisateurs/creer-employe', 'App\Controllers\AdminController', 'createEmploye');
$router->post('/admin/utilisateurs/desactiver', 'App\Controllers\AdminController', 'deactivateUser');
$router->post('/admin/utilisateurs/activer', 'App\Controllers\AdminController', 'activateUser');

// Gestion des commandes (admin)
$router->get('/admin/commandes', 'App\Controllers\AdminController', 'commandes');

// Gestion des horaires (admin)
$router->get('/admin/horaires', 'App\Controllers\HoraireController', 'index');
$router->post('/admin/horaires/update', 'App\Controllers\HoraireController', 'update');

// ROUTES EMPLOYÉ (NÉCESSITE RÔLE EMPLOYÉ OU ADMIN)

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
// ROUTES ADMIN/EMPLOYÉ - GESTION MENUS

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

// ROUTES ADMIN/EMPLOYÉ - GESTION PLATS

// Liste des plats (admin/employé)
$router->get('/admin/plats', 'App\Controllers\PlatController', 'index');

// Créer un plat
$router->get('/admin/plats/create', 'App\Controllers\PlatController', 'create');
$router->post('/admin/plats/store', 'App\Controllers\PlatController', 'store');

// Modifier un plat
$router->get('/admin/plats/edit', 'App\Controllers\PlatController', 'edit');
$router->post('/admin/plats/update', 'App\Controllers\PlatController', 'update');

// Supprimer un plat
$router->post('/admin/plats/delete', 'App\Controllers\PlatController', 'delete');

return $router;
