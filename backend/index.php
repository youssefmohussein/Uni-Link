<?php
// ============================
// 🌐 UniLink API Router
// ============================

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once __DIR__ . '/routes/userRoutes.php';
// Later: require_once __DIR__ . '/routes/adminRoutes.php';
// Later: require_once __DIR__ . '/routes/postRoutes.php';

$request = str_replace('/backend/index.php', '', $_SERVER['REQUEST_URI']);
$method  = $_SERVER['REQUEST_METHOD'];

// Check each route group in order
if (registerUserRoutes($request, $method)) {
    exit;
}
// elseif (registerAdminRoutes($request, $method)) { exit; }
// elseif (registerProfessorRoutes($request, $method)) { exit; }

echo json_encode([
    "status" => "error",
    "message" => "Invalid route or method"
]);
?>
