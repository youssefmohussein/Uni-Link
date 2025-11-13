<?php
// ============================
// 🌐 UniLink API Router
// ============================

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once __DIR__ . '/routes/userRoutes.php';
require_once __DIR__ . '/routes/studentRoutes.php';
require_once __DIR__ . '/routes/adminRoutes.php';
require_once __DIR__ . '/routes/facultyRoutes.php';
require_once __DIR__ . '/routes/majorRoutes.php';
require_once __DIR__ . '/routes/professorRoute.php';
require_once __DIR__ . '/routes/postRoutes.php';
$request = str_replace('/backend/index.php', '', $_SERVER['REQUEST_URI']);
$method  = $_SERVER['REQUEST_METHOD'];

// Check each route group in order
if (registerUserRoutes($request, $method)) {
    exit;
}
elseif (registerStudentRoutes($request, $method)) { 
    exit; 
}
elseif (registerAdminRoutes($request, $method)) { 
    exit; 
}
elseif (registerProfessorRoutes($request, $method)) { 
    exit; 
}
elseif (registerFacultyRoutes($request, $method)) { 
    exit; 
}
elseif (registerMajorRoutes($request, $method)) { 
    exit; 
}
elseif (registerPostRoutes($request, $method)) { 
    exit; 
}


// elseif (registerProfessorRoutes($request, $method)) { exit; }

echo json_encode([
    "status" => "error",
    "message" => "Invalid route or method"
]);
?>
