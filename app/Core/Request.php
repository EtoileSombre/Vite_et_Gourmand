<?php

namespace App\Core;

/**
 * Classe Request
 * Gère les requêtes HTTP
 */
class Request
{
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    public function getUri()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return rtrim($uri, '/') ?: '/';
    }
    public function get($key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }
    public function post($key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }
    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }
}
