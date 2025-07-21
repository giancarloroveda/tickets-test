<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/configureCors.php';

use App\App;
use App\Controllers\TasksController;
use App\Controllers\TicketsController;
use App\Database\Database;
use App\Middlewares\LoggingMiddleware;
use App\ServiceContainer;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

configureCors();

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
    "/tickets",
    [TicketsController::class, "index"],
    [
        LoggingMiddleware::class
    ]
);

$app->route(
    "GET",
    "/tickets/:id",
    [TicketsController::class, "show"],
    [
        LoggingMiddleware::class
    ]
);

$app->route(
    "POST",
    "/tickets",
    [TicketsController::class, "store"],
    [
        LoggingMiddleware::class
    ]
);

$app->route(
    "PUT",
    "/tickets/:id",
    [TicketsController::class, "update"],
    [
        LoggingMiddleware::class
    ]
);

$app->route(
    "DELETE",
    "/tickets/:id",
    [TicketsController::class, "destroy"],
    [
        LoggingMiddleware::class
    ]
);

$app->route(
    "GET",
    "/tasks",
    [TasksController::class, "index"],
    [
        LoggingMiddleware::class
    ]
);

$app->route(
    "GET",
    "/tasks/by-ticket/:ticketId",
    [TasksController::class, "tasksByTicket"],
    [
        LoggingMiddleware::class
    ]
);

$app->route(
    "GET",
    "/tasks/:id",
    [TasksController::class, "show"],
    [
        LoggingMiddleware::class
    ]
);

$app->route(
    "POST",
    "/tasks",
    [TasksController::class, "store"],
    [
        LoggingMiddleware::class
    ]
);

$app->route(
    "PUT",
    "/tasks/:id",
    [TasksController::class, "update"],
    [
        LoggingMiddleware::class
    ]
);

$app->route(
    "DELETE",
    "/tasks/:id",
    [TasksController::class, "destroy"],
    [
        LoggingMiddleware::class
    ]
);

$app->handle($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
