<?php
require_once __DIR__ . '/../autoload.php';

// Charger les librairies externes installées via Composer
// (PHPMailer pour les emails, MongoDB pour les statistiques))
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
// Afficher toutes les erreurs PHP (utile en développement)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Définir le fuseau horaire pour les fonctions de date
date_default_timezone_set('Europe/Paris');

// Définir l'encodage des pages (pour afficher correctement les accents)
header('Content-Type: text/html; charset=UTF-8');

// Démarrer la session PHP (pour gérer la connexion utilisateur)
session_start();

// Charger le routeur avec toutes les routes de l'application
$router = require_once __DIR__ . '/../routes.php';

// Analyser l'URL demandée et exécuter le contrôleur correspondant
$router->dispatch();
