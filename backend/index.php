<?php
// ============================
// 🌐 UniLink API Router
// ============================

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-USER-ID, Authorization, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/routes/userRoutes.php';
require_once __DIR__ . '/routes/studentRoutes.php';
require_once __DIR__ . '/routes/adminRoutes.php';
require_once __DIR__ . '/routes/facultyRoutes.php';
require_once __DIR__ . '/routes/majorRoutes.php';
require_once __DIR__ . '/routes/professorRoute.php';
require_once __DIR__ . '/routes/postRoutes.php';
require_once __DIR__ . '/routes/postMediaRoutes.php';
require_once __DIR__ . '/routes/commentRoutes.php';
require_once __DIR__ . '/routes/postInteractionRoutes.php';
require_once __DIR__ . '/routes/cvRoutes.php';
require_once __DIR__ . '/routes/projectRoutes.php';
require_once __DIR__ . '/routes/skillCategoryRoutes.php';
require_once __DIR__ . '/routes/skillRoutes.php';
require_once __DIR__ . '/routes/projectSkillRoutes.php';
require_once __DIR__ . '/routes/userSkillRoutes.php';
require_once __DIR__ . '/routes/announcementRoutes.php';
require_once __DIR__ . '/routes/projectReviewRoutes.php';
// Parse request URL without query parameters
if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '') {
    $request = $_SERVER['PATH_INFO'];
} else {
    $request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $request = str_replace('/backend/index.php', '', $request);
}

$method  = $_SERVER['REQUEST_METHOD'];
// Debug
error_log("Request path: [$request], Method: [$method]");

// Check each route group in order
if (registerUserRoutes($request, $method)) exit;
elseif (registerStudentRoutes($request, $method)) exit;
elseif (registerAdminRoutes($request, $method)) exit;
elseif (registerProfessorRoutes($request, $method)) exit;
elseif (registerFacultyRoutes($request, $method)) exit;
elseif (registerMajorRoutes($request, $method)) exit;
elseif (registerPostRoutes($request, $method)) exit;
elseif (registerPostMediaRoutes($request, $method)) exit;
elseif (registerCommentRoutes($request, $method)) exit;
elseif (registerPostInteractionRoutes($request, $method)) exit;
elseif (registerCVRoutes($request, $method)) exit;
elseif (registerProjectRoutes($request, $method)) exit;  // <-- Added
elseif (registerSkillCategoryRoutes($request, $method)) exit;
elseif (registerSkillRoutes($request, $method)) exit;   
elseif (registerProjectSkillRoutes($request, $method)) exit;
elseif (registerUserSkillRoutes($request, $method)) exit;
elseif (registerAnnouncementRoutes($request, $method)) exit;
elseif (registerProjectReviewRoutes($request, $method)) exit;
echo json_encode([
    "status" => "error",
    "message" => "Invalid route or method"
]);
