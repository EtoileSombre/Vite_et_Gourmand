<?php

namespace App\Core;

class Router
{
    private $routes = [];

    public function get($path, $controller, $method)
    {
        $this->routes[] = [
            'method' => 'GET',
            'path' => $path,
            'controller' => $controller,
            'action' => $method
        ];
    }

    public function post($path, $controller, $method)
    {
        $this->routes[] = [
            'method' => 'POST',
            'path' => $path,
            'controller' => $controller,
            'action' => $method
        ];
    }

    public function dispatch()
    {
       $request = new Request();
       $requestMethod = $request->getMethod();
   if ($requestMethod === 'HEAD') {
       $requestMethod = 'GET';
}

    // Normalisation URI (ignore ?query + gère slash final)
                $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
                $requestUri = rtrim($requestUri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['path'] === $requestUri) {
                // Créer le contrôleur et appeler la méthode
                $controller = new $route['controller']();
                $action = $route['action'];
                $controller->$action($request);
                return;
            }
        }

        // Page non trouvée
        http_response_code(404);
        echo "<h1>404 - Page non trouvée</h1>";
        echo "<p><a href='/'>Retour à l'accueil</a></p>";
    }
}
