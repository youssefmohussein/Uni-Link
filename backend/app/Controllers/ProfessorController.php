<?php
namespace App\Controllers;

use App\Services\UserService;

/**
 * Professor Controller
 * 
 * Handles professor-specific operations
 */
class ProfessorController extends BaseController {
    private UserService $userService;
    
    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }
    
    /**
     * Get all professors
     */
    public function getAll(): void {
        try {
            $this->requireAuth();
            
            $pagination = $this->getPagination();
            $users = $this->userService->getAllUsers($pagination['limit'], $pagination['offset']);
            
            // Filter only professors
            $professors = array_filter($users, function($user) {
                return $user['role'] === 'Professor';
            });
            
            $this->success([
                'count' => count($professors),
                'data' => array_values($professors)
            ]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
