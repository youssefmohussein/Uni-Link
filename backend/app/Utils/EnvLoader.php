<?php
namespace App\Utils;

/**
 * Environment Variable Loader
 * 
 * Loads environment variables from .env file
 */
class EnvLoader {
    /**
     * Load environment variables from file
     * 
     * @param string $path Path to .env file
     * @return array Associative array of environment variables
     */
    public static function load(string $path): array {
        if (!file_exists($path)) {
            throw new \Exception(".env file not found at: {$path}");
        }
        
        $env = [];
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse key=value
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $env[trim($key)] = trim($value);
            }
        }
        
        return $env;
    }
}
