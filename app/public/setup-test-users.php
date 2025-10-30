<?php
/**
 * Script de mise à jour des mots de passe de test
 * À exécuter une seule fois pour créer les comptes de test
 */

require_once __DIR__ . '/../config/db.php';

// Mot de passe de test : password123
$testPassword = password_hash('password123', PASSWORD_DEFAULT);

try {
    // Mise à jour du mot de passe pour les comptes de test
    $users = [
        ['email' => 'admin@viteetgourmand.fr', 'prenom' => 'Alice', 'nom' => 'Admin'],
        ['email' => 'employe@viteetgourmand.fr', 'prenom' => 'Pierre', 'nom' => 'Employé'],
        ['email' => 'client@test.fr', 'prenom' => 'Marie', 'nom' => 'Client']
    ];

    foreach ($users as $user) {
        $stmt = $pdo->prepare("UPDATE utilisateur SET password = :password, prenom = :prenom, nom = :nom WHERE email = :email");
        $stmt->execute([
            'password' => $testPassword,
            'prenom' => $user['prenom'],
            'nom' => $user['nom'],
            'email' => $user['email']
        ]);
        echo "✅ Mot de passe mis à jour pour : {$user['email']}\n";
    }

    echo "\n🎉 Tous les comptes de test sont prêts !\n\n";
    echo "Identifiants de connexion :\n";
    echo "- Admin : admin@viteetgourmand.fr / password123\n";
    echo "- Employé : employe@viteetgourmand.fr / password123\n";
    echo "- Client : client@test.fr / password123\n";

} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
