<?php
require_once __DIR__ . '/../controllers/UserSkillController.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

function registerUserSkillRoutes($request, $method) {
    switch (true) {

        // ADD skill to user
        case $request === '/addUserSkills' && $method === 'POST': // expect {"user_id": 1, "skill_ids": [1,2]}
            AuthMiddleware::requireAuth();
            UserSkillController::addUserSkills();
            break;

        // GET user's skills
        case $request === '/getUserSkills' && $method === 'POST': // expect {"user_id": 1}
            AuthMiddleware::requireAuth();
            $data = json_decode(file_get_contents('php://input'), true);
            UserSkillController::getUserSkills($data['user_id']);
            break;

        // REMOVE skill from user
        case $request === '/deleteUserSkill' && $method === 'POST':
            AuthMiddleware::requireAuth();
            UserSkillController::deleteUserSkill();
            break;

        default:
            return false;
    }
    return true;
}
