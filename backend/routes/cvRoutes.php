<?php
require_once __DIR__ . '/../controllers/CVController.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

function registerCVRoutes($request, $method) {
    switch (true) {
        case $request === '/uploadCV' && $method === 'POST':
            AuthMiddleware::requireAuth();
            CVController::uploadCV();
            return true;

        case preg_match('#^/downloadCV/(\d+)$#', $request, $matches) && $method === 'GET':
            AuthMiddleware::requireAuth();
            CVController::downloadCV($matches[1]);
            return true;

        case $request === '/getCV' && $method === 'GET':
            AuthMiddleware::requireAuth();
            CVController::getCV();
            return true;

        default:
            return false;
    }
}
