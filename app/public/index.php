<?php
require_once __DIR__ . '/../autoload.php';

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../storage/logs/php_errors.log');
error_reporting(E_ALL);

date_default_timezone_set('Europe/Paris');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,         // true en production HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

$timeout = 7200;

if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['employé', 'administrateur'])) {
    $timeout = 14400;
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['LAST_ACTIVITY'] = time();

$router = require_once __DIR__ . '/../routes.php';

// Analyser l'URL demandée et exécuter le contrôleur correspondant
$router->dispatch();
