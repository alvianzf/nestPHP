<?php

namespace Framework\Core;

class Kernel
{
    public function handle()
    {
        $router = new Router();
        $router->dispatch();


    }

    private array $global = [];

    public function addGlobalMiddleware(string $class)
    {
        $this->global[] = $class;
    }

    public function handle(array $middlewares, callable $controller)
    {
        $stack = array_merge($this->global, $middlewares);

        $pipeline = array_reduce(
            array_reverse($stack),
            fn($next, $middlewareClass) => function ($req) use ($middlewareClass, $next) {
                $mw = new $middlewareClass();
                return $mw->handle($req, $next);
            },
            $controller
        );

        return $pipeline($_REQUEST);
    }
}
