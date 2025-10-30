<?php
/**
 * Page de test simple pour vérifier que l'architecture MVC fonctionne
 */

echo "<h1>🧪 Test Architecture MVC</h1>";
echo "<hr>";

// Test 1 : Autoloader
echo "<h2>1. Test Autoloader</h2>";
require_once __DIR__ . '/../autoload.php';
echo "✅ Autoloader chargé<br>";

// Test 2 : Classe Database
echo "<h2>2. Test Database</h2>";
try {
    require_once __DIR__ . '/../config/db.php';
    $db = App\Core\Database::getInstance();
    echo "✅ Connexion Database OK<br>";
    echo "Type : " . get_class($db) . "<br>";
} catch (Exception $e) {
    echo "❌ Erreur Database : " . $e->getMessage() . "<br>";
}

// Test 3 : Classe Model
echo "<h2>3. Test Model (Menu)</h2>";
try {
    $menuModel = new App\Models\Menu();
    echo "✅ Modèle Menu instancié<br>";
    
    $menus = $menuModel->findAll();
    echo "✅ Menus récupérés : " . count($menus) . " menu(s)<br>";
    
    if (!empty($menus)) {
        echo "<pre>";
        print_r($menus[0]);
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "❌ Erreur Model : " . $e->getMessage() . "<br>";
}

// Test 4 : Router
echo "<h2>4. Test Router</h2>";
try {
    $router = new App\Core\Router();
    echo "✅ Router instancié<br>";
    
    $router->get('/', 'App\Controllers\HomeController', 'index');
    $router->get('/menus', 'App\Controllers\MenuController', 'index');
    echo "✅ Routes ajoutées<br>";
} catch (Exception $e) {
    echo "❌ Erreur Router : " . $e->getMessage() . "<br>";
}

// Test 5 : Request
echo "<h2>5. Test Request</h2>";
try {
    $request = new App\Core\Request();
    echo "✅ Request instancié<br>";
    echo "Méthode : " . $request->getMethod() . "<br>";
    echo "URI : " . $request->getUri() . "<br>";
} catch (Exception $e) {
    echo "❌ Erreur Request : " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>✅ Tests terminés !</h2>";
echo "<p><a href='/'>Retour à l'accueil ancien</a> | <a href='/index_mvc.php'>Tester MVC</a></p>";
