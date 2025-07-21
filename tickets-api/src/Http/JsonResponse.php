<?php

namespace App\Http;

class JsonResponse
{
    public static function success(
        $data = null,
        string $message = 'Success',
        int $statusCode = 200
    ): void {
        self::send(
            [
                'success' => true,
                'message' => $message,
                'data' => $data
            ],
            $statusCode
        );
    }

    public static function error(
        string $message = 'An error occurred',
        int    $statusCode = 400,
               $errors = null
    ): void {
        self::send(
            [
                'success' => false,
                'message' => $message,
                'errors' => $errors
            ],
            $statusCode
        );
    }

    private static function send(array $payload, int $statusCode): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }
}
