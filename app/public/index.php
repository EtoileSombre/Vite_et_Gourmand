<?php
require_once __DIR__ . '/../autoload.php';

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Europe/Paris');

// Définir l'encodage des pages (pour afficher correctement les accents)
header('Content-Type: text/html; charset=UTF-8');

session_start();

$router = require_once __DIR__ . '/../routes.php';

// Analyser l'URL demandée et exécuter le contrôleur correspondant
$router->dispatch();
