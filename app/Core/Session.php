<?php

namespace App\Core;

/**
 * Classe Session
 * Gère les sessions de manière sécurisée
 */
class Session
{
    /**
     * Démarre la session si elle n'est pas déjà démarrée
     * 
     * @return void
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Configuration sécurisée de la session
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_samesite', 'Strict');
            
            session_start();
        }
    }

    /**
     * Définit une valeur dans la session
     * 
     * @param string $key Clé
     * @param mixed $value Valeur
     * @return void
     */
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Récupère une valeur de la session
     * 
     * @param string $key Clé
     * @param mixed $default Valeur par défaut
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Vérifie si une clé existe dans la session
     * 
     * @param string $key Clé
     * @return bool
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Supprime une valeur de la session
     * 
     * @param string $key Clé
     * @return void
     */
    public static function delete(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Détruit complètement la session
     * 
     * @return void
     */
    public static function destroy(): void
    {
        self::start();
        session_unset();
        session_destroy();
    }

    /**
     * Régénère l'ID de session (sécurité après connexion)
     * 
     * @return void
     */
    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    /**
     * Définit un message flash (disponible une seule fois)
     * 
     * @param string $key Clé du message
     * @param string $message Message
     * @return void
     */
    public static function flash(string $key, string $message): void
    {
        self::set("flash_$key", $message);
    }

    /**
     * Récupère et supprime un message flash
     * 
     * @param string $key Clé du message
     * @return string|null
     */
    public static function getFlash(string $key): ?string
    {
        $message = self::get("flash_$key");
        self::delete("flash_$key");
        return $message;
    }
}
