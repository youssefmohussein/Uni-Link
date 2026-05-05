<?php

namespace App\Controllers;

/**
 * Base Controller
 * All controllers extend this
 */
abstract class BaseController
{
    /**
     * Render a view
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../../views/' . $view . '.php';
    }

    /**
     * Return JSON response
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect to URL
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Validate required fields
     */
    protected function validateRequired(array $data, array $required): array
    {
        $errors = [];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = ucfirst($field) . ' is required';
            }
        }
        return $errors;
    }

    /**
     * Sanitize input
     */
    protected function sanitize(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }
}

