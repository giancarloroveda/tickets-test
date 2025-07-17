<?php

namespace App\Middlewares;

use App\Interfaces\MiddlewareInterface;

class LoggingMiddleware implements MiddlewareInterface
{
    public function handle(callable $next)
    {
        // do logging logic

        return $next();
    }
}
