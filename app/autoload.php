<?php
/**
 * Autoloader PSR-4
 * Charge automatiquement les classes selon leur namespace
 */

spl_autoload_register(function ($class) {
    // Préfixe du namespace de base
    $prefix = 'App\\';
    
    // Répertoire de base pour le namespace
    $baseDir = __DIR__ . '/';
    
    // Vérifier si la classe utilise le namespace de base
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // Non, passer à l'autoloader suivant
        return;
    }
    
    // Obtenir le nom relatif de la classe
    $relativeClass = substr($class, $len);
    
    // Remplacer le namespace par le chemin de fichier
    // et ajouter .php à la fin
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    // Si le fichier existe, le charger
    if (file_exists($file)) {
        require $file;
    }
});
