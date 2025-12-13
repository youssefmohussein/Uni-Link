<?php
namespace App\Utils;

/**
 * Input Validation Utility
 * 
 * Provides common validation methods
 */
class Validator {
    /**
     * Validate required fields in data array
     * 
     * @param array $data Input data
     * @param array $requiredFields List of required field names
     * @return array Missing fields (empty if all present)
     */
    public static function validateRequired(array $data, array $requiredFields): array {
        $missing = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                $missing[] = $field;
            }
        }
        
        return $missing;
    }
    
    /**
     * Validate email format
     * 
     * @param string $email Email address
     * @return bool
     */
    public static function isValidEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate enum value
     * 
     * @param mixed $value Value to check
     * @param array $allowedValues Allowed values
     * @return bool
     */
    public static function isValidEnum($value, array $allowedValues): bool {
        return in_array($value, $allowedValues, true);
    }
    
    /**
     * Sanitize string input
     * 
     * @param string $input Input string
     * @return string Sanitized string
     */
    public static function sanitizeString(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate integer range
     * 
     * @param int $value Value to check
     * @param int $min Minimum value
     * @param int $max Maximum value
     * @return bool
     */
    public static function isInRange(int $value, int $min, int $max): bool {
        return $value >= $min && $value <= $max;
    }
}
