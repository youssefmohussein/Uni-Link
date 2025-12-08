<?php
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

function registerUserRoutes($request, $method) {
    switch (true) {
        case $request === '/addUser' && $method === 'POST':
            AuthMiddleware::requireRole('Admin');
            UserController::addUser();
            break;

        case $request === '/getUsers' && $method === 'GET':
            AuthMiddleware::requireRole('Admin');
            UserController::getAllUsers();
            break;
        
        case $request === '/updateUser' && $method === 'POST':
            AuthMiddleware::requireRole('Admin');
            UserController::updateUser();
            break;

        case $request === '/deleteUser' && $method === 'POST':
            AuthMiddleware::requireRole('Admin');
            UserController::deleteUser();
            break;

        case $request === '/getUserProfile' && $method === 'GET':
            AuthMiddleware::requireAuth();
            UserController::getUserProfile();
            break;

        default:
            return false;
    }
    return true;
}
