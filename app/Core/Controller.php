<?php

namespace App\Core;

/**
 * Classe Controller de base
 * Tous les contrôleurs héritent de cette classe
 */
abstract class Controller
{
    // Affiche une vue
    protected function render($view, $data = [])
    {
        extract($data);
        
        $viewPath = __DIR__ . "/../Views/$view.php";
        
        if (!file_exists($viewPath)) {
            echo "Erreur : La vue $view n'existe pas";
            return;
        }
        
        require_once $viewPath;
    }

    // Redirige vers une URL
    protected function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    // Retourne du JSON
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
