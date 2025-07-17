<?php

require __DIR__ . '/vendor/autoload.php';

use App\App;
use App\Controllers\TicketsController;
use App\Middlewares\LoggingMiddleware;

$app = new App();

$app->route(
    "GET",
    "/tickets/:id",
    [TicketsController::class, "show"],
    [
        LoggingMiddleware::class
    ]
);

$app->handle($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
