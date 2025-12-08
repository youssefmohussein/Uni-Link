<?php
require_once __DIR__ . '/../controllers/UserSkillController.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

function registerUserSkillRoutes($request, $method) {
    switch (true) {

        // ADD skill to user
        case $request === '/addUserSkills' && $method === 'POST':
            AuthMiddleware::requireAuth();
            UserSkillController::addUserSkills();
            break;

        // GET user's skills
        case $request === '/getUserSkills' && $method === 'POST':
            AuthMiddleware::requireAuth();
            UserSkillController::getUserSkills();
            break;

        // REMOVE specific skill from user
        case $request === '/removeUserSkill' && $method === 'POST':
            AuthMiddleware::requireAuth();
            UserSkillController::removeUserSkill();
            break;

        // DELETE all user skills
        case $request === '/deleteUserSkills' && $method === 'POST':
            AuthMiddleware::requireAuth();
            UserSkillController::deleteUserSkills();
            break;

        default:
            return false;
    }
    return true;
}
