<?php
// Test if the UserRepository searchByUsername method is working
require_once __DIR__ . '/app/Utils/Database.php';
require_once __DIR__ . '/app/Repositories/BaseRepository.php';
require_once __DIR__ . '/app/Repositories/UserRepository.php';
require_once __DIR__ . '/app/Repositories/Interfaces/UserRepositoryInterface.php';

use App\Repositories\UserRepository;

try {
    $userRepo = new UserRepository();
    $results = $userRepo->searchByUsername('student_users', 20);
    
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'count' => count($results),
        'data' => $results
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
