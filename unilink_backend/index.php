<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalize request (remove trailing slashes)
$request = rtrim($request, '/');

// ✅ Router
switch ($request) {

    // 🧩 INSTALL ROUTE
    case '/unilink-backend/api/install':
    case '/api/install':
    case '/unilink_backend/api/install':
    case '/unilink_backend/index.php/api/install':
    case '/unilink-backend/api/install.php':
    case '/api/install.php':
    case '/unilink_backend/api/install.php':
    case '/unilink_backend/index.php/api/install.php':
        require_once __DIR__ . '/controllers/InstallController.php';
        $controller = new InstallController();
        $controller->handleRequest();
        break;

    // 🧪 TEST ROUTE
    case '/unilink-backend/api/test':
    case '/api/test':
    case '/unilink_backend/api/test':
    case '/unilink_backend/index.php/api/test':
    case '/unilink-backend/api/test.php':
    case '/api/test.php':
    case '/unilink_backend/api/test.php':
    case '/unilink_backend/index.php/api/test.php':
        require_once __DIR__ . '/test.php';
        break;

    // 📦 IMPORT ROUTE (optional for restoring backups)
    case '/unilink-backend/api/import':
    case '/api/import':
    case '/unilink_backend/api/import':
    case '/unilink_backend/index.php/api/import':
    case '/unilink-backend/api/import.php':
    case '/api/import.php':
    case '/unilink_backend/api/import.php':
    case '/unilink_backend/index.php/api/import.php':
        require_once __DIR__ . '/controllers/ImportController.php';
        $importController = new ImportController();
        $importController->handleRequest();
        break;

    // ⬇️ EXPORT ROUTE
    case '/unilink-backend/api/export':
    case '/api/export':
    case '/unilink_backend/api/export':
    case '/unilink_backend/index.php/api/export':
    case '/unilink-backend/api/export.php':
    case '/api/export.php':
    case '/unilink_backend/api/export.php':
    case '/unilink_backend/index.php/api/export.php':
        require_once __DIR__ . '/controllers/ExportController.php';
        $exportController = new ExportController();
        $exportController->handleRequest();
        break;

    // 🚫 DEFAULT (unknown route)
    default:
        echo json_encode([
            "status" => "error",
            "message" => "Invalid route: $request"
        ]);
        break;
}
?>
