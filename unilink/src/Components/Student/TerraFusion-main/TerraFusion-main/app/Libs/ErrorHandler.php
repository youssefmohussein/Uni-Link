<?php

namespace App\Libs;

/**
 * Error Handler Class
 * Handles logging and displaying errors
 */
class ErrorHandler
{
    private static string $logFile;
    
    public static function init(): void
    {
        self::$logFile = __DIR__ . '/../../logs/app.log';
        
        // Create logs directory if it doesn't exist
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Set error handler
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }
    
    /**
     * Handle PHP errors
     */
    public static function handleError(int $severity, string $message, string $file, int $line): bool
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        
        $error = [
            'type' => 'Error',
            'severity' => self::getSeverityName($severity),
            'message' => $message,
            'file' => $file,
            'line' => $line,
            'timestamp' => date('Y-m-d H:i:s'),
            'user_id' => $_SESSION['user_id'] ?? null,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        self::logError(json_encode($error));
        
        if (error_reporting() !== 0) {
            $appConfig = require __DIR__ . '/../config/app.php';
            if ($appConfig['debug']) {
                self::displayError($error);
            } else {
                self::displayGenericError();
            }
        }
        
        return true;
    }
    
    /**
     * Handle exceptions
     */
    public static function handleException(\Throwable $exception): void
    {
        $error = [
            'type' => 'Exception',
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => date('Y-m-d H:i:s'),
            'user_id' => $_SESSION['user_id'] ?? null,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        self::logError(json_encode($error));
        
        $appConfig = require __DIR__ . '/../config/app.php';
        if ($appConfig['debug']) {
            self::displayError($error);
        } else {
            self::displayGenericError();
        }
    }
    
    /**
     * Handle fatal errors
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $errorData = [
                'type' => 'Fatal Error',
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line'],
                'timestamp' => date('Y-m-d H:i:s'),
                'user_id' => $_SESSION['user_id'] ?? null,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ];
            
            self::logError(json_encode($errorData));
            self::displayGenericError();
        }
    }
    
    /**
     * Log error to file
     */
    public static function logError(string $message): void
    {
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logMessage = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Display error (debug mode)
     */
    private static function displayError(array $error): void
    {
        http_response_code(500);
        echo '<!DOCTYPE html><html><head><title>Error</title></head><body>';
        echo '<h1>Application Error</h1>';
        echo '<pre>' . htmlspecialchars(print_r($error, true)) . '</pre>';
        echo '</body></html>';
        exit;
    }
    
    /**
     * Display generic error (production mode)
     */
    private static function displayGenericError(): void
    {
        http_response_code(500);
        if (file_exists(__DIR__ . '/../../views/errors/500.php')) {
            require __DIR__ . '/../../views/errors/500.php';
        } else {
            echo '<!DOCTYPE html><html><head><title>Error</title></head><body>';
            echo '<h1>Something went wrong. Please try again later.</h1>';
            echo '</body></html>';
        }
        exit;
    }
    
    /**
     * Get severity name
     */
    private static function getSeverityName(int $severity): string
    {
        $severities = [
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        ];
        
        return $severities[$severity] ?? 'UNKNOWN';
    }
}

