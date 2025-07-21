<?php

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    public static function connect(): PDO
    {
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASSWORD'];

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

        try {
            return new PDO(
                $dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(
                [
                    "error" => "DB Connection Failed",
                    "details" => $e->getMessage()
                ]
            );
            exit;
        }
    }
}
