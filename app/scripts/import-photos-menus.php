<?php

require_once __DIR__ . '/../autoload.php';

use App\Factory\RepositoryFactory;

echo "=== IMPORTATION DES PHOTOS DE MENUS ===\n\n";

try {
    $factory = RepositoryFactory::getInstance();
    $menuRepository = $factory->createMenuRepository();
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage() . "\n");
}

// Dossier contenant les photos
$imgBaseDir = __DIR__ . '/../public/assets/img/';

// Récupérer tous les menus actifs
$menus = $menuRepository->findAll();

$totalPhotosImportees = 0;
$totalMenusTraites = 0;

foreach ($menus as $menu) {
    $menuId = $menu['menu_id'];
    $menuTitre = $menu['titre'];
    
    echo "Traitement du menu : {$menuTitre}\n";
    
    $nbPhotos = $menuRepository->importPhotosFromDirectory($menuId, $menuTitre, $imgBaseDir);
    
    if ($nbPhotos === 0) {
        echo "   Aucune photo trouvée ou dossier inexistant\n\n";
        continue;
    }
    
    $totalPhotosImportees += $nbPhotos;
    $totalMenusTraites++;
    echo "  {$nbPhotos} photo(s) importée(s)\n\n";
}

echo "\n=== RÉSUMÉ ===\n";
echo "Menus traités : {$totalMenusTraites}\n";
echo "Photos importées : {$totalPhotosImportees}\n";
echo "\nImportation terminée !\n";
