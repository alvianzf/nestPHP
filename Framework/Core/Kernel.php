<?php

namespace Framework\Core;

class Kernel
{
    public function handle()
    {
        $container = new Container();
        $router = new Router($container);

        $router->dispatch();
    }
}
