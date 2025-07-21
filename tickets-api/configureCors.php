<?php

function configureCors(): void
{
    $corsConfig = require __DIR__ . '/src/Config/cors.php';

    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    if (in_array($origin, $corsConfig['allowedOrigins'])) {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Credentials: true");
    }

    header(
        "Access-Control-Allow-Methods: " . implode(
            ',',
            $corsConfig['allowedMethods']
        )
    );
    header(
        "Access-Control-Allow-Headers: " . implode(
            ',',
            $corsConfig['allowedHeaders']
        )
    );
    header(
        "Access-Control-Expose-Headers: " . implode(
            ',',
            $corsConfig['exposedHeaders']
        )
    );
    header("Access-Control-Max-Age: " . $corsConfig['maxAge']);

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}