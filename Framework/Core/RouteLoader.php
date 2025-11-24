<?php

namespace Framework\Core;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use Framework\Core\Attributes\Get;

class RouteLoader
{
    private array $routes = [];

    public function __construct(private Container $container)
    {
    }

    public function load(): array
    {
        $basePath = dirname(__DIR__, 2) . '/App/routes';
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath)
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) continue;
            if (!preg_match('/Controller\.php$/', $file->getFilename())) continue;

            $folderPrefix = '/' . strtolower(basename(dirname($file->getRealPath())));

            $class = $this->convertFileToClass($file->getRealPath());

            if (!class_exists($class)) {
                require_once $file->getRealPath();
            }

            if (!class_exists($class)) continue;

            $this->inspectController($class, $folderPrefix);
        }

        return $this->routes;
    }

    private function convertFileToClass(string $filePath): string
    {
        $relative = str_replace(dirname(__DIR__, 2) . '/', '', $filePath);
        return str_replace('/', '\\', preg_replace('/\.php$/', '', $relative));
    }

    private function inspectController(string $class, string $folderPrefix)
    {
        $reflection = new ReflectionClass($class);
        $controller = $this->container->get($class);

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
                        'controller' => $controller,
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
        $regex = rtrim($regex, '/'); // remove trailing slash
        return '#^' . $regex . '/?$#'; // allow optional trailing slash
    }

    private function extractParams(string $path): array
    {
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $path, $matches);
        return $matches[1];
    }
}
