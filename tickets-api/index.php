<?php

require __DIR__ . '/vendor/autoload.php';

use App\App;
use App\Controllers\TicketsController;
use App\Database\Database;
use App\Middlewares\LoggingMiddleware;
use App\ServiceContainer;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$databaseConnection = (new Database())->connect();

$container = new ServiceContainer();

$container->bind(
    PDO::class,
    fn() => $databaseConnection
);

$router = new \App\Router($container);
$app = new App($router);

$app->route(
    "GET",
    "/tickets/:id",
    [TicketsController::class, "show"],
    [
        LoggingMiddleware::class
    ]
);

$app->handle($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
