<?php
require_once __DIR__ . '/../controllers/ProjectController.php';

function registerProjectRoutes($request, $method) {
    switch (true) {
        case $request === '/addProject' && $method === 'POST':
            ProjectController::addProject();
            break;

        case $request === '/updateProject' && $method === 'POST':
            ProjectController::updateProject();
            break;

        case $request === '/deleteProject' && $method === 'POST':
            ProjectController::deleteProject();
            break;
        
        case $request === '/addGrade' && $method === 'POST':
            ProjectController::addGrade();
            break;
            
        case $request === '/getProjectById' && $method === 'GET':
            ProjectController::getProjectById();
            break;
        default:
            return false;
    }
    return true;
}
