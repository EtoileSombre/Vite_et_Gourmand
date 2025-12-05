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

    // Exécute la bonne route
    public function dispatch()
    {
        $request = new Request();
        $requestMethod = $request->getMethod();
        $requestUri = $request->getUri();

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
