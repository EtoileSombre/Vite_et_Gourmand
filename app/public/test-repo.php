<?php
// Test rapide pour voir l'erreur
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../autoload.php';

echo "Autoload OK<br>";

try {
    $factory = \App\Factory\RepositoryFactory::getInstance();
    echo "Factory OK<br>";
    
    $userRepo = $factory->createUserRepository();
    echo "UserRepository OK<br>";
    
    $menuRepo = $factory->createMenuRepository();
    echo "MenuRepository OK<br>";
    
    echo "Tous les repositories fonctionnent !";
} catch (\Throwable $e) {
    echo "ERREUR : " . $e->getMessage() . "<br>";
    echo "Fichier : " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
