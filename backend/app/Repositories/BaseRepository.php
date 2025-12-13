<?php
namespace App\Repositories;

use App\Utils\Database;
use PDO;

/**
 * Base Repository
 * 
 * Abstract base class for all repositories
 * Provides common CRUD operations and transaction management
 */
abstract class BaseRepository {
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected bool $softDelete = false;
    protected string $deletedAtColumn = 'deleted_at';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Find record by ID
     * 
     * @param int $id Record ID
     * @param bool $includeSoftDeleted Include soft deleted records
     * @return array|null Record data or null if not found
     */
    public function find(int $id, bool $includeSoftDeleted = false): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        
        if ($this->softDelete && !$includeSoftDeleted) {
            $sql .= " AND {$this->deletedAtColumn} IS NULL";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    /**
     * Find records by field value
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @return array Array of records
     */
    public function findBy(string $field, $value): array {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = ?";
        
        if ($this->softDelete) {
            $sql .= " AND {$this->deletedAtColumn} IS NULL";
        }
        
        return $this->query($sql, [$value]);
    }
    
    /**
     * Find one record by field value
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @return array|null Record or null
     */
    public function findOneBy(string $field, $value): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = ? LIMIT 1";
        
        if ($this->softDelete) {
            $sql .= " AND {$this->deletedAtColumn} IS NULL";
        }
        
        return $this->queryOne($sql, [$value]);
    }
    
    /**
     * Find all records
     * 
     * @param int|null $limit Maximum number of records
     * @param int $offset Offset for pagination
     * @param string $orderBy Order by clause
     * @return array Array of records
     */
    public function findAll(?int $limit = null, int $offset = 0, string $orderBy = ''): array {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($this->softDelete) {
            $sql .= " WHERE {$this->deletedAtColumn} IS NULL";
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit !== null) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Find records with WHERE conditions
     * 
     * @param array $conditions Key-value pairs for WHERE clause
     * @param string $orderBy Order by clause
     * @param int|null $limit Limit
     * @param int $offset Offset
     * @return array Array of records
     */
    public function findWhere(array $conditions, string $orderBy = '', ?int $limit = null, int $offset = 0): array {
        $where = [];
        $params = [];
        
        foreach ($conditions as $field => $value) {
            $where[] = "{$field} = ?";
            $params[] = $value;
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where);
        
        if ($this->softDelete) {
            $sql .= " AND {$this->deletedAtColumn} IS NULL";
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit !== null) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        
        return $this->query($sql, $params);
    }
    
    /**
     * Create new record
     * 
     * @param array $data Record data
     * @return int Inserted record ID
     */
    public function create(array $data): int {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        
        return (int)$this->db->lastInsertId();
    }
    
    /**
     * Create multiple records in batch
     * 
     * @param array $records Array of records to insert
     * @return bool Success status
     */
    public function createBatch(array $records): bool {
        if (empty($records)) {
            return true;
        }
        
        $this->beginTransaction();
        
        try {
            foreach ($records as $record) {
                $this->create($record);
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    /**
     * Update record
     * 
     * @param int $id Record ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update(int $id, array $data): bool {
        $fields = array_keys($data);
        $setClause = implode(' = ?, ', $fields) . ' = ?';
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";
        
        $values = array_values($data);
        $values[] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Update records matching conditions
     * 
     * @param array $conditions WHERE conditions
     * @param array $data Data to update
     * @return int Number of affected rows
     */
    public function updateWhere(array $conditions, array $data): int {
        $setFields = array_keys($data);
        $setClause = implode(' = ?, ', $setFields) . ' = ?';
        
        $whereFields = array_keys($conditions);
        $whereClause = implode(' = ? AND ', $whereFields) . ' = ?';
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$whereClause}";
        
        $params = array_merge(array_values($data), array_values($conditions));
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->rowCount();
    }
    
    /**
     * Delete record (soft or hard delete based on configuration)
     * 
     * @param int $id Record ID
     * @return bool Success status
     */
    public function delete(int $id): bool {
        if ($this->softDelete) {
            return $this->update($id, [$this->deletedAtColumn => date('Y-m-d H:i:s')]);
        }
        
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Permanently delete record (even if soft delete is enabled)
     * 
     * @param int $id Record ID
     * @return bool Success status
     */
    public function forceDelete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Restore soft deleted record
     * 
     * @param int $id Record ID
     * @return bool Success status
     */
    public function restore(int $id): bool {
        if (!$this->softDelete) {
            return false;
        }
        
        return $this->update($id, [$this->deletedAtColumn => null]);
    }
    
    /**
     * Count total records
     * 
     * @param array $conditions Optional WHERE conditions
     * @return int Total count
     */
    public function count(array $conditions = []): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        if ($this->softDelete) {
            $sql .= empty($conditions) ? " WHERE " : " AND ";
            $sql .= "{$this->deletedAtColumn} IS NULL";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }
    
    /**
     * Check if record exists
     * 
     * @param int $id Record ID
     * @return bool
     */
    public function exists(int $id): bool {
        $sql = "SELECT 1 FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        
        if ($this->softDelete) {
            $sql .= " AND {$this->deletedAtColumn} IS NULL";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Begin database transaction
     */
    public function beginTransaction(): bool {
        return $this->db->beginTransaction();
    }
    
    /**
     * Commit database transaction
     */
    public function commit(): bool {
        return $this->db->commit();
    }
    
    /**
     * Rollback database transaction
     */
    public function rollback(): bool {
        return $this->db->rollBack();
    }
    
    /**
     * Execute raw query
     * 
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @return array Query results
     */
    protected function query(string $sql, array $params = []): array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Execute raw query and return single row
     * 
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @return array|null Single row or null
     */
    protected function queryOne(string $sql, array $params = []): ?array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    /**
     * Execute raw query (for INSERT, UPDATE, DELETE)
     * 
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @return int Number of affected rows
     */
    protected function execute(string $sql, array $params = []): int {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
}
