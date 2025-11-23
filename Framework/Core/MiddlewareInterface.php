<?php

namespace Framework\Core;

interface MiddlewareInterface
{
    public function handle(array $request, callable $next);
}
