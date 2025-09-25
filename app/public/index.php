<?php
echo "<h1>Vite & Gourmand – Environnement Docker OK ✅</h1>";

echo "<p><a href='?phpinfo=1'>Afficher phpinfo()</a></p>";
if (isset($_GET['phpinfo'])) { phpinfo(); exit; }

$errors = [];

// --- MySQL (PDO) ---
$dbHost = getenv('DB_HOST') ?: 'mysql';
$dbName = getenv('DB_NAME') ?: 'vg';
$dbUser = getenv('DB_USER') ?: 'vg';
$dbPass = getenv('DB_PASS') ?: 'vgpass';

echo "<h2>Test MySQL (PDO)</h2>";
try {
  $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  ]);
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS contact_messages (
      id INT AUTO_INCREMENT PRIMARY KEY,
      nom VARCHAR(100) NOT NULL,
      email VARCHAR(190) NOT NULL,
      message TEXT NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ");
  $stmt = $pdo->prepare("INSERT INTO contact_messages (nom, email, message) VALUES (?, ?, ?)");
  $stmt->execute(['Julie', 'julie@test.com', 'Bonjour, je teste la base.']);
  $count = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
  echo "<p>✅ MySQL OK — lignes dans contact_messages : <strong>$count</strong></p>";
} catch (Throwable $e) {
  $errors[] = "MySQL: " . $e->getMessage();
  echo "<p>❌ MySQL KO — " . htmlspecialchars($e->getMessage()) . "</p>";
}

// --- MongoDB ---
echo "<h2>Test MongoDB</h2>";
try {
  if (!extension_loaded('mongodb')) {
    throw new Exception("Extension mongodb non chargée");
  }
  $mongoUser = getenv('MONGO_USER') ?: 'vgroot';
  $mongoPass = getenv('MONGO_PASS') ?: 'vgrootpass';
  $mongoHost = getenv('MONGO_HOST') ?: 'mongo';
  $mongoDb   = getenv('MONGO_DB')   ?: 'vg';

  $uri = "mongodb://{$mongoUser}:{$mongoPass}@{$mongoHost}:27017/{$mongoDb}?authSource=admin";
  $manager = new MongoDB\Driver\Manager($uri);
  $bulk = new MongoDB\Driver\BulkWrite();
  $bulk->insert(['from' => 'index.php', 'created_at' => new MongoDB\BSON\UTCDateTime()]);
  $result = $manager->executeBulkWrite("$mongoDb.test_messages", $bulk);
  echo "<p>✅ MongoDB OK — document inséré.</p>";
} catch (Throwable $e) {
  $errors[] = "MongoDB: " . $e->getMessage();
  echo "<p>❌ MongoDB KO — " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Interfaces utiles</h2>";
$pma = getenv('PMA_PORT') ?: '8090';
$me  = getenv('MONGO_EXPRESS_PORT') ?: '8081';
$mh  = getenv('MAILHOG_WEB_PORT') ?: '8025';
echo "<ul>";
echo "<li><a href='http://localhost:$pma' target='_blank'>phpMyAdmin</a> (serveur: mysql, user: vg, pass: vgpass)</li>";
echo "<li><a href='http://localhost:$me' target='_blank'>Mongo Express</a> (login: admin / pass: admin)</li>";
echo "<li><a href='http://localhost:$mh' target='_blank'>Mailhog</a> (webmail tests)</li>";
echo "</ul>";

if ($errors) {
  echo "<h3>Résumé erreurs</h3><pre>" . htmlspecialchars(implode("\n", $errors)) . "</pre>";
} else {
  echo "<p><strong>Tout est OK 🎉</strong></p>";
}
