<?php

namespace App;

class Route
{
    public function __construct(
        private string $method,
        private string $path,
        private array  $handler,
        private array  $middlewares = []
    ) {
    }

    public function matches(string $method, string $uri): bool
    {
        if (strtoupper($method) !== strtoupper($this->method)) {
            return false;
        }

        $pattern = preg_replace('#:([\w]+)#', '([^/]+)', $this->path);
        $pattern = '#^' . $pattern . '$#';

        return preg_match($pattern, $uri);
    }

    public function extractParams(string $uri): array
    {
        $paramNames = [];
        preg_match_all('/:([\w]+)/', $this->path, $matches);
        $paramNames = $matches[1];

        $pattern = preg_replace('#:([\w]+)#', '([^/]+)', $this->path);
        $pattern = '#^' . $pattern . '$#';

        preg_match($pattern, $uri, $matches);
        array_shift($matches); // remove full match

        return array_combine($paramNames, $matches);
    }

    public function getHandler(): array
    {
        return $this->handler;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}
