<?php
require_once __DIR__ . '/../controllers/CVController.php';

function registerCVRoutes($request, $method) {
    switch (true) {
        case $request === '/uploadCV' && $method === 'POST':
            CVController::uploadCV();
            return true;

        case preg_match('#^/downloadCV/(\d+)$#', $request, $matches) && $method === 'GET':
            CVController::downloadCV($matches[1]);
            return true;

        default:
            return false;
    }
}
