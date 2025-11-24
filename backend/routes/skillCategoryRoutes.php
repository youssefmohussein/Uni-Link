<?php
require_once __DIR__ . '/../controllers/SkillCategoryController.php';

function registerSkillCategoryRoutes($request, $method) {
    switch (true) {

        // CREATE category
        case $request === '/addSkillCategory' && $method === 'POST':
            SkillCategoryController::addCategory();
            break;

        // GET all categories for user
        case $request === '/getSkillCategories' && $method === 'POST': // expect {"user_id": 1}
            $data = json_decode(file_get_contents('php://input'), true);
            SkillCategoryController::getCategoriesByUser($data['user_id']);
            break;

        // UPDATE category
        case $request === '/updateSkillCategory' && $method === 'POST':
            SkillCategoryController::updateCategory();
            break;

        // DELETE category
        case $request === '/deleteSkillCategory' && $method === 'POST':
            SkillCategoryController::deleteCategory();
            break;

        default:
            return false;
    }
    return true;
}
