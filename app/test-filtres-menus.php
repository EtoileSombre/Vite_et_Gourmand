<?php
/**
 * Test des filtres dynamiques menus
 */

require_once '/var/www/html/autoload.php';

use App\Models\Menu;

echo "=== TEST FILTRES MENUS DYNAMIQUES ===" . PHP_EOL . PHP_EOL;

$menuModel = new Menu();
$menus = $menuModel->findActive();

echo "✅ Menus actifs: " . count($menus) . PHP_EOL . PHP_EOL;

// Afficher les données de chaque menu pour vérifier les filtres
echo "📋 Données des menus (pour filtrage):" . PHP_EOL;
echo str_repeat("=", 80) . PHP_EOL;

foreach ($menus as $i => $menu) {
    $num = $i + 1;
    echo "Menu #{$num}: {$menu['titre']}" . PHP_EOL;
    echo "  - Régime: " . ($menu['regime'] ?? 'N/A') . PHP_EOL;
    echo "  - Thème: " . ($menu['theme'] ?? 'N/A') . PHP_EOL;
    echo "  - Prix: " . ($menu['prix_par_personne'] ?? 'N/A') . " €/pers" . PHP_EOL;
    echo "  - Min personnes: " . ($menu['nombre_personne_minimum'] ?? 1) . PHP_EOL;
    echo str_repeat("-", 80) . PHP_EOL;
}

echo PHP_EOL;

// Statistiques pour les filtres
echo "📊 Statistiques pour filtres:" . PHP_EOL;
echo str_repeat("=", 80) . PHP_EOL;

// Régimes disponibles
$regimes = array_unique(array_column($menus, 'regime'));
echo "Régimes disponibles: " . implode(', ', array_filter($regimes)) . PHP_EOL;

// Prix min/max
$prix = array_column($menus, 'prix_par_personne');
echo "Prix minimum: " . min($prix) . " €" . PHP_EOL;
echo "Prix maximum: " . max($prix) . " €" . PHP_EOL;

// Nb personnes
$personnes = array_column($menus, 'nombre_personne_minimum');
echo "Personnes min: " . min($personnes) . " - max: " . max($personnes) . PHP_EOL;

echo PHP_EOL;

// Test des fichiers JavaScript
echo "🔍 Vérification des fichiers:" . PHP_EOL;
echo str_repeat("=", 80) . PHP_EOL;

$jsFile = '/var/www/html/public/assets/js/menu-filters.js';
if (file_exists($jsFile)) {
    echo "✅ menu-filters.js existe (" . number_format(filesize($jsFile)) . " octets)" . PHP_EOL;
    
    // Vérifier le contenu du fichier
    $content = file_get_contents($jsFile);
    $functions = [
        'applyFilters' => strpos($content, 'function applyFilters'),
        'updateMenuCount' => strpos($content, 'function updateMenuCount'),
        'resetFilters' => strpos($content, 'function resetFilters'),
        'addEventListener' => strpos($content, 'addEventListener'),
        'filterRegime' => strpos($content, 'filterRegime'),
        'filterPersonnes' => strpos($content, 'filterPersonnes'),
        'filterTheme' => strpos($content, 'filterTheme'),
        'filterPrixMax' => strpos($content, 'filterPrixMax'),
        'filterPrixMin' => strpos($content, 'filterPrixMin'),
    ];
    
    echo PHP_EOL . "  Fonctionnalités détectées:" . PHP_EOL;
    foreach ($functions as $func => $found) {
        echo "  " . ($found !== false ? "✅" : "❌") . " $func" . PHP_EOL;
    }
} else {
    echo "❌ menu-filters.js manquant!" . PHP_EOL;
}

echo PHP_EOL;

// Test de la vue menus/index.php
$viewFile = '/var/www/html/Views/menus/index.php';
if (file_exists($viewFile)) {
    echo "✅ menus/index.php existe" . PHP_EOL;
    
    $content = file_get_contents($viewFile);
    $checks = [
        'filterRegime input' => strpos($content, 'id="filterRegime"'),
        'filterPersonnes input' => strpos($content, 'id="filterPersonnes"'),
        'filterTheme input' => strpos($content, 'id="filterTheme"'),
        'filterPrixMax input' => strpos($content, 'id="filterPrixMax"'),
        'filterPrixMin input' => strpos($content, 'id="filterPrixMin"'),
        'script menu-filters.js' => strpos($content, 'menu-filters.js'),
        'data-regime attribute' => strpos($content, 'data-regime'),
        'data-prix attribute' => strpos($content, 'data-prix'),
        'data-theme attribute' => strpos($content, 'data-theme'),
        'menuCount element' => strpos($content, 'id="menuCount"'),
    ];
    
    echo PHP_EOL . "  Éléments HTML détectés:" . PHP_EOL;
    foreach ($checks as $check => $found) {
        echo "  " . ($found !== false ? "✅" : "❌") . " $check" . PHP_EOL;
    }
} else {
    echo "❌ menus/index.php manquant!" . PHP_EOL;
}

echo PHP_EOL;
echo str_repeat("=", 80) . PHP_EOL;
echo "✅ TESTS TERMINÉS!" . PHP_EOL;
echo str_repeat("=", 80) . PHP_EOL;
echo PHP_EOL;

echo "📌 Actions à tester manuellement:" . PHP_EOL;
echo "  1. Ouvrir http://localhost:8080/menus" . PHP_EOL;
echo "  2. Tester le filtre 'Régime' (dropdown)" . PHP_EOL;
echo "  3. Tester le filtre 'Nombre de personnes' (dropdown)" . PHP_EOL;
echo "  4. Tester le filtre 'Thème' (saisie texte)" . PHP_EOL;
echo "  5. Tester les filtres 'Prix min' et 'Prix max'" . PHP_EOL;
echo "  6. Vérifier que le compteur de résultats se met à jour" . PHP_EOL;
echo "  7. Tester le bouton 'Réinitialiser'" . PHP_EOL;
echo "  8. Vérifier que le filtrage est instantané (sans rechargement)" . PHP_EOL;
echo PHP_EOL;
