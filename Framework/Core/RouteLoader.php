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
            if ($file->isDir()) continue;

            if (!preg_match('/Controller\.php$/', $file->getFilename())) {
                continue;
            }

            $class = $this->convertFileToClass($file->getRealPath());

            if (!class_exists($class)) {
                continue;
            }

            $this->inspectController($class);
        }

        return $this->routes;
    }

    private function convertFileToClass(string $filePath): string
    {
        // Example: /var/www/App/routes/products/ProductsController.php
        $relative = str_replace(dirname(__DIR__, 2) . '/', '', $filePath);
        $class = str_replace('/', '\\', $relative);
        return preg_replace('/\.php$/', '', $class);
    }

    private function inspectController(string $class)
    {
        $reflection = new ReflectionClass($class);

        foreach ($reflection->getMethods() as $method) {
            foreach ($method->getAttributes() as $attribute) {

                $instance = $attribute->newInstance();

                if ($instance instanceof Get) {
                    $this->routes[] = [
                        'method' => 'GET',
                        'path' => $instance->path,
                        'class' => $class,
                        'function' => $method->getName()
                    ];
                }

                if ($instance instanceof Post) {
                    $this->routes[] = [
                        'method' => 'POST',
                        'path' => $instance->path,
                        'class' => $class,
                        'function' => $method->getName()
                    ];
                }
            }
        }
    }
}
