<?php
// Database configuration for Vite & Gourmand

class DatabaseConfig {
    // MySQL configuration
    public static $mysql = [
        'host' => 'mysql',
        'database' => 'vite_gourmand',
        'username' => 'root',
        'password' => 'password',
        'charset' => 'utf8mb4'
    ];

    // MongoDB configuration
    public static $mongodb = [
        'host' => 'mongodb',
        'port' => 27017,
        'database' => 'vite_gourmand'
    ];

    // Application configuration
    public static $app = [
        'name' => 'Vite & Gourmand',
        'version' => '1.0.0',
        'timezone' => 'Europe/Paris'
    ];

    public static function getMySQLConnection() {
        $host = $_ENV['MYSQL_HOST'] ?? self::$mysql['host'];
        $database = $_ENV['MYSQL_DATABASE'] ?? self::$mysql['database'];
        $username = $_ENV['MYSQL_USER'] ?? self::$mysql['username'];
        $password = $_ENV['MYSQL_PASSWORD'] ?? self::$mysql['password'];
        
        try {
            $pdo = new PDO(
                "mysql:host={$host};dbname={$database};charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            return $pdo;
        } catch (PDOException $e) {
            error_log("MySQL Connection Error: " . $e->getMessage());
            return null;
        }
    }

    public static function getMongoDBConnection() {
        $host = $_ENV['MONGODB_HOST'] ?? self::$mongodb['host'];
        $port = $_ENV['MONGODB_PORT'] ?? self::$mongodb['port'];
        $database = $_ENV['MONGODB_DATABASE'] ?? self::$mongodb['database'];
        
        try {
            $client = new MongoDB\Driver\Manager("mongodb://{$host}:{$port}");
            return $client;
        } catch (Exception $e) {
            error_log("MongoDB Connection Error: " . $e->getMessage());
            return null;
        }
    }
}

// Set timezone
date_default_timezone_set(DatabaseConfig::$app['timezone']);
?>