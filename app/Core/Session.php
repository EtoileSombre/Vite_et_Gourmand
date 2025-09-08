<?php

namespace App\Core;

/**
 * Classe Session
 * Gère les sessions
 */
class Session
{
    // Démarre la session
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Enregistre une valeur
    public static function set($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }
    public static function get($key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    public static function has($key)
    {
        self::start();
        return isset($_SESSION[$key]);
    }
    public static function delete($key)
    {
        self::start();
        unset($_SESSION[$key]);
    }

    // Détruit la session
    public static function destroy()
    {
        self::start();
        session_destroy();
    }
}
