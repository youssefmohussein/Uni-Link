<?php
require_once __DIR__ . '/../controllers/SkillController.php';

function registerSkillRoutes($request, $method) {
    switch (true) {

        // CREATE skill
        case $request === '/addSkill' && $method === 'POST':
            SkillController::addSkill();
            break;

        // GET all skills by user
        case $request === '/getSkills' && $method === 'POST': // expect {"user_id": 1}
            $data = json_decode(file_get_contents('php://input'), true);
            SkillController::getSkillsByUser($data['user_id']);
            break;

        // UPDATE skill
        case $request === '/updateSkill' && $method === 'POST':
            SkillController::updateSkill();
            break;

        // DELETE skill
        case $request === '/deleteSkill' && $method === 'POST':
            SkillController::deleteSkill();
            break;

        default:
            return false;
    }
    return true;
}
