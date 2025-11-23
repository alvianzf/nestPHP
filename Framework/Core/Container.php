<?php

namespace Framework\Core;

use ReflectionClass;

class Container
{
  private array $singletons = [];

  public function get(string $class)
  {
    // Return singleton if exists
    if (isset($this->singletons[$class])) {
      return $this->singletons[$class];
    }

    // Build using reflection
    $object = $this->resolve($class);

    // Store as singleton
    $this->singletons[$class] = $object;

    return $object;
  }

  private function resolve(string $class)
  {
    $reflection = new ReflectionClass($class);

    $constructor = $reflection->getConstructor();

    if (!$constructor) {
      return new $class();
    }

    $dependencies = [];

    foreach ($constructor->getParameters() as $param) {

      $type = $param->getType();

      // If type missing or not a class
      if (!$type instanceof \ReflectionNamedType || $type->isBuiltin()) {
        throw new \Exception(
          "Cannot resolve dependency '{$param->getName()}' in {$class}"
        );
      }

      // Resolve recursively
      $dependencies[] = $this->get($type->getName());
    }

    return $reflection->newInstanceArgs($dependencies);
  }

}
