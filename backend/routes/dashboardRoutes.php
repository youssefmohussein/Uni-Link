<?php

require_once __DIR__ . '/../controllers/DashboardController.php';

function registerDashboardRoutes($request, $method)
{
    switch (true) {

        // 📊 Get dashboard statistics
        case $request === '/getDashboardStats' && $method === 'GET':
            DashboardController::getDashboardStats();
            break;

        default:
            return false; // Route not matched
    }
    return true; // Route was matched and handled
}
?>