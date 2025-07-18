<?php

namespace App;

use App\Router;

class App
{
    public function __construct(
        private readonly Router           $router
    ) {
    }

    public function route(
        string $method,
        string $path,
        array  $handler,
        array  $middlewares = []
    ) {
        $this->router->addRoute($method, $path, $handler, $middlewares);
    }

    public function handle(string $method, string $uri)
    {
        $this->router->dispatch($method, $uri);
    }
}
