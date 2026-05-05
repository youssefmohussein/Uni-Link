<?php

/**
 * Global Helper Functions
 */

if (!function_exists('csrf_token')) {
    /**
     * Generate CSRF token
     */
    function csrf_token(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate CSRF hidden input field
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('validate_csrf')) {
    /**
     * Validate CSRF token
     */
    function validate_csrf(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to URL
     */
    function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('url')) {
    /**
     * Generate URL
     */
    function url(string $path = ''): string
    {
        $appConfig = require __DIR__ . '/../config/app.php';
        $baseUrl = rtrim($appConfig['url'], '/');
        $path = ltrim($path, '/');
        return $baseUrl . ($path ? '/' . $path : '');
    }
}

if (!function_exists('asset')) {
    /**
     * Generate asset URL
     */
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('view')) {
    /**
     * Render a view
     */
    function view(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../../views/' . $view . '.php';
    }
}

if (!function_exists('auth')) {
    /**
     * Check if user is authenticated
     */
    function auth(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

if (!function_exists('user')) {
    /**
     * Get current user
     */
    function user(): ?object
    {
        if (!auth()) {
            return null;
        }
        
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if current user is admin
     */
    function is_admin(): bool
    {
        $user = user();
        return $user && ($user->role_name === 'admin' || $user->role_id == 1);
    }
}

if (!function_exists('is_staff')) {
    /**
     * Check if current user is staff
     */
    function is_staff(): bool
    {
        $user = user();
        return $user && ($user->role_name === 'staff' || $user->role_id == 2);
    }
}

if (!function_exists('is_customer')) {
    /**
     * Check if current user is customer
     */
    function is_customer(): bool
    {
        $user = user();
        return $user && ($user->role_name === 'customer' || $user->role_id == 3);
    }
}

if (!function_exists('has_role')) {
    /**
     * Check if user has specific role
     */
    function has_role(string $role): bool
    {
        $user = user();
        if (!$user) {
            return false;
        }
        
        return $user->role_name === $role;
    }
}

if (!function_exists('flash')) {
    /**
     * Set flash message
     */
    function flash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }
}

if (!function_exists('get_flash')) {
    /**
     * Get and clear flash message
     */
    function get_flash(string $type = null): ?string
    {
        if ($type) {
            if (isset($_SESSION['flash'][$type])) {
                $message = $_SESSION['flash'][$type];
                unset($_SESSION['flash'][$type]);
                return $message;
            }
            return null;
        }
        
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }
}

if (!function_exists('sanitize')) {
    /**
     * Sanitize input
     */
    function sanitize(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format currency
     */
    function format_currency(float $amount): string
    {
        return '$' . number_format($amount, 2);
    }
}

if (!function_exists('format_date')) {
    /**
     * Format date
     */
    function format_date(string $date, string $format = 'Y-m-d H:i:s'): string
    {
        return date($format, strtotime($date));
    }
}

if (!function_exists('old')) {
    /**
     * Get old input value
     */
    function old(string $key, mixed $default = ''): mixed
    {
        return $_SESSION['old_input'][$key] ?? $default;
    }
}

if (!function_exists('session_regenerate')) {
    /**
     * Regenerate session ID
     */
    function session_regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}

