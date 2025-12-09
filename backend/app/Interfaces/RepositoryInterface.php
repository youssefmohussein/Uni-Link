<?php
namespace App\Interfaces;

/**
 * Base Repository Interface
 * 
 * Defines standard CRUD operations for all repositories
 */
interface RepositoryInterface {
    /**
     * Find a record by ID
     * 
     * @param int $id Record ID
     * @return array|null Record data or null if not found
     */
    public function find(int $id): ?array;
    
    /**
     * Find all records
     * 
     * @return array Array of records
     */
    public function findAll(): array;
    
    /**
     * Create a new record
     * 
     * @param array $data Record data
     * @return int Created record ID
     */
    public function create(array $data): int;
    
    /**
     * Update an existing record
     * 
     * @param int $id Record ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update(int $id, array $data): bool;
    
    /**
     * Delete a record
     * 
     * @param int $id Record ID
     * @return bool Success status
     */
    public function delete(int $id): bool;
}
