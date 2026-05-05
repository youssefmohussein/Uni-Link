<?php

namespace App\Libs;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $config = require __DIR__ . '/../config/database.php';

        $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
        
        try {
            $this->connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options'] ?? []
            );
        } catch (PDOException $e) {
            throw new RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    // Prevent cloning of the instance
    private function __clone() {}

    // Prevent unserializing of the instance
    public function __wakeup()
    {
        throw new RuntimeException("Cannot unserialize database connection");
    }
}
