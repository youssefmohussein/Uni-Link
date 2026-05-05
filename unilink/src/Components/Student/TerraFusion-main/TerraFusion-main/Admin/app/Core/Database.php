<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $pdo;

    private $host;
    private $db;
    private $user;
    private $pass;
    private $charset = 'utf8mb4';

    private function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';
        $this->host = $config['host'];
        $this->db   = $config['dbname'];
        $this->user = $config['user'];
        $this->pass = $config['password'];

        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            // In production, handle this gracefully
            die('Database Connection Failed: ' . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
