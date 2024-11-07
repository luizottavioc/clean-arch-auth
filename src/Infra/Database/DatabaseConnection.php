<?php

declare(strict_types=1);

namespace App\Infra\Database;

use PDO;
use PDOException;
use RuntimeException;
use Dotenv\Dotenv;
use Exception;

class DatabaseConnection
{
    private PDO $connection;

    public function __construct()
    {
        if (empty($_ENV['DB_HOST'])) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
            $dotenv->safeLoad();
        }

        try {
            $dsn = sprintf(
                '%s:host=%s;port=%s;dbname=%s',
                $_ENV['DB_CONNECTION'],
                $_ENV['DB_HOST'],
                $_ENV['DB_PORT'],
                $_ENV['DB_DATABASE']
            );

            $username = $_ENV['DB_USERNAME'];
            $password = $_ENV['DB_PASSWORD'];

            $this->connection = new PDO($dsn, $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection error: ' . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
