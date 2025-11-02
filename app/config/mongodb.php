<?php
/**
 * Configuration de la connexion MongoDB
 * Utilisé pour les statistiques, logs et cache
 */

// Charger l'autoloader Composer
require_once __DIR__ . '/../vendor/autoload.php';

try {
    // Connexion MongoDB avec les bons identifiants
    $mongoClient = new MongoDB\Client(
        "mongodb://vgroot:vgrootpass@mongo:27017",
        [],
        [
            'typeMap' => [
                'root' => 'array',
                'document' => 'array',
                'array' => 'array'
            ]
        ]
    );

    // Sélection de la base de données
    $mongodb = $mongoClient->vg;

    // Collections principales
    $mongoCollections = [
        'menu_views' => $mongodb->menu_views,           // Statistiques vues menus
        'user_activity' => $mongodb->user_activity,     // Logs d'activité
        'commande_stats' => $mongodb->commande_stats,   // Statistiques commandes
        'avis_analytics' => $mongodb->avis_analytics    // Analytics des avis
    ];

} catch (Exception $e) {
    error_log("Erreur connexion MongoDB : " . $e->getMessage());
    $mongoClient = null;
    $mongodb = null;
    $mongoCollections = [];
}
