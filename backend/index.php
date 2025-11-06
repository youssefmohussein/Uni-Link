<?php
// ============================
// 🌐 UniLink API Router
// ============================

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once __DIR__ . '/controllers/UserController.php';

// Get the requested endpoint from the URL
$request = $_SERVER['REQUEST_URI'];
$method  = $_SERVER['REQUEST_METHOD'];

// Optional: clean path if your project is in a folder
// Example: http://localhost/unilink_backend/index.php/addUser → /addUser
$request = str_replace('/backend/index.php', '', $request);

// 🧭 ROUTING SECTION
switch (true) {
    // ============ USERS ============

    case $request === '/addUser' && $method === 'POST':
        UserController::addUser();
        break;

    // 🧩 You can add more APIs later
    // case $request === '/getUsers' && $method === 'GET':
    //     UserController::getAllUsers();
    //     break;

    default:
        echo json_encode([
            "status" => "error",
            "message" => "Invalid route or method"
        ]);
        break;
}
?>
