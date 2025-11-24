<?php

namespace Framework\Core;

class Router
{
    private array $routes;

    public function __construct(private Container $container)
    {
        // Pass container to RouteLoader so it can inject controllers
        $loader = new RouteLoader($this->container);
        $this->routes = $loader->load();
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {

            // Skip if HTTP method does not match
            if ($route['method'] !== $method) {
                continue;
            }

            // Match regex
            if (preg_match($route['regex'], $path, $matches)) {

                $controller = $route['controller']; // Already instantiated via DI
                $function = $route['function'];

                // Build argument list for dynamic params
                $args = [];
                foreach ($route['params'] as $paramName) {
                    // only pass named matches
                    if (isset($matches[$paramName])) {
                        $args[] = $matches[$paramName];
                    }
                }

                $result = $controller->$function(...$args);

                header('Content-Type: application/json');
                echo json_encode($result);
                return;
            }
        }

        // 404 if no route matched
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
    }
}
