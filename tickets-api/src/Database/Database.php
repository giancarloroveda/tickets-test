<?php

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    public static function connect(): PDO
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $dbname = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASSWORD');

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
