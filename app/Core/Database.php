<?php

namespace App\Core;

use PDO;

/**
 * Classe Database
 * Connexion à la base de données
 */
class Database
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            $host = getenv('MYSQL_HOST') ?: 'mysql';
            $dbname = 'vite_et_gourmand';
            $username = getenv('MYSQL_USER') ?: 'root';
            $password = getenv('MYSQL_PASSWORD');
            
            if (!$password) {
                throw new \RuntimeException('Variable d\'environnement MYSQL_PASSWORD non définie');
            }
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            self::$instance = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                $options
            );

            error_log("[DATABASE] Connexion MySQL établie : host={$host}, db={$dbname}");
        }

        return self::$instance;
    }
}
