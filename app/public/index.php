<?php
// Point d'entrée unique (Front Controller)

ob_start();

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Europe/Paris');

// Charger l'autoloader PSR-4
require_once __DIR__ . '/../autoload.php';

// Charger Composer autoloader (pour PHPMailer, MongoDB, etc.)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Charger les routes
$router = require_once __DIR__ . '/../routes.php';

// Dispatcher la requête
$router->dispatch();

// Envoyer le buffer
ob_end_flush();
