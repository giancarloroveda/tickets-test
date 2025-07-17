<?php

namespace App;

class Router {
    private array $routes = [];

    public function addRoute(string $method, string $path, array $handler, array $middlewares = []) {
        $this->routes[] = new Route($method, $path, $handler, $middlewares);
    }

    public function dispatch(string $method, string $uri) {
        $uri = parse_url($uri, PHP_URL_PATH); // ignore query string

        foreach ($this->routes as $route) {
            if ($route->matches($method, $uri)) {
                $params = $route->extractParams($uri);
                $middlewareChain = $this->buildMiddlewareChain($route, $params);
                return $middlewareChain();
            }
        }

        http_response_code(404);
        echo json_encode(["error" => "Not Found"]);
    }

    private function buildMiddlewareChain(Route $route, array $params): callable {
        $middlewares = $route->getMiddlewares();
        $handler = $route->getHandler();

        $controller = new $handler[0];
        $action = $handler[1];

        $next = function () use ($controller, $action, $params) {
            return call_user_func_array([$controller, $action], $params);
        };

        foreach (array_reverse($middlewares) as $middlewareClass) {
            $middleware = new $middlewareClass;
            $next = function () use ($middleware, $next) {
                return $middleware->handle($next);
            };
        }

        return $next;
    }
}
