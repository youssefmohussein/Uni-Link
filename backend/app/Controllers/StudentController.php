<?php
namespace App\Controllers;

use App\Services\UserService;

/**
 * Student Controller
 * 
 * Handles student-specific operations
 */
class StudentController extends BaseController {
    private UserService $userService;
    
    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }
    
    /**
     * Get all students
     */
    public function getAll(): void {
        try {
            $this->requireAuth();
            
            $pagination = $this->getPagination();
            $users = $this->userService->getAllUsers($pagination['limit'], $pagination['offset']);
            
            // Filter only students
            $students = array_filter($users, function($user) {
                return $user['role'] === 'Student';
            });
            
            $this->success([
                'count' => count($students),
                'data' => array_values($students)
            ]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Update student
     */
    public function update(): void {
        try {
            $this->requireAuth();
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['user_id']);
            
            $user = $this->userService->updateUser((int)$data['user_id'], $data);
            
            unset($user['password']);
            $this->success($user, 'Student updated successfully');
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
