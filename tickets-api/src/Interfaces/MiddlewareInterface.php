<?php

namespace App\Interfaces;

interface MiddlewareInterface
{
    public function handle(callable $next);
}
