<?php
require_once __DIR__ . '/../controllers/MajorController.php';

function registerMajorRoutes($request, $method) {
    switch (true) {
        case $request === '/addMajor' && $method === 'POST':
            MajorController::addMajor();
            break;

        case $request === '/getAllMajors' && $method === 'GET':
            MajorController::getAllMajors();
            break;
        
        case $request === '/updateMajor' && $method === 'POST':
            MajorController::updateMajor();
            break;

        case $request === '/deleteMajor' && $method === 'POST':
            MajorController::deleteMajor();
            break;

        default:
            return false;
    }
    return true;
}
