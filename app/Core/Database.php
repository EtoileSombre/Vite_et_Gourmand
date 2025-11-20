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
            $password = getenv('MYSQL_PASSWORD') ?: 'rootpass';
            
            self::$instance = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password
            );
            
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }

        return self::$instance;
    }
}
