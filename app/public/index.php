<?php
require_once __DIR__ . '/../autoload.php';

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../storage/logs/php_errors.log');
error_reporting(E_ALL);

date_default_timezone_set('Europe/Paris');


session_start();

$router = require_once __DIR__ . '/../routes.php';

// Analyser l'URL demandée et exécuter le contrôleur correspondant
$router->dispatch();
