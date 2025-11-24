<?php

namespace Framework\Core;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

use Framework\Core\Attributes\Get;
use Framework\Core\Attributes\Post;

class RouteLoader
{
  private string $basePath;
  private array $routes = [];

  public function __construct()
  {
    $this->basePath = dirname(__DIR__, 2) . '/App/routes';
  }

  public function load(): array
  {
    $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($this->basePath)
    );

    foreach ($iterator as $file) {

      if ($file->isDir()) {
        continue;
      }

      if (!preg_match('/Controller\.php$/', $file->getFilename())) {
        continue;
      }

      // Determine folder name for auto prefix
      $folderName = basename(dirname($file->getRealPath()));
      $folderPrefix = '/' . strtolower($folderName);

      // Convert file path to namespaced class
      $class = $this->convertFileToClass($file->getRealPath());

      if (!class_exists($class)) {
        require_once $file->getRealPath();

        if (!class_exists($class)) {
          continue;
        }
      }

      $this->inspectController($class, $folderPrefix);
    }

    return $this->routes;
  }

  private function convertFileToClass(string $filePath): string
  {
    $relative = str_replace(dirname(__DIR__, 2) . '/', '', $filePath);

    $class = str_replace('/', '\\', $relative);

    return preg_replace('/\.php$/', '', $class);
  }

  private function inspectController(string $class, string $folderPrefix)
  {
    $reflection = new ReflectionClass($class);

    // Container Class
    $container = new \Framework\Core\Container();

    $controllerInstance = $container->get($class);

    foreach ($reflection->getMethods() as $method) {
      foreach ($method->getAttributes() as $attribute) {

        $instance = $attribute->newInstance();
        $relativePath = '/' . ltrim($instance->path, '/');
        $fullPath = rtrim($folderPrefix . $relativePath, '/');

        if ($instance instanceof Get) {
          $this->routes[] = [
            'method' => 'GET',
            'path' => $fullPath,
            'regex' => $this->convertToRegex($fullPath),
            'controller' => $controllerInstance,
            'function' => $method->getName(),
            'params' => $this->extractParams($fullPath)
          ];
        }

        if ($instance instanceof Post) {
          $this->routes[] = [
            'method' => 'POST',
            'path' => $fullPath,
            'regex' => $this->convertToRegex($fullPath),
            'controller' => $controllerInstance,
            'function' => $method->getName(),
            'params' => $this->extractParams($fullPath)
          ];
        }
      }
    }
  }

  private function convertToRegex(string $path): string
  {
    $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
    return '#^' . $regex . '$#';
  }

  private function extractParams(string $path): array
  {
    preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $path, $matches);
    return $matches[1];
  }
}
