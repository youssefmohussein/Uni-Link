<?php
namespace App\Controllers;

use App\Services\UserService;
/**
 * User Controller
 * 
 * Handles user management operations
 */
class UserController extends BaseController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Create new user
     */
    public function create(): void
    {
        try {
            $this->requireRole('Admin');

            $data = $this->getJsonInput();
            $user = $this->userService->createUser($data);

            unset($user['password_hash']);
            $this->success($user, 'User created successfully', 201);

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }

    /**
     * Update user
     */
    public function update(): void
    {
        try {
            $this->requireRole('Admin');

            $data = $this->getJsonInput();
            $this->validateRequired($data, ['user_id']);

            $user = $this->userService->updateUser((int) $data['user_id'], $data);

            unset($user['password_hash']);
            $this->success($user, 'User updated successfully');

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }

    /**
     * Delete user
     */
    public function delete(): void
    {
        try {
            $this->requireRole('Admin');

            $data = $this->getJsonInput();

            // Allow user_id from GET for DELETE requests where body might be dropped
            if ((!isset($data['user_id']) || $data['user_id'] === '') && isset($_GET['user_id'])) {
                $data['user_id'] = $_GET['user_id'];
            }

            $this->validateRequired($data, ['user_id']);

            $this->userService->deleteUser((int) $data['user_id']);
            $this->success(null, 'User deleted successfully');

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }

    /**
     * Get all users
     */
    public function getAll(): void
    {
        try {
            $this->requireRole('Admin');

            $pagination = $this->getPagination();
            $users = $this->userService->getAllUsers($pagination['limit'], $pagination['offset']);

            $this->success([
                'count' => count($users),
                'data' => $users
            ]);

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }

    /**
     * Get user profile
     */
    public function getProfile(): void
    {
        try {
            $userId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : $this->getCurrentUserId();

            if (!$userId) {
                throw new \Exception('User ID is required', 400);
            }

            $profile = $this->userService->getUserProfile($userId);
            $this->success($profile);

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }
}
