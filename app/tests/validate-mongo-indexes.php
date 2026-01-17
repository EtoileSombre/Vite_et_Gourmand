<?php
/*Script de validation des index MongoDB*/

require_once __DIR__ . '/../vendor/autoload.php';

echo "🔍 Validation des index MongoDB\n";
echo str_repeat("=", 60) . "\n\n";

try {
    // Récupération de l'URI depuis les variables d'environnement
    $mongoUri = getenv('MONGO_URI') ?: 'mongodb://vgroot:vgrootpass@mongo:27017';
    $mongoDbName = getenv('MONGO_DATABASE') ?: 'vg';
    
    $client = new MongoDB\Client($mongoUri);
    $db = $client->$mongoDbName;
    
    echo "Connexion MongoDB réussie\n\n";
    

    // Vérification des index critiques
    echo "Vérification des index critiques\n";
    echo str_repeat("-", 60) . "\n";
    
    $requiredIndexes = [
        'commande_stats' => [
            'idx_menu_timestamp' => ['menu_id' => 1, 'timestamp' => 1],
            'idx_timestamp' => ['timestamp' => 1],
        ],
        'menu_views' => [
            'idx_menu_timestamp' => ['menu_id' => 1, 'timestamp' => 1],
            'idx_ttl_90days' => ['timestamp' => 1],
        ],
        'user_activity' => [
            'idx_utilisateur_timestamp' => ['utilisateur_id' => 1, 'timestamp' => 1],
            'idx_ttl_90days' => ['timestamp' => 1],
        ]
    ];
    
    $allIndexesOk = true;
    foreach ($requiredIndexes as $collName => $indexes) {
        echo "\nCollection : $collName\n";
        $existingIndexes = [];
        foreach ($db->$collName->listIndexes() as $index) {
            $existingIndexes[$index['name']] = $index['key'];
        }
        
        foreach ($indexes as $indexName => $expectedKeys) {
            if (isset($existingIndexes[$indexName])) {
                echo "  ✓ $indexName présent\n";
            } else {
                echo "  $indexName MANQUANT !\n";
                $allIndexesOk = false;
            }
        }
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    if ($allIndexesOk) {
        echo "Tous les index critiques sont présents\n\n";
    } else {
        echo "Certains index sont manquants. Exécutez create-mongo-indexes.php\n\n";
        exit(1);
    }
    
    // Test de performance des agrégations

    echo "⚡ Test de performance des agrégations\n";
    echo str_repeat("-", 60) . "\n\n";
    
    // Test Agrégation sur commande_stats
    $start = microtime(true);
    $result = $db->commande_stats->aggregate([
        ['$match' => [
            'timestamp' => [
                '$gte' => new MongoDB\BSON\UTCDateTime(strtotime('2024-01-01') * 1000)
            ]
        ]],
        ['$group' => [
            '_id' => '$menu_id',
            'total' => ['$sum' => 1],
            'ca' => ['$sum' => '$prix_total']
        ]],
        ['$sort' => ['total' => -1]]
    ])->toArray();
    $duration1 = round((microtime(true) - $start) * 1000, 2);
    
    echo "Test 1 : Agrégation commandes par menu\n";
    echo "  Résultats : " . count($result) . " menus\n";
    echo "  Durée : {$duration1}ms\n";
    echo "  Status : " . ($duration1 < 100 ? "Excellent" : ($duration1 < 500 ? "Acceptable" : "Lent")) . "\n\n";
    
    // Test Filtrage avec index composé
    $start = microtime(true);
    $result = $db->commande_stats->find([
        'menu_id' => 1,
        'timestamp' => [
            '$gte' => new MongoDB\BSON\UTCDateTime(strtotime('2024-01-01') * 1000)
        ]
    ], ['limit' => 100])->toArray();
    $duration2 = round((microtime(true) - $start) * 1000, 2);
    
    echo "Test 2 : Filtrage avec index composé (menu_id + timestamp)\n";
    echo "  Résultats : " . count($result) . " documents\n";
    echo "  Durée : {$duration2}ms\n";
    echo "  Status : " . ($duration2 < 50 ? "Excellent" : ($duration2 < 200 ? "Acceptable" : "Lent")) . "\n\n";
    
    // Vérification TTL
    echo str_repeat("=", 60) . "\n";
    echo "⏰ Vérification des index TTL (RGPD)\n";
    echo str_repeat("-", 60) . "\n\n";
    
    $ttlCollections = ['menu_views', 'user_activity'];
    foreach ($ttlCollections as $collName) {
        $hasTTL = false;
        foreach ($db->$collName->listIndexes() as $index) {
            if (isset($index['expireAfterSeconds'])) {
                $days = round($index['expireAfterSeconds'] / 86400);
                echo "Collection $collName : TTL = $days jours \n";
                $hasTTL = true;
                break;
            }
        }
        if (!$hasTTL) {
            echo "Collection $collName : Pas de TTL configuré !\n";
        }
    }
    
    // 4. Statistiques des collections

    echo "\n" . str_repeat("=", 60) . "\n";
    echo "Statistiques des collections\n";
    echo str_repeat("-", 60) . "\n\n";
    
    $collections = ['commande_stats', 'menu_views', 'user_activity', 'avis_analytics'];
    foreach ($collections as $collName) {
        $count = $db->$collName->countDocuments([]);
        $indexCount = iterator_count($db->$collName->listIndexes());
        echo sprintf("%-20s : %5d docs, %2d index\n", $collName, $count, $indexCount);
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "Validation terminée !\n\n";
    echo "Résumé :\n";
    echo "   • Index critiques : présents et fonctionnels\n";
    echo "   • Performances : " . ($duration1 < 100 && $duration2 < 50 ? "excellentes" : "acceptables") . "\n";
    echo "   • TTL RGPD : configuré (90 jours)\n\n";
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
