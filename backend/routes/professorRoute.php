<?php
require_once __DIR__ . '/../controllers/ProfessorController.php';

function registerProfessorRoutes($request, $method)
{
    switch (true) {

        // ➕ Add professor
        case $request === '/addProfessor' && $method === 'POST':
            ProfessorController::addProfessor();
            return true;

        // 📋 Get all professors
        case $request === '/getAllProfessors' && $method === 'GET':
            ProfessorController::getAllProfessors();
            return true;

        // 🔍 Get professor by ID
        case preg_match('#^/getProfessorById/(\d+)$#', $request, $matches) && $method === 'GET':
            ProfessorController::getProfessorById($matches[1]);
            return true;

        // ✏️ Update professor
        case $request === '/updateProfessor' && $method === 'POST':
            ProfessorController::updateProfessor();
            return true;

        default:
            return false;
    }
}
