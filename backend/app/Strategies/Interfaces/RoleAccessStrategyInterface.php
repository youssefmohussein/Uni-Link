<?php
namespace App\Strategies\Interfaces;

/**
 * Role-Based Access Control Strategy Interface
 * 
 * Defines the contract for role-specific access strategies
 * Implements Strategy Pattern for authorization
 */
interface RoleAccessStrategyInterface {
    /**
     * Check if user can access a specific resource
     * 
     * @param int $userId User ID
     * @param string $resource Resource identifier
     * @param array $context Additional context (e.g., owner_id, resource_id)
     * @return bool Access granted or denied
     */
    public function canAccessResource(int $userId, string $resource, array $context = []): bool;
    
    /**
     * Get all permissions for this role
     * 
     * @return array Permissions array
     */
    public function getPermissions(): array;
    
    /**
     * Get role name
     * 
     * @return string Role name
     */
    public function getRoleName(): string;
}
