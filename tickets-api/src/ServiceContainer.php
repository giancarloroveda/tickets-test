<?php

namespace App;

class ServiceContainer
{
    private array $bindings = [];

    public function bind(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = $factory;
    }

    public function resolve(string $class)
    {
        if (isset($this->bindings[$class])) {
            return ($this->bindings[$class])($this);
        }

        $reflector = new ReflectionClass($class);

        if (!$reflector->isInstantiable()) {
            throw new Exception("Class {$class} is not instantiable.");
        }

        $constructor = $reflector->getConstructor();
        if (!$constructor) {
            return new $class;
        }

        $params = $constructor->getParameters();
        $dependencies = [];

        foreach ($params as $param) {
            $type = $param->getType();
            if ($type && !$type->isBuiltin()) {
                $dependencies[] = $this->resolve($type->getName());
            } else {
                throw new Exception(
                    "Cannot resolve class dependency {$param->name}"
                );
            }
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}
