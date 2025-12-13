<?php
namespace App\Services;

/**
 * Base Service
 * 
 * Abstract base class for all services
 * Provides common business logic patterns
 */
abstract class BaseService {
    
    /**
     * Validate data against rules
     * 
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @return array Validation errors (empty if valid)
     */
    protected function validate(array $data, array $rules): array {
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
                    $min = (int)substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field][] = "{$field} must be at least {$min} characters";
                    }
                }
                
                if (strpos($rule, 'max:') === 0 && $value) {
                    $max = (int)substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field][] = "{$field} must not exceed {$max} characters";
                    }
                }
                
                if (strpos($rule, 'in:') === 0 && $value) {
                    $allowed = explode(',', substr($rule, 3));
                    if (!in_array($value, $allowed)) {
                        $errors[$field][] = "{$field} must be one of: " . implode(', ', $allowed);
                    }
                }
                
                if ($rule === 'numeric' && $value && !is_numeric($value)) {
                    $errors[$field][] = "{$field} must be numeric";
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Sanitize string input
     * 
     * @param string $input Input string
     * @return string Sanitized string
     */
    protected function sanitize(string $input): string {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitize array of data
     * 
     * @param array $data Data to sanitize
     * @param array $fields Fields to sanitize
     * @return array Sanitized data
     */
    protected function sanitizeData(array $data, array $fields): array {
        $sanitized = $data;
        
        foreach ($fields as $field) {
            if (isset($sanitized[$field]) && is_string($sanitized[$field])) {
                $sanitized[$field] = $this->sanitize($sanitized[$field]);
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Hash password
     * 
     * @param string $password Plain password
     * @return string Hashed password
     */
    protected function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Verify password
     * 
     * @param string $password Plain password
     * @param string $hash Hashed password
     * @return bool
     */
    protected function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    /**
     * Execute callback within a transaction
     * 
     * @param callable $callback Callback to execute
     * @param object $repository Repository with transaction methods
     * @return mixed Result of callback
     * @throws \Exception If transaction fails
     */
    protected function transaction(callable $callback, object $repository) {
        $repository->beginTransaction();
        
        try {
            $result = $callback();
            $repository->commit();
            return $result;
        } catch (\Exception $e) {
            $repository->rollback();
            throw $e;
        }
    }
    
    /**
     * Log error message
     * 
     * @param string $message Error message
     * @param array $context Additional context
     */
    protected function logError(string $message, array $context = []): void {
        error_log("[ERROR] {$message} " . json_encode($context));
    }
    
    /**
     * Log info message
     * 
     * @param string $message Info message
     * @param array $context Additional context
     */
    protected function logInfo(string $message, array $context = []): void {
        error_log("[INFO] {$message} " . json_encode($context));
    }
    
    /**
     * Format validation errors for response
     * 
     * @param array $errors Validation errors
     * @return string Formatted error message
     */
    protected function formatValidationErrors(array $errors): string {
        $messages = [];
        
        foreach ($errors as $field => $fieldErrors) {
            $messages[] = implode(', ', $fieldErrors);
        }
        
        return implode('; ', $messages);
    }
}
