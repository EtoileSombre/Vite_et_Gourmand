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

    // Récupère la connexion à la base de données
    public static function getInstance()
    {
        if (self::$instance === null) {
            $host = 'mysql';
            $dbname = 'vite_et_gourmand';
            $username = 'vg';
            $password = 'vgpass';
            
            self::$instance = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8",
                $username,
                $password
            );
            
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }

        return self::$instance;
    }
}
