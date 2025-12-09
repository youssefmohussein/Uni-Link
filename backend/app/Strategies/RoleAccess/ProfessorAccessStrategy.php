<?php
namespace App\Strategies\RoleAccess;

use App\Strategies\Interfaces\RoleAccessStrategyInterface;

/**
 * Professor Access Strategy
 * 
 * Implements professor-specific access rules
 * Strategy Pattern implementation for Professor role
 */
class ProfessorAccessStrategy implements RoleAccessStrategyInterface {
    
    /**
     * Check professor access to resources
     * 
     * @param int $userId User ID
     * @param string $resource Resource identifier
     * @param array $context Additional context
     * @return bool Access granted or denied
     */
    public function canAccessResource(int $userId, string $resource, array $context = []): bool {
        switch ($resource) {
            case 'project_grade':
            case 'project_review':
                // Professors can grade all projects
                return true;
                
            case 'post_edit':
            case 'post_delete':
                // Can only edit/delete own posts
                return isset($context['author_id']) && $context['author_id'] === $userId;
                
            case 'room_edit':
            case 'room_delete':
                // Can only edit/delete own rooms
                return isset($context['owner_id']) && $context['owner_id'] === $userId;
                
            case 'announcement_create':
                // Professors can create announcements
                return true;
                
            case 'user_edit':
                // Can only edit own profile
                return isset($context['user_id']) && $context['user_id'] === $userId;
                
            default:
                return false;
        }
    }
    
    /**
     * Get professor permissions
     * 
     * @return array Professor permissions
     */
    public function getPermissions(): array {
        return [
            'posts' => ['create', 'read', 'update_own', 'delete_own'],
            'projects' => ['read', 'grade', 'comment', 'review'],
            'rooms' => ['create', 'read', 'update_own', 'delete_own', 'join'],
            'announcements' => ['create', 'read'],
            'users' => ['read', 'update_own']
        ];
    }
    
    /**
     * Get role name
     * 
     * @return string
     */
    public function getRoleName(): string {
        return 'Professor';
    }
}
