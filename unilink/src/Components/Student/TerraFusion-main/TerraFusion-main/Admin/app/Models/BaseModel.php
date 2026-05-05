<?php

namespace App\Models;

use PDO;
use App\Core\Database;

// Explicitly require the Database file in case autoloader is missing 'App\Core' mapping
require_once __DIR__ . '/../Core/Database.php';

abstract class BaseModel
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id'; // Default PK

    public function __construct()
    {
        // Database class is now guaranteed to be loaded
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute(['id' => $id]);
    }
}
