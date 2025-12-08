<?php
require_once __DIR__ . '/../controllers/ProjectController.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

function registerProjectRoutes($request, $method) {
    switch (true) {
        case $request === '/addProject' && $method === 'POST':
            AuthMiddleware::requireAuth();
            ProjectController::addProject();
            break;

        case $request === '/updateProject' && $method === 'POST':
            AuthMiddleware::requireAuth();
            ProjectController::updateProject();
            break;

        case $request === '/deleteProject' && $method === 'POST':
            AuthMiddleware::requireAuth();
            ProjectController::deleteProject();
            break;
        
        case $request === '/addGrade' && $method === 'POST':
            AuthMiddleware::requireAuth();
            ProjectController::addGrade();
            break;
            
        case $request === '/getProjectById' && $method === 'GET':
            AuthMiddleware::requireAuth();
            ProjectController::getProjectById();
            break;

        case $request === '/getUserProjects' && $method === 'GET':
            AuthMiddleware::requireAuth();
            ProjectController::getUserProjects();
            break;

        default:
            return false;
    }
    return true;
}
