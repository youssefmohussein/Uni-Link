<?php
namespace App\Controllers;

use App\Utils\ResponseHandler;

/**
 * Base Controller
 * 
 * Abstract base class for all controllers
 * Provides common functionality for request handling and validation
 */
abstract class BaseController
{

    /**
     * Validate required fields in request data
     * 
     * @param array $data Request data
     * @param array $requiredFields List of required field names
     * @throws \Exception If validation fails
     */
    protected function validateRequired(array $data, array $requiredFields): void
    {
        $missing = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            throw new \Exception('Missing required fields: ' . implode(', ', $missing), 400);
        }
    }

    /**
     * Validate array of data against rules
     * 
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @return array Validation errors (empty if valid)
     */
    protected function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && ($value === null || $value === '')) {
                    $errors[$field][] = "{$field} is required";
                }

                if ($rule === 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "{$field} must be a valid email";
                }

                if (strpos($rule, 'min:') === 0 && $value) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field][] = "{$field} must be at least {$min} characters";
                    }
                }

                if (strpos($rule, 'max:') === 0 && $value) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field][] = "{$field} must not exceed {$max} characters";
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Get JSON input from request body
     * 
     * @return array Decoded JSON data
     */
    protected function getJsonInput(): array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON input', 400);
        }

        return $data ?? [];
    }

    /**
     * Get pagination parameters from request
     * 
     * @param int $defaultLimit Default items per page
     * @return array ['limit' => int, 'offset' => int, 'page' => int]
     */
    protected function getPagination(int $defaultLimit = 20): array
    {
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? max(1, min(100, (int) $_GET['limit'])) : $defaultLimit;
        $offset = ($page - 1) * $limit;

        return [
            'limit' => $limit,
            'offset' => $offset,
            'page' => $page
        ];
    }

    /**
     * Handle file upload
     * 
     * @param string $fieldName Form field name
     * @param string $uploadDir Upload directory
     * @param array $allowedTypes Allowed MIME types
     * @param int $maxSize Max file size in bytes
     * @return string Uploaded file path
     * @throws \Exception If upload fails
     */
    protected function handleFileUpload(
        string $fieldName,
        string $uploadDir,
        array $allowedTypes = [],
        int $maxSize = 5242880 // 5MB default
    ): string {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('File upload failed', 400);
        }

        $file = $_FILES[$fieldName];

        // Check file size
        if ($file['size'] > $maxSize) {
            throw new \Exception('File size exceeds maximum allowed size', 400);
        }

        // Check file type
        if (!empty($allowedTypes) && !in_array($file['type'], $allowedTypes)) {
            throw new \Exception('File type not allowed', 400);
        }

        // Create upload directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . '/' . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new \Exception('Failed to save uploaded file', 500);
        }

        return $filepath;
    }

    /**
     * Get current authenticated user ID from session
     * 
     * @return int|null User ID or null if not authenticated
     */
    protected function getCurrentUserId(): ?int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check for both 'id' and 'user_id' for robustness across different session structures
        return $_SESSION['user']['id'] ?? $_SESSION['user']['user_id'] ?? null;
    }

    /**
     * Get current authenticated user data from session
     * 
     * @return array|null User data or null if not authenticated
     */
    protected function getCurrentUser(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION['user'] ?? null;
    }

    /**
     * Verify that the current session user actually exists in the database
     * 
     * @return bool True if user exists, false otherwise
     */
    protected function verifyUserExists(): bool
    {
        $userId = $this->getCurrentUserId();
        if (!$userId)
            return false;

        try {
            $stmt = \App\Utils\Database::getInstance()->getConnection()->prepare("SELECT 1 FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            if (!$stmt->fetch()) {
                // User ID in session does not exist in database (stale session)
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                session_unset();
                session_destroy();
                return false;
            }
            return true;
        } catch (\Exception $e) {
            // Database error - assume user exists to avoid accidental logout during db downtime,
            // or log error and continue. For now, we'll just log it.
            error_log("Database error in verifyUserExists: " . $e->getMessage());
            return true;
        }
    }

    /**
     * Require authentication
     * 
     * @throws \Exception If user is not authenticated
     */
    protected function requireAuth(): void
    {
        $userId = $this->getCurrentUserId();

        if (!$userId || !$this->verifyUserExists()) {
            throw new \Exception('Authentication required or session invalid. Please log in again.', 401);
        }
    }

    /**
     * Check if user has specific role
     * 
     * @param string $role Required role
     * @return bool
     */
    protected function hasRole(string $role): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return ($_SESSION['user']['role'] ?? null) === $role;
    }

    /**
     * Require specific role
     * 
     * @param string $role Required role
     * @throws \Exception If user doesn't have required role
     */
    protected function requireRole(string $role): void
    {
        $this->requireAuth();

        if (!$this->hasRole($role)) {
            throw new \Exception('Insufficient permissions', 403);
        }
    }

    /**
     * Send success response
     * 
     * @param mixed $data Response data
     * @param string $message Success message
     * @param int $code HTTP status code
     */
    protected function success($data = null, string $message = 'Success', int $code = 200): void
    {
        ResponseHandler::success($data, $message, $code);
    }

    /**
     * Send error response
     * 
     * @param string $message Error message
     * @param int $code HTTP status code
     */
    protected function error(string $message, int $code = 400): void
    {
        ResponseHandler::error($message, $code);
    }

    /**
     * Send paginated response
     * 
     * @param array $data Response data
     * @param int $total Total count
     * @param int $page Current page
     * @param int $limit Items per page
     */
    protected function paginatedSuccess(array $data, int $total, int $page, int $limit): void
    {
        $totalPages = ceil($total / $limit);

        ResponseHandler::success([
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'totalPages' => $totalPages,
                'hasNext' => $page < $totalPages,
                'hasPrev' => $page > 1
            ]
        ]);
    }
}
