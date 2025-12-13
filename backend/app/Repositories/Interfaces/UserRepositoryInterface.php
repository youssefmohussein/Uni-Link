<?php
namespace App\Repositories\Interfaces;

use App\Interfaces\RepositoryInterface;

/**
 * User Repository Interface
 * 
 * Extends base repository with user-specific methods
 */
interface UserRepositoryInterface extends RepositoryInterface {
    /**
     * Find user by email
     * 
     * @param string $email Email address
     * @return array|null User data or null
     */
    public function findByEmail(string $email): ?array;
    
    /**
     * Find user by username
     * 
     * @param string $username Username
     * @return array|null User data or null
     */
    public function findByUsername(string $username): ?array;
    
    /**
     * Find user by email or username
     * 
     * @param string $identifier Email or username
     * @return array|null User data or null
     */
    public function findByEmailOrUsername(string $identifier): ?array;
    
    /**
     * Get user with role-specific data
     * 
     * @param int $id User ID
     * @return array|null Complete user data with role info
     */
    public function findWithRoleData(int $id): ?array;
}
