<?php
namespace App\Controllers;

use App\Services\UserService;

/**
 * Admin Controller
 * 
 * Handles admin-specific operations
 */
class AdminController extends BaseController {
    private UserService $userService;
    
    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }
    
    /**
     * Get all admins
     */
    public function getAll(): void {
        try {
            $this->requireRole('Admin');
            
            $pagination = $this->getPagination();
            $users = $this->userService->getAllUsers($pagination['limit'], $pagination['offset']);
            
            // Filter only admins
            $admins = array_filter($users, function($user) {
                return $user['role'] === 'Admin';
            });
            
            $this->success([
                'count' => count($admins),
                'data' => array_values($admins)
            ]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Update admin
     */
    public function update(): void {
        try {
            $this->requireRole('Admin');
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['user_id']);
            
            $user = $this->userService->updateUser((int)$data['user_id'], $data);
            
            unset($user['password']);
            $this->success($user, 'Admin updated successfully');
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
