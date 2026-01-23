<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $mongoUri = getenv('MONGO_URI') ?: 'mongodb://vgroot:vgrootpass@mongo:27017';
    $mongoDb = getenv('MONGO_DATABASE') ?: 'vg';
    
    $mongoClient = new MongoDB\Client(
        $mongoUri,
        [],
        [
            'typeMap' => [
                'root' => 'array',
                'document' => 'array',
                'array' => 'array'
            ]
        ]
    );

    $mongodb = $mongoClient->$mongoDb;

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
