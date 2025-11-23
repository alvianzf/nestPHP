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

      if ($route['method'] !== $method) {
        continue;
      }

      if (preg_match($route['regex'], $path, $matches)) {

        $controller = new $route['class'];
        $function = $route['function'];

        // Build param list in correct order
        $args = [];
        foreach ($route['params'] as $paramName) {
          $args[] = $matches[$paramName];
        }

        $result = $controller->$function(...$args);

        header('Content-Type: application/json');
        echo json_encode($result);
        return;
      }
    }


    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
  }
}
