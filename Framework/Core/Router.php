<?php

namespace Framework\Core;

class Router
{
    private array $routes;

    public function __construct(private Container $container)
    {
        $loader = new RouteLoader($this->container);
        $this->routes = $loader->load();
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            if (preg_match($route['regex'], $path, $matches)) {
                $controller = $route['controller'];
                $function = $route['function'];

                $args = [];
                foreach ($route['params'] as $paramName) {
                    if (isset($matches[$paramName])) $args[] = $matches[$paramName];
                }

                $result = $controller->$function(...$args);

                if ($result !== null) {
                    header('Content-Type: application/json');
                    echo json_encode($result);
                }
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
    }
}
