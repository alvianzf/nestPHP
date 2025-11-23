<?php

namespace Framework\Core;

class Kernel 
{
    public function handle()
    {
        $router = new Router();
        $router->dispatch();
    }
}
