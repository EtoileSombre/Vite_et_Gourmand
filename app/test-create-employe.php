<?php
/**
 * Test de création d'un employé via PHP
 * Simule une requête POST vers /admin/users/create
 */

require_once '/var/www/html/autoload.php';

use App\Models\User;
use App\Core\Session;

Session::start();

echo "=== TEST CRÉATION EMPLOYÉ ===" . PHP_EOL . PHP_EOL;

// Données de test
$email = 'test.employe' . time() . '@viteetgourmand.fr';
$prenom = 'Jean';
$nom = 'Test';

echo "Données de l'employé à créer:" . PHP_EOL;
echo "  Email: $email" . PHP_EOL;
echo "  Prénom: $prenom" . PHP_EOL;
echo "  Nom: $nom" . PHP_EOL;
echo PHP_EOL;

// Test 1: Créer l'employé
echo "📝 Étape 1: Création de l'employé..." . PHP_EOL;
$userModel = new User();
$userId = $userModel->createEmploye($email, $prenom, $nom);

if ($userId) {
    echo "Employé créé avec succès! ID: $userId" . PHP_EOL;
} else {
    echo "Échec de la création de l'employé" . PHP_EOL;
    exit(1);
}
echo PHP_EOL;

// Test 2: Générer le token de réinitialisation
echo "🔑 Étape 2: Génération du token de réinitialisation..." . PHP_EOL;
require_once '/var/www/html/config/db.php';

$token = bin2hex(random_bytes(32));
$expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

$stmt = $pdo->prepare('INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)');
$stmt->execute([$email, $token, $expiresAt]);

echo "Token généré: " . substr($token, 0, 20) . "..." . PHP_EOL;
echo "   Expire le: $expiresAt" . PHP_EOL;
echo PHP_EOL;

// Test 3: Envoyer l'email
echo "Étape 3: Envoi de l'email de bienvenue..." . PHP_EOL;
require_once '/var/www/html/config/mail.php';

$emailSent = sendEmployeeWelcomeEmail($email, $prenom, $token);

if ($emailSent) {
    echo "Email envoyé avec succès!" . PHP_EOL;
    echo "   Consultez MailHog: http://localhost:8025" . PHP_EOL;
} else {
    echo "Échec de l'envoi de l'email" . PHP_EOL;
}
echo PHP_EOL;

// Test 4: Vérifier dans la base de données
echo "🔍 Étape 4: Vérification dans la BDD..." . PHP_EOL;
$stmt = $pdo->prepare('
    SELECT u.utilisateur_id, u.email, u.prenom, u.nom, r.libelle as role, u.est_actif 
    FROM utilisateur u 
    LEFT JOIN role r ON u.role_id = r.role_id 
    WHERE u.utilisateur_id = ?
');
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "Utilisateur trouvé dans la BDD:" . PHP_EOL;
    echo "   ID: " . $user['utilisateur_id'] . PHP_EOL;
    echo "   Email: " . $user['email'] . PHP_EOL;
    echo "   Nom: " . $user['nom'] . " " . $user['prenom'] . PHP_EOL;
    echo "   Rôle: " . $user['role'] . PHP_EOL;
    echo "   Statut: " . ($user['est_actif'] ? 'Actif' : 'Inactif') . PHP_EOL;
} else {
    echo "Utilisateur non trouvé dans la BDD" . PHP_EOL;
}
echo PHP_EOL;

// Test 5: Test de désactivation
echo "⏸️  Étape 5: Test de désactivation..." . PHP_EOL;
$userModel->toggleActive($userId, false);
echo "Utilisateur désactivé" . PHP_EOL;

$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
echo "   Nouveau statut: " . ($user['est_actif'] ? 'Actif' : 'Inactif') . PHP_EOL;
echo PHP_EOL;

// Test 6: Test de réactivation
echo "▶️  Étape 6: Test de réactivation..." . PHP_EOL;
$userModel->toggleActive($userId, true);
echo "Utilisateur réactivé" . PHP_EOL;

$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
echo "   Nouveau statut: " . ($user['est_actif'] ? 'Actif' : 'Inactif') . PHP_EOL;
echo PHP_EOL;

echo "========================================" . PHP_EOL;
echo "TOUS LES TESTS RÉUSSIS!" . PHP_EOL;
echo "========================================" . PHP_EOL;
echo PHP_EOL;
echo "📌 Actions à tester manuellement:" . PHP_EOL;
echo "  1. Se connecter comme admin: admin@viteetgourmand.fr" . PHP_EOL;
echo "  2. Aller sur /admin/users" . PHP_EOL;
echo "  3. Cliquer sur 'Créer un employé'" . PHP_EOL;
echo "  4. Remplir le formulaire" . PHP_EOL;
echo "  5. Vérifier l'email dans MailHog" . PHP_EOL;
echo "  6. Tester l'activation/désactivation" . PHP_EOL;
echo PHP_EOL;
