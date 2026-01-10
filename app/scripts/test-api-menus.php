<?php
/**
 * Script de test de l'API de filtrage des menus
 * Usage: php test-api-menus.php
 */

// Charger l'autoloader
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../autoload.php';

use App\Models\Menu;

echo "=== Test de l'API de filtrage des menus ===\n\n";

try {
    $menuModel = new Menu();
    
    // Test 1: Récupérer tous les menus
    echo "Test 1: Récupération de tous les menus actifs\n";
    echo "----------------------------------------------\n";
    $allMenus = $menuModel->findActiveWithPhotos();
    echo "Nombre de menus trouvés: " . count($allMenus) . "\n";
    
    if (!empty($allMenus)) {
        $first = $allMenus[0];
        echo "Premier menu:\n";
        echo "  - ID: " . $first['menu_id'] . "\n";
        echo "  - Titre: " . $first['titre'] . "\n";
        echo "  - Prix: " . $first['prix_par_personne'] . "€\n";
        echo "  - Min personnes: " . $first['nombre_personne_minimum'] . "\n";
        echo "  - Régime: " . ($first['regime'] ?? 'N/A') . "\n";
        echo "  - Thème: " . ($first['theme'] ?? 'N/A') . "\n";
        echo "  - Photos: " . count($first['photos'] ?? []) . "\n";
    }
    echo "\n";
    
    // Test 2: Filtrer par régime
    echo "Test 2: Filtrage par régime (végétarien)\n";
    echo "----------------------------------------------\n";
    $filters = ['regime' => 'végétarien'];
    $filteredMenus = $menuModel->findFiltered($filters);
    echo "Nombre de menus trouvés: " . count($filteredMenus) . "\n";
    foreach ($filteredMenus as $menu) {
        echo "  - " . $menu['titre'] . " (Régime: " . ($menu['regime'] ?? 'N/A') . ")\n";
    }
    echo "\n";
    
    // Test 3: Filtrer par thème
    echo "Test 3: Filtrage par thème (Noël)\n";
    echo "----------------------------------------------\n";
    $filters = ['theme' => 'Noël'];
    $filteredMenus = $menuModel->findFiltered($filters);
    echo "Nombre de menus trouvés: " . count($filteredMenus) . "\n";
    foreach ($filteredMenus as $menu) {
        echo "  - " . $menu['titre'] . " (Thème: " . ($menu['theme'] ?? 'N/A') . ")\n";
    }
    echo "\n";
    
    // Test 4: Filtrer par prix
    echo "Test 4: Filtrage par prix (max 30€)\n";
    echo "----------------------------------------------\n";
    $filters = ['prixMax' => 30];
    $filteredMenus = $menuModel->findFiltered($filters);
    echo "Nombre de menus trouvés: " . count($filteredMenus) . "\n";
    foreach ($filteredMenus as $menu) {
        echo "  - " . $menu['titre'] . " (" . $menu['prix_par_personne'] . "€)\n";
    }
    echo "\n";
    
    // Test 5: Filtrage multiple
    echo "Test 5: Filtrage multiple (prix max 40€, min 2 personnes)\n";
    echo "----------------------------------------------\n";
    $filters = [
        'prixMax' => 40,
        'minPersonnes' => 2
    ];
    $filteredMenus = $menuModel->findFiltered($filters);
    echo "Nombre de menus trouvés: " . count($filteredMenus) . "\n";
    foreach ($filteredMenus as $menu) {
        echo "  - " . $menu['titre'] . " (" . $menu['prix_par_personne'] . "€, min " . $menu['nombre_personne_minimum'] . " pers)\n";
    }
    echo "\n";
    
    // Test 6: Simuler une requête JSON
    echo "Test 6: Simulation de la réponse JSON\n";
    echo "----------------------------------------------\n";
    $filters = [];
    $menus = $menuModel->findFiltered($filters);
    $response = [
        'success' => true,
        'count' => count($menus),
        'menus' => array_map(function($menu) {
            return [
                'menu_id' => $menu['menu_id'],
                'titre' => $menu['titre'],
                'prix_par_personne' => $menu['prix_par_personne'],
                'nombre_personne_minimum' => $menu['nombre_personne_minimum'],
                'regime' => $menu['regime'] ?? null,
                'theme' => $menu['theme'] ?? null,
                'photos_count' => count($menu['photos'] ?? [])
            ];
        }, array_slice($menus, 0, 3)) // Afficher seulement les 3 premiers
    ];
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
    echo "\nTous les tests sont réussis !\n";
    
} catch (\Exception $e) {
    echo "\nErreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
