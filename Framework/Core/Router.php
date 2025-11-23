<?php

namespace Framework\Core;

class Router
{
    private array $routes;

    public function __construct()
    {
        $loader = new RouteLoader();
        $this->routes = $loader->load();
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                
                $controller = new $route['class'];
                $function = $route['function'];

                $result = $controller->$function();

                header('Content-Type: application/json');
                echo json_encode($result);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
    }
}
