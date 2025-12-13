<?php
namespace App\Strategies\RoleAccess;

use App\Strategies\Interfaces\RoleAccessStrategyInterface;

/**
 * Admin Access Strategy
 * 
 * Implements full access for administrators
 * Strategy Pattern implementation for Admin role
 */
class AdminAccessStrategy implements RoleAccessStrategyInterface {
    
    /**
     * Admins have full access to all resources
     * 
     * @param int $userId User ID
     * @param string $resource Resource identifier
     * @param array $context Additional context
     * @return bool Always true for admins
     */
    public function canAccessResource(int $userId, string $resource, array $context = []): bool {
        // Admins have unrestricted access
        return true;
    }
    
    /**
     * Get admin permissions
     * 
     * @return array Full permissions
     */
    public function getPermissions(): array {
        return [
            'users' => ['create', 'read', 'update', 'delete'],
            'posts' => ['create', 'read', 'update', 'delete'],
            'projects' => ['create', 'read', 'update', 'delete', 'grade'],
            'rooms' => ['create', 'read', 'update', 'delete'],
            'announcements' => ['create', 'read', 'update', 'delete'],
            'skills' => ['create', 'read', 'update', 'delete'],
            'faculties' => ['create', 'read', 'update', 'delete'],
            'majors' => ['create', 'read', 'update', 'delete']
        ];
    }
    
    /**
     * Get role name
     * 
     * @return string
     */
    public function getRoleName(): string {
        return 'Admin';
    }
}
