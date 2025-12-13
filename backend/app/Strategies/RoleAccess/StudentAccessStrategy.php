<?php
namespace App\Strategies\RoleAccess;

use App\Strategies\Interfaces\RoleAccessStrategyInterface;

/**
 * Student Access Strategy
 * 
 * Implements student-specific access rules
 * Strategy Pattern implementation for Student role
 */
class StudentAccessStrategy implements RoleAccessStrategyInterface {
    
    /**
     * Check student access to resources
     * 
     * @param int $userId User ID
     * @param string $resource Resource identifier
     * @param array $context Additional context
     * @return bool Access granted or denied
     */
    public function canAccessResource(int $userId, string $resource, array $context = []): bool {
        switch ($resource) {
            case 'post_edit':
            case 'post_delete':
            case 'project_edit':
            case 'project_delete':
                // Can only edit/delete own content
                return isset($context['owner_id']) && $context['owner_id'] === $userId;
                
            case 'cv_upload':
            case 'cv_download_own':
                // Can manage own CV
                return isset($context['user_id']) && $context['user_id'] === $userId;
                
            case 'room_join':
            case 'room_create':
                // Students can create and join rooms
                return true;
                
            case 'room_edit':
            case 'room_delete':
                // Can only manage own rooms
                return isset($context['owner_id']) && $context['owner_id'] === $userId;
                
            case 'user_edit':
                // Can only edit own profile
                return isset($context['user_id']) && $context['user_id'] === $userId;
                
            default:
                return false;
        }
    }
    
    /**
     * Get student permissions
     * 
     * @return array Student permissions
     */
    public function getPermissions(): array {
        return [
            'posts' => ['create', 'read', 'update_own', 'delete_own', 'interact'],
            'projects' => ['create', 'read', 'update_own', 'delete_own'],
            'rooms' => ['create', 'read', 'join', 'update_own', 'delete_own'],
            'cv' => ['upload', 'download_own'],
            'skills' => ['read', 'add_to_profile'],
            'users' => ['read', 'update_own']
        ];
    }
    
    /**
     * Get role name
     * 
     * @return string
     */
    public function getRoleName(): string {
        return 'Student';
    }
}
