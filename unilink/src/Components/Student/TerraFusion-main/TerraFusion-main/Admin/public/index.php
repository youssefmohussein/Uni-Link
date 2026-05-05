<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load Helpers first
require_once __DIR__ . '/../app/Helpers/Security.php';
require_once __DIR__ . '/../app/Helpers/Session.php';

require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Models/BaseModel.php';
// Load Models
require_once __DIR__ . '/../app/Models/MenuItemModel.php';
require_once __DIR__ . '/../app/Models/OrderModel.php';
require_once __DIR__ . '/../app/Models/ReservationModel.php';
require_once __DIR__ . '/../app/Models/UserModel.php';
// Load Controllers
require_once __DIR__ . '/../app/Controllers/DashboardController.php';
require_once __DIR__ . '/../app/Controllers/MenuController.php';
require_once __DIR__ . '/../app/Controllers/OrderController.php';
require_once __DIR__ . '/../app/Controllers/ReservationController.php';
require_once __DIR__ . '/../app/Controllers/UserController.php';
require_once __DIR__ . '/../app/Controllers/ProfileController.php';

// Start session early after loading the class
\App\Helpers\Session::start();

$logFile = __DIR__ . '/debug.log';
$logMsg = "[" . date('Y-m-d H:i:s') . "] REQUEST: " . $_SERVER['REQUEST_URI'] . " | Method=" . $_SERVER['REQUEST_METHOD'] . " | Session=" . session_id() . " | Role=" . ($_SESSION['role'] ?? 'NONE') . "\n";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $logMsg .= "POST DATA: " . json_encode($_POST) . "\n";
}
$logMsg .= "FULL SESSION: " . json_encode($_SESSION) . "\n";
file_put_contents($logFile, $logMsg, FILE_APPEND);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\MenuController;
use App\Controllers\OrderController;
use App\Controllers\ReservationController;
use App\Controllers\UserController;
use App\Controllers\ProfileController;

// Enforce Login
AuthController::requireLogin();

// Get page and action from request
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Page Access Control (RBAC) - Backend Authorization using Role Strings
// Define which roles can access each page
$pageAccessMap = [
    'users' => ['Manager'],                                           // Manager only
    'menu' => ['Manager', 'Chef Boss'],                              // Manager, Chef Boss
    'reservations' => ['Manager', 'Table Manager'],                  // Manager, Table Manager
    'orders' => ['Manager', 'Chef Boss', 'Table Manager', 'Waiter'], // All staff
    'dashboard' => ['Manager', 'Chef Boss', 'Table Manager', 'Waiter'], // All staff
    'profile' => ['Manager', 'Chef Boss', 'Table Manager', 'Waiter'], // All staff
    'logout' => ['Manager', 'Chef Boss', 'Table Manager', 'Waiter']  // All staff
];

$allowedRoles = $pageAccessMap[$page] ?? ['Manager', 'Chef Boss', 'Table Manager', 'Waiter'];
$userRole = $_SESSION['role'] ?? null;

if (!$userRole || !in_array($userRole, $allowedRoles)) {
    // Log for debugging
    $logMsg = "[ACCESS DENIED] Page: $page | Role: " . (($userRole === null) ? 'NULL' : $userRole) . " | Allowed: " . implode(', ', $allowedRoles) . " | Session ID: " . session_id() . "\n";
    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] $logMsg", FILE_APPEND);
    
    if (!$userRole) {
        \App\Helpers\Session::setFlash('session_error', 'Your session has expired. Please log in again.');
        header("Location: login.php");
        exit();
    }
    
    if ($page === 'dashboard') {
        // If they can't access dashboard, something is very wrong, logout to be safe
        AuthController::logout();
        exit();
    }
    
    $_SESSION['error_message'] = 'Access Denied: You do not have permission to access the ' . $page . ' page.';
    header("Location: index.php?page=dashboard");
    exit();
}

// Simple Router
switch ($page) {
    case 'dashboard':
        $controller = new DashboardController();
        $controller->index();
        break;
    case 'menu':
        $controller = new MenuController();
        if ($action === 'save') {
            $controller->save();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;
    case 'orders':
         $controller = new OrderController();
         if ($action === 'updateStatus' || $action === 'update_status') {
             $controller->updateStatus();
         } else {
             $controller->index();
         }
        break;
    case 'reservations':
         $controller = new ReservationController();
         if ($action === 'save') {
             $controller->save();
         } elseif ($action === 'delete') {
             $controller->delete();
         } else {
             $controller->index();
         }
        break;
    case 'users':
         $controller = new UserController();
         if ($action === 'create') {
             $controller->create();
         } elseif ($action === 'update') {
             $controller->update();
         } elseif ($action === 'delete') {
             $controller->delete();
         } else {
             $controller->index();
         }
        break;
    case 'profile':
        $controller = new ProfileController();
        if ($action === 'update') {
            $controller->update();
        } else {
            $controller->index();
        }
        break;
    case 'logout':
        AuthController::logout();
        break;
    default:
        // 404
        echo "404 - Page Not Found";
        break;
}
