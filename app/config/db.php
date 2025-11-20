<?php
/**
 * Configuration de la connexion à la base de données MySQL
 * PDO avec gestion d'erreurs
 */

// Chargement des variables d'environnement depuis Docker
$host = getenv('MYSQL_HOST') ?: 'mysql';
$dbname = 'vite_et_gourmand'; // Nom de la base créée dans le SQL
$username = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: 'rootpass';
$port = getenv('MYSQL_PORT') ?: 3306;

// Options PDO pour sécurité et performance
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,      // Lance des exceptions en cas d'erreur
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Retourne des tableaux associatifs
    PDO::ATTR_EMULATE_PREPARES => false,              // Vraies requêtes préparées (sécurité)
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
];

try {
    // Création de la connexion PDO
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        $options
    );
    
    // Message de debug (à retirer en production)
    // echo "✅ Connexion réussie à la base de données $dbname<br>";
    
} catch (PDOException $e) {
    // En cas d'erreur, affiche un message et arrête le script
    die("❌ Erreur de connexion à la base de données : " . $e->getMessage());
}

// La variable $pdo est maintenant disponible dans tous les fichiers qui incluent db.php
