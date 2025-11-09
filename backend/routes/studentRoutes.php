<?php

require_once __DIR__ . '/../controllers/StudentController.php';

function registerStudentRoutes($request, $method) {
    switch (true) {

        // ➕ Add student
        case $request === '/addStudent' && $method === 'POST':
            StudentController::addStudent();
            break;

        // 📋 Get all students
        case $request === '/getAllStudents' && $method === 'GET':
            StudentController::getAllStudents();
            break;
        
        // ✏️ Update student
        case $request === '/updateStudent' && $method === 'POST':
            StudentController::updateStudent();
            break;

        default:
            return false; // Route not matched
    }
    return true; // Route was matched and handled
}
