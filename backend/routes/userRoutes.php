<?php
require_once __DIR__ . '/../controllers/UserController.php';

function registerUserRoutes($request, $method) {
    switch (true) {
        case $request === '/addUser' && $method === 'POST':
            UserController::addUser();
            break;

        case $request === '/getUsers' && $method === 'GET':
            UserController::getAllUsers();
            break;
        
        case $request === '/updateUser' && $method === 'POST':
            UserController::updateUser();
            break;

        case $request === '/deleteUser' && $method === 'POST':
            UserController::deleteUser();
            break;

        default:
            return false;
    }
    return true;
}
