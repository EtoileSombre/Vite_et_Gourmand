<?php
/*Script de création des index MongoDB pour les statistiques*/

require_once __DIR__ . '/../vendor/autoload.php';

echo "🔧 Création des index MongoDB pour Vite & Gourmand\n";
echo str_repeat("=", 60) . "\n\n";

try {
    // Récupération de l'URI depuis les variables d'environnement
    $mongoUri = getenv('MONGO_URI');
    if (!$mongoUri) {
        throw new RuntimeException('Variable d\'environnement MONGO_URI non définie');
    }
    $mongoDbName = getenv('MONGO_DATABASE') ?: 'vg';
    
    $client = new MongoDB\Client($mongoUri);
    $db = $client->$mongoDbName;
    
    echo "Connexion MongoDB réussie\n\n";
    
    // Collection : commande_stats

    echo "Collection : commande_stats\n";
    $commandeStats = $db->commande_stats;
    
    // Index composé pour filtrer efficacement par menu + timestamp
    $commandeStats->createIndex(
        ['menu_id' => 1, 'timestamp' => 1],
        ['name' => 'idx_menu_timestamp', 'background' => true]
    );
    echo "  ✓ Index composé créé : menu_id + timestamp\n";
    
    // Index sur timestamp pour tri chronologique
    $commandeStats->createIndex(
        ['timestamp' => 1],
        ['name' => 'idx_timestamp', 'background' => true]
    );
    echo "  ✓ Index créé : timestamp\n\n";
    
    // Collection : menu_views (avec TTL 90 jours)

    echo "Collection : menu_views\n";
    $menuViews = $db->menu_views;
    
    // Index composé pour filtrer par menu + date
    $menuViews->createIndex(
        ['menu_id' => 1, 'timestamp' => 1],
        ['name' => 'idx_menu_timestamp', 'background' => true]
    );
    echo "  ✓ Index composé créé : menu_id + timestamp\n";
    
    // Index TTL pour suppression automatique après 90 jours (RGPD)
    $menuViews->createIndex(
        ['timestamp' => 1],
        [
            'name' => 'idx_ttl_90days',
            'expireAfterSeconds' => 7776000, // 90 jours
            'background' => true
        ]
    );
    echo "  ✓ Index TTL créé : 90 jours (RGPD)\n\n";
    
    // Collection : user_activity (avec TTL 90 jours)

    echo "Collection : user_activity\n";
    $userActivity = $db->user_activity;
    
    // Index sur utilisateur_id pour recherches par utilisateur
    $userActivity->createIndex(
        ['utilisateur_id' => 1, 'timestamp' => 1],
        ['name' => 'idx_utilisateur_timestamp', 'background' => true]
    );
    echo "  ✓ Index composé créé : utilisateur_id + timestamp\n";
    
    // Index TTL pour suppression automatique après 90 jours (RGPD)
    $userActivity->createIndex(
        ['timestamp' => 1],
        [
            'name' => 'idx_ttl_90days',
            'expireAfterSeconds' => 7776000, // 90 jours
            'background' => true
        ]
    );
    echo "  ✓ Index TTL créé : 90 jours (RGPD)\n\n";
    
    // Collection : avis_analytics

    echo "Collection : avis_analytics\n";
    $avisAnalytics = $db->avis_analytics;
    
    // Index sur timestamp pour tri chronologique
    $avisAnalytics->createIndex(
        ['timestamp' => 1],
        ['name' => 'idx_timestamp', 'background' => true]
    );
    echo "  ✓ Index créé : timestamp\n\n";
    
    // Vérification des index créés

    echo str_repeat("=", 60) . "\n";
    echo "Vérification des index créés\n\n";
    
    $collections = ['commande_stats', 'menu_views', 'user_activity', 'avis_analytics'];
    foreach ($collections as $collName) {
        echo "Collection : $collName\n";
        $indexes = $db->$collName->listIndexes();
        foreach ($indexes as $index) {
            $name = $index['name'];
            $keys = json_encode($index['key']);
            $ttl = isset($index['expireAfterSeconds']) ? " (TTL: " . $index['expireAfterSeconds'] . "s)" : "";
            echo "  - $name : $keys$ttl\n";
        }
        echo "\n";
    }
    
    echo str_repeat("=", 60) . "\n";
    echo "Création des index terminée !\n\n";
    echo "Notes importantes :\n";
    echo "   • Les index composés optimisent les agrégations par menu + timestamp\n";
    echo "   • Les index TTL suppriment automatiquement les données après 90 jours (RGPD)\n";
    echo "   • Les index sont créés en arrière-plan (pas de blocage)\n\n";

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
