<?php

namespace App\Repositories;

interface RepositoryInterface
{
    /**
     * Find by ID
     */
    public function find(int $id): ?object;

    /**
     * Get all records
     */
    public function all(): array;

    /**
     * Create a new record
     */
    public function create(array $data): object;

    /**
     * Update a record
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a record
     */
    public function delete(int $id): bool;
}

