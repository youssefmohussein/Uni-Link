<?php
namespace App\Utils;

/**
 * HTTP Response Handler
 * 
 * Standardizes JSON responses across the application
 */
class ResponseHandler {
    /**
     * Send success response
     * 
     * @param mixed $data Response data
     * @param string $message Success message
     * @param int $code HTTP status code
     */
    public static function success($data = null, string $message = 'Success', int $code = 200): void {
        http_response_code($code);
        
        $response = [
            'status' => 'success',
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response);
        exit;
    }
    
    /**
     * Send error response
     * 
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param mixed $errors Additional error details
     */
    public static function error(string $message, int $code = 400, $errors = null): void {
        http_response_code($code);
        
        $response = [
            'status' => 'error',
            'message' => $message
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        echo json_encode($response);
        exit;
    }
    
    /**
     * Send paginated response
     * 
     * @param array $data Response data
     * @param int $total Total items
     * @param int $page Current page
     * @param int $perPage Items per page
     */
    public static function paginated(array $data, int $total, int $page, int $perPage): void {
        http_response_code(200);
        
        echo json_encode([
            'status' => 'success',
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($total / $perPage)
            ]
        ]);
        exit;
    }
}
