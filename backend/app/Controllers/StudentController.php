<?php
namespace App\Controllers;

use App\Services\UserService;

/**
 * Student Controller
 * 
 * Handles student-specific operations
 */
class StudentController extends BaseController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get all students
     */
    public function getAll(): void
    {
        try {
            $this->requireAuth();

            $pagination = $this->getPagination();
            $students = $this->userService->getUsersByRole('STUDENT', $pagination['limit'], $pagination['offset']);

            // Force role to uppercase to satisfy strict frontend requirements
            $students = array_map(function ($student) {
                $student['role'] = strtoupper($student['role']);
                return $student;
            }, $students);

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
    public function update(): void
    {
        try {
            $this->requireAuth();

            $data = $this->getJsonInput();
            $this->validateRequired($data, ['user_id']);

            $user = $this->userService->updateUser((int) $data['user_id'], $data);

            unset($user['password_hash']);
            $this->success($user, 'Student updated successfully');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    /**
     * Get leaderboard
     */
    public function getLeaderboard(): void
    {
        try {
            $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
            $students = $this->userService->getLeaderboard($limit);

            $this->success([
                'count' => count($students),
                'data' => $students
            ]);

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
