<?php

use App\Libs\Router;
use App\Controllers\AuthController;
use App\Controllers\MenuController;
use App\Controllers\OrderController;
use App\Controllers\CustomerController;
use App\Controllers\StaffController;
use App\Controllers\AdminController;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\AdminMiddleware;
use App\Middlewares\StaffMiddleware;
use App\Middlewares\GuestMiddleware;
use App\Middlewares\CsrfMiddleware;

$router = new Router();

// Auth Routes
$router->get('/', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
$router->get('login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
$router->post('login', [AuthController::class, 'login'], [GuestMiddleware::class, CsrfMiddleware::class]);
$router->get('register', [AuthController::class, 'showRegister'], [GuestMiddleware::class]);
$router->post('register', [AuthController::class, 'register'], [GuestMiddleware::class, CsrfMiddleware::class]);
$router->get('logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);

// Customer Routes
$router->get('customer/menu', [CustomerController::class, 'menu'], [AuthMiddleware::class]);
$router->get('customer/orders', [CustomerController::class, 'orders'], [AuthMiddleware::class]);
$router->get('customer/orders/{id}', [CustomerController::class, 'orderDetails'], [AuthMiddleware::class]);
$router->get('customer/track/{id}', [CustomerController::class, 'trackOrder'], [AuthMiddleware::class]);
$router->get('customer/review/{id}', [CustomerController::class, 'showReview'], [AuthMiddleware::class]);
$router->post('customer/review', [CustomerController::class, 'submitReview'], [AuthMiddleware::class, CsrfMiddleware::class]);

// Menu Routes
$router->get('menu', [MenuController::class, 'index'], [AuthMiddleware::class]);

// Order Routes
$router->get('order/cart', [OrderController::class, 'cart'], [AuthMiddleware::class]);
$router->post('order/add-to-cart', [OrderController::class, 'addToCart'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('order/remove-from-cart', [OrderController::class, 'removeFromCart'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('order/checkout', [OrderController::class, 'checkout'], [AuthMiddleware::class]);
$router->post('order/apply-promo', [OrderController::class, 'applyPromo'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('order/place', [OrderController::class, 'placeOrder'], [AuthMiddleware::class, CsrfMiddleware::class]);

// Staff Routes
$router->get('staff/dashboard', [StaffController::class, 'dashboard'], [StaffMiddleware::class]);
$router->get('staff/kitchen-orders', [StaffController::class, 'kitchenOrders'], [StaffMiddleware::class]);
$router->post('staff/update-order-status', [StaffController::class, 'updateOrderStatus'], [StaffMiddleware::class, CsrfMiddleware::class]);
$router->get('staff/tables', [StaffController::class, 'tables'], [StaffMiddleware::class]);
$router->post('staff/update-table-status', [StaffController::class, 'updateTableStatus'], [StaffMiddleware::class, CsrfMiddleware::class]);
$router->get('staff/create-order', [StaffController::class, 'createInPersonOrder'], [StaffMiddleware::class]);
$router->post('staff/process-order', [StaffController::class, 'processInPersonOrder'], [StaffMiddleware::class, CsrfMiddleware::class]);

// Admin Routes
$router->get('admin/dashboard', [AdminController::class, 'dashboard'], [AdminMiddleware::class]);
$router->get('admin/menu-management', [AdminController::class, 'menuManagement'], [AdminMiddleware::class]);
$router->post('admin/menu/create', [AdminController::class, 'createMenuItem'], [AdminMiddleware::class, CsrfMiddleware::class]);
$router->post('admin/menu/update', [AdminController::class, 'updateMenuItem'], [AdminMiddleware::class, CsrfMiddleware::class]);
$router->get('admin/menu/delete/{id}', [AdminController::class, 'deleteMenuItem'], [AdminMiddleware::class]);
$router->get('admin/users', [AdminController::class, 'users'], [AdminMiddleware::class]);
$router->post('admin/users/create', [AdminController::class, 'createUser'], [AdminMiddleware::class, CsrfMiddleware::class]);
$router->post('admin/users/update', [AdminController::class, 'updateUser'], [AdminMiddleware::class, CsrfMiddleware::class]);
$router->get('admin/users/delete/{id}', [AdminController::class, 'deleteUser'], [AdminMiddleware::class]);
$router->get('admin/promotions', [AdminController::class, 'promotions'], [AdminMiddleware::class]);
$router->post('admin/promotions/create', [AdminController::class, 'createPromotion'], [AdminMiddleware::class, CsrfMiddleware::class]);
$router->get('admin/reports', [AdminController::class, 'reports'], [AdminMiddleware::class]);

// Dispatch request
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$router->dispatch($method, $uri);

