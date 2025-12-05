<?php
/**
 * Script de test MongoDB
 * Teste la connexion et insère des données de test
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/mongodb.php';
require_once __DIR__ . '/../config/MongoStats.php';

use App\Config\MongoStats;

echo "=== TEST MONGODB ===\n\n";

// 1. Vérifier la connexion
echo "1. Test de connexion MongoDB...\n";
$mongoStats = new MongoStats();

if ($mongoStats->isAvailable()) {
    echo "✓ MongoDB connecté avec succès\n\n";
} else {
    echo "✗ Impossible de se connecter à MongoDB\n";
    exit(1);
}

// 2. Insérer des données de test - Vues de menus
echo "2. Insertion de vues de menus de test...\n";
$menusTest = [
    ['id' => 1, 'titre' => 'Menu de Noël Traditionnel'],
    ['id' => 2, 'titre' => 'Menu de Pâques Gourmet'],
    ['id' => 3, 'titre' => 'Menu Classique Familial'],
    ['id' => 4, 'titre' => 'Menu Évènement Entreprise']
];

foreach ($menusTest as $menu) {
    for ($i = 0; $i < rand(5, 15); $i++) {
        $success = $mongoStats->logMenuView($menu['id'], ['titre' => $menu['titre']]);
        if ($success) {
            echo "  ✓ Vue enregistrée pour menu #{$menu['id']}\n";
        } else {
            echo "  ✗ Erreur pour menu #{$menu['id']}\n";
        }
        usleep(100000); // 0.1 seconde entre chaque insertion
    }
}
echo "\n";

// 3. Insérer des activités utilisateurs
echo "3. Insertion d'activités utilisateurs...\n";
$activites = [
    ['action' => 'login', 'user_id' => 2, 'details' => ['role' => 'client']],
    ['action' => 'view_menu', 'user_id' => 2, 'details' => ['menu_id' => 1]],
    ['action' => 'add_to_cart', 'user_id' => 2, 'details' => ['menu_id' => 1, 'nb_personnes' => 4]],
    ['action' => 'login', 'user_id' => 3, 'details' => ['role' => 'employe']],
    ['action' => 'view_commandes', 'user_id' => 3, 'details' => ['statut' => 'en cours']],
    ['action' => 'login', 'user_id' => 1, 'details' => ['role' => 'admin']],
    ['action' => 'view_stats', 'user_id' => 1, 'details' => ['page' => 'dashboard']],
];

foreach ($activites as $activite) {
    $success = $mongoStats->logUserActivity(
        $activite['action'],
        $activite['user_id'],
        $activite['details']
    );
    if ($success) {
        echo "  ✓ Activité '{$activite['action']}' enregistrée\n";
    } else {
        echo "  ✗ Erreur pour activité '{$activite['action']}'\n";
    }
    usleep(50000); // 0.05 seconde
}
echo "\n";

// 4. Insérer des statistiques de commandes
echo "4. Insertion de statistiques de commandes...\n";
$commandes = [
    [
        'numero' => 'CMD-2024-001',
        'data' => [
            'menu_id' => 1,
            'prix_total' => 240.00,
            'nombre_personne' => 4,
            'statut' => 'validée'
        ]
    ],
    [
        'numero' => 'CMD-2024-002',
        'data' => [
            'menu_id' => 2,
            'prix_total' => 180.00,
            'nombre_personne' => 3,
            'statut' => 'en préparation'
        ]
    ],
    [
        'numero' => 'CMD-2024-003',
        'data' => [
            'menu_id' => 3,
            'prix_total' => 120.00,
            'nombre_personne' => 2,
            'statut' => 'livrée'
        ]
    ],
    [
        'numero' => 'CMD-2024-004',
        'data' => [
            'menu_id' => 1,
            'prix_total' => 360.00,
            'nombre_personne' => 6,
            'statut' => 'validée'
        ]
    ],
];

foreach ($commandes as $commande) {
    $success = $mongoStats->logCommande($commande['numero'], $commande['data']);
    if ($success) {
        echo "  ✓ Commande {$commande['numero']} enregistrée\n";
    } else {
        echo "  ✗ Erreur pour commande {$commande['numero']}\n";
    }
    usleep(50000); // 0.05 seconde
}
echo "\n";

// 5. Récupérer et afficher les statistiques
echo "5. Récupération des statistiques...\n\n";

// Top menus
echo "--- Top 3 des menus les plus vus ---\n";
$topMenus = $mongoStats->getTopMenus(3);
foreach ($topMenus as $index => $menu) {
    $position = $index + 1;
    echo "  {$position}. Menu #{$menu['menu_id']} - {$menu['count']} vues\n";
    if (isset($menu['titre'])) {
        echo "     Titre: {$menu['titre']}\n";
    }
}
echo "\n";

// Vues par jour
echo "--- Vues de menus par jour ---\n";
$viewsPerDay = $mongoStats->getViewsPerDay(7);
foreach ($viewsPerDay as $day) {
    echo "  {$day['date']} : {$day['count']} vues\n";
}
echo "\n";

// Statistiques globales
echo "--- Statistiques globales ---\n";
$globalStats = $mongoStats->getGlobalStats();
echo "  Total vues menus: {$globalStats['total_menu_views']}\n";
echo "  Total activités utilisateurs: {$globalStats['total_user_activities']}\n";
echo "  Total commandes: {$globalStats['total_commandes']}\n";
if (isset($globalStats['chiffre_affaires'])) {
    echo "  Chiffre d'affaires total: " . number_format($globalStats['chiffre_affaires'], 2, ',', ' ') . " €\n";
}
echo "\n";

echo "=== TEST TERMINÉ AVEC SUCCÈS ===\n";
