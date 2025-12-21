<?php
namespace App\Controllers;

use App\Services\UserService;

/**
 * Admin Controller
 * 
 * Handles admin-specific operations
 */
class AdminController extends BaseController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get all admins
     */
    public function getAll(): void
    {
        try {
            $this->requireRole('Admin');

            $pagination = $this->getPagination();
            $admins = $this->userService->getUsersByRole('ADMIN', $pagination['limit'], $pagination['offset']);

            $this->success([
                'count' => count($admins),
                'data' => array_values($admins)
            ]);

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }

    /**
     * Update admin
     */
    public function update(): void
    {
        try {
            $this->requireRole('Admin');

            $data = $this->getJsonInput();
            $this->validateRequired($data, ['user_id']);

            $user = $this->userService->updateUser((int) $data['user_id'], $data);

            unset($user['password_hash']);
            $this->success($user, 'Admin updated successfully');

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }
}
