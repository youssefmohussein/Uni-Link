<?php
require_once __DIR__ . '/../controllers/ProfessorController.php';

function registerProfessorRoutes($request, $method) {
    switch (true) {
        case $request === '/addProfessor' && $method === 'POST':
            ProfessorController::addProfessor();
            break;

        case $request === '/getAllProfessors' && $method === 'GET':
            ProfessorController::getAllProfessors();
            break;
        
        case $request === '/updateProfessor' && $method === 'POST':
            ProfessorController::updateProfessor();
            break;

        default:
            return false;
    }
    return true;
}
