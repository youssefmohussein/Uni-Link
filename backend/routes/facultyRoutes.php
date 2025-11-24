<?php
require_once __DIR__ . '/../controllers/FacultyController.php';

function registerFacultyRoutes($request, $method) {
    switch (true) {
        case $request === '/addFaculty' && $method === 'POST':
            FacultyController::addFaculty();
            break;

        case $request === '/getAllFaculties' && $method === 'GET':
            FacultyController::getAllFaculties();
            break;
        
        case $request === '/updateFaculty' && $method === 'POST':
            FacultyController::updateFaculty();
            break;

        case $request === '/deleteFaculty' && $method === 'POST':
            FacultyController::deleteFaculty();
            break;

        default:
            return false;
    }
    return true;
}
