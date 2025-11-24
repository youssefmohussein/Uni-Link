<?php

require_once __DIR__ . '/../controllers/AdminController.php';

function registerAdminRoutes($request, $method) {
    switch (true) {

        // ➕ Add student
        case $request === '/updateAdmin' && $method === 'POST':
            AdminController::updateAdmin();
            break;

        // 📋 Get all students
        case $request === '/getAllAdmins' && $method === 'GET':
            AdminController::getAllAdmins();
            break;
        

        default:
            return false; // Route not matched
    }
    return true; // Route was matched and handled
}
