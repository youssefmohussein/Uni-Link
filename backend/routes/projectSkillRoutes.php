<?php
require_once __DIR__ . '/../controllers/ProjectSkillController.php';

function registerProjectSkillRoutes($request, $method) {
    switch (true) {

        // ADD skills to a project
        case $request === '/addProjectSkills' && $method === 'POST': // expect {"project_id": 1, "skill_ids": [1,2,3]}
            ProjectSkillController::addProjectSkills();
            break;

        // GET skills of a project
        case $request === '/getProjectSkills' && $method === 'POST': // expect {"project_id": 1}
            $data = json_decode(file_get_contents('php://input'), true);
            ProjectSkillController::getProjectSkills($data['project_id']);
            break;

        // REMOVE skill from project
        case $request === '/deleteProjectSkill' && $method === 'POST':
            ProjectSkillController::deleteProjectSkill();
            break;

        default:
            return false;
    }
    return true;
}
