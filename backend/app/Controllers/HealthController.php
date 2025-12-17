<?php
namespace App\Controllers;

use App\Utils\Database;
use App\Utils\ResponseHandler;

/**
 * Health Controller
 * 
 * Provides health check endpoints for monitoring system status
 */
class HealthController {
    /**
     * Health check endpoint
     * GET /health
     * 
     * Checks database connectivity and system health
     */
    public function check(): void {
        try {
            // Test database connection
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT 1");
            $stmt->execute();
            
            ResponseHandler::success([
                'database' => 'connected',
                'timestamp' => date('Y-m-d H:i:s'),
                'php_version' => PHP_VERSION
            ], 'System healthy');
            
        } catch (\Exception $e) {
            error_log("Health check failed: " . $e->getMessage());
            ResponseHandler::error('Database connection failed: ' . $e->getMessage(), 500);
        }
    }
}
