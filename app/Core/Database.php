<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Classe Database - Singleton pour la connexion PDO
 * Gère la connexion unique à la base de données MySQL
 */
class Database
{
    private static ?PDO $instance = null;

    /**
     * Récupère l'instance unique de la connexion PDO
     * 
     * @return PDO Instance PDO configurée
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                // Récupération des variables d'environnement
                $host = getenv('MYSQL_HOST') ?: 'mysql';
                $dbname = 'vite_et_gourmand';
                $username = getenv('MYSQL_USER') ?: 'vg';
                $password = getenv('MYSQL_PASSWORD') ?: 'vgpass';
                $port = getenv('MYSQL_PORT') ?: 3306;
                
                $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4;port={$port}";
                
                self::$instance = new PDO(
                    $dsn,
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                    ]
                );
            } catch (PDOException $e) {
                error_log("Erreur de connexion à la base de données : " . $e->getMessage());
                die("Erreur de connexion à la base de données. Consultez les logs.");
            }
        }

        return self::$instance;
    }

    /**
     * Empêche le clonage de l'instance (pattern Singleton)
     */
    private function __clone() {}

    /**
     * Empêche la désérialisation de l'instance (pattern Singleton)
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}
