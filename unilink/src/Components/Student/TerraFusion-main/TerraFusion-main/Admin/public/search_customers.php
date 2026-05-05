<?php
// Admin/public/search_customers.php

require_once __DIR__ . '/../app/Helpers/Security.php';
require_once __DIR__ . '/../app/Helpers/Session.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';

// Start session to use AuthController
\App\Helpers\Session::start();

// Enforce Login - ensure only authorized personnel can search customers
\App\Controllers\AuthController::requireLogin('login.php');

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';
$query = trim($query);

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $db = \App\Core\Database::getInstance()->getConnection();
    
    // Search for active customers by name, phone, or email
    $sql = "SELECT full_name, phone, email 
            FROM users 
            WHERE is_active = 1 
              AND (full_name LIKE :query OR phone LIKE :query OR email LIKE :query) 
            ORDER BY full_name ASC 
            LIMIT 10";
            
    $stmt = $db->prepare($sql);
    $stmt->execute(['query' => '%' . $query . '%']);
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($results);
} catch (PDOException $e) {
    // Return empty array on error to prevent breaking front-end JSON parsing
    error_log("Search Customers Error: " . $e->getMessage());
    echo json_encode([]);
}
