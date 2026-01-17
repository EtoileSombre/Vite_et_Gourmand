<?php
/**
 * Script de synchronisation MySQL → MongoDB
 * Importe toutes les commandes existantes de MySQL vers MongoDB pour les statistiques
 */

require_once __DIR__ . '/../vendor/autoload.php';

echo "🔄 Synchronisation MySQL → MongoDB\n";
echo str_repeat("=", 60) . "\n\n";

try {
    // === CONNEXION MYSQL ===
    echo "📦 Connexion à MySQL...\n";
    $mysqlHost = getenv('MYSQL_HOST') ?: 'mysql';
    $mysqlDb = getenv('MYSQL_DATABASE') ?: 'vite_et_gourmand';
    $mysqlUser = getenv('MYSQL_USER') ?: 'root';
    $mysqlPass = getenv('MYSQL_PASSWORD') ?: 'rootpass';
    
    $pdo = new PDO(
        "mysql:host=$mysqlHost;dbname=$mysqlDb;charset=utf8mb4",
        $mysqlUser,
        $mysqlPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "   ✅ MySQL connecté\n\n";
    
    // === CONNEXION MONGODB ===
    echo "📦 Connexion à MongoDB...\n";
    $mongoUri = getenv('MONGO_URI') ?: 'mongodb://vgroot:vgrootpass@mongo:27017';
    $mongoDbName = getenv('MONGO_DATABASE') ?: 'vg';
    
    $mongoClient = new MongoDB\Client($mongoUri);
    $db = $mongoClient->$mongoDbName;
    $collection = $db->commande_stats;
    echo "   ✅ MongoDB connecté\n\n";
    
    // === NETTOYAGE DONNÉES DE TEST ===
    echo "🗑️  Suppression des données de test...\n";
    $deleteResult = $collection->deleteMany(['numero_commande' => ['$regex' => 'TEST']]);
    echo "   ✓ " . $deleteResult->getDeletedCount() . " données de test supprimées\n\n";
    
    // === RÉCUPÉRATION COMMANDES MYSQL ===
    echo "📊 Récupération des commandes MySQL...\n";
    $stmt = $pdo->query("
        SELECT 
            c.numero_commande,
            c.total_final,
            c.statut,
            c.created_at,
            cm.menu_id,
            cm.quantite as nombre_personne
        FROM commande c
        INNER JOIN commande_menu cm ON c.numero_commande = cm.numero_commande
        ORDER BY c.created_at DESC
    ");
    
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totalCommandes = count($commandes);
    echo "   ✓ $totalCommandes lignes trouvées\n\n";
    
    if ($totalCommandes === 0) {
        echo "⚠️  Aucune commande trouvée dans MySQL.\n";
        echo "   Créez d'abord des commandes via l'interface utilisateur.\n\n";
        exit(0);
    }
    
    // === SYNCHRONISATION ===
    echo "⚡ Synchronisation en cours...\n";
    $inserted = 0;
    $skipped = 0;
    $errors = 0;
    
    foreach ($commandes as $index => $cmd) {
        try {
            // Vérifier si déjà dans MongoDB
            $existing = $collection->findOne([
                'numero_commande' => $cmd['numero_commande'],
                'menu_id' => (int)$cmd['menu_id']
            ]);
            
            if ($existing) {
                $skipped++;
                continue;
            }
            
            // Insérer dans MongoDB
            $timestamp = strtotime($cmd['created_at']);
            $collection->insertOne([
                'numero_commande' => $cmd['numero_commande'],
                'menu_id' => (int)$cmd['menu_id'],
                'prix_total' => (float)$cmd['total_final'],
                'nombre_personne' => (int)$cmd['nombre_personne'],
                'statut' => $cmd['statut'],
                'timestamp' => new MongoDB\BSON\UTCDateTime($timestamp * 1000),
                'date' => date('Y-m-d', $timestamp)
            ]);
            
            $inserted++;
            
            // Afficher progression tous les 10
            if (($inserted + $skipped) % 10 === 0) {
                $progress = round((($inserted + $skipped) / $totalCommandes) * 100);
                echo "   ⏳ Progression : $progress% ($inserted insérées, $skipped déjà présentes)\n";
            }
            
        } catch (Exception $e) {
            $errors++;
            error_log("Erreur sync commande {$cmd['numero_commande']}: " . $e->getMessage());
        }
    }
    
    echo "\n";
    echo str_repeat("=", 60) . "\n";
    echo "✅ SYNCHRONISATION TERMINÉE\n";
    echo str_repeat("=", 60) . "\n\n";
    
    echo "📈 RÉSULTATS :\n";
    echo "   • Commandes insérées :      $inserted\n";
    echo "   • Commandes déjà présentes : $skipped\n";
    echo "   • Erreurs :                  $errors\n";
    echo "   • TOTAL dans MongoDB :       " . $collection->countDocuments() . "\n\n";
    
    // === STATISTIQUES ===
    echo "📊 STATISTIQUES PAR MENU\n";
    echo str_repeat("-", 60) . "\n\n";
    
    $pipeline = [
        ['$group' => [
            '_id' => '$menu_id',
            'nb_commandes' => ['$sum' => 1],
            'ca_total' => ['$sum' => '$prix_total'],
            'total_personnes' => ['$sum' => '$nombre_personne']
        ]],
        ['$sort' => ['nb_commandes' => -1]],
        ['$limit' => 10]
    ];
    
    $topMenus = $collection->aggregate($pipeline);
    
    echo sprintf("%-10s %-20s %-15s %-15s\n", "Menu ID", "Commandes", "CA Total", "Personnes");
    echo str_repeat("-", 60) . "\n";
    
    foreach ($topMenus as $menu) {
        printf(
            "%-10d %-20d %-15s %-15d\n",
            $menu['_id'],
            $menu['nb_commandes'],
            number_format($menu['ca_total'], 2, ',', ' ') . ' €',
            $menu['total_personnes']
        );
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "🎯 Accédez aux statistiques : http://localhost:8080/admin/stats\n";
    echo str_repeat("=", 60) . "\n\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur MySQL : " . $e->getMessage() . "\n";
    exit(1);
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "❌ Erreur MongoDB : " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
