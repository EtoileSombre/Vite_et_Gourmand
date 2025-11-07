<?php
require_once '/var/www/html/config/db.php';

echo "=== TEST ADMIN ACCOUNT ===" . PHP_EOL;
$stmt = $pdo->query("SELECT u.utilisateur_id, u.email, u.prenom, u.nom, r.libelle as role, u.est_actif 
                     FROM utilisateur u 
                     LEFT JOIN role r ON u.role_id = r.role_id 
                     WHERE r.libelle = 'administrateur' 
                     LIMIT 1");
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    echo "✅ Admin trouvé:" . PHP_EOL;
    echo "  ID: " . $admin['utilisateur_id'] . PHP_EOL;
    echo "  Email: " . $admin['email'] . PHP_EOL;
    echo "  Nom: " . ($admin['nom'] ?? 'N/A') . " " . ($admin['prenom'] ?? 'N/A') . PHP_EOL;
    echo "  Actif: " . ($admin['est_actif'] ? 'Oui' : 'Non') . PHP_EOL;
} else {
    echo "❌ Aucun admin trouvé" . PHP_EOL;
}

echo PHP_EOL . "=== TEST USERS LIST ===" . PHP_EOL;
$stmt = $pdo->query("SELECT COUNT(*) as total FROM utilisateur");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Total utilisateurs: " . $result['total'] . PHP_EOL;

echo PHP_EOL . "=== TEST EMAIL FUNCTION ===" . PHP_EOL;
require_once '/var/www/html/config/mail.php';
echo "sendEmployeeWelcomeEmail exists: " . (function_exists('sendEmployeeWelcomeEmail') ? '✅ YES' : '❌ NO') . PHP_EOL;

echo PHP_EOL . "=== TEST ROUTES ===" . PHP_EOL;
echo "Routes à tester:" . PHP_EOL;
echo "  GET  /admin/users" . PHP_EOL;
echo "  POST /admin/users/create" . PHP_EOL;
echo "  POST /admin/users/toggle-active" . PHP_EOL;

echo PHP_EOL . "✅ Tests terminés!" . PHP_EOL;
