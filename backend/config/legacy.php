<?php
/**
 * Legacy Controller Wrapper
 * 
 * Wraps old procedural controllers to work with new routing system
 * This allows gradual migration while maintaining compatibility
 */

// Load all legacy controllers
$legacyControllers = [
    'UserController',
    'AdminController',
    'StudentController',
    'ProfessorController',
    'PostController',
    'PostInteractionController',
    'PostMediaController',
    'CommentController',
    'ProjectController',
    'ProjectReviewController',
    'ProjectSkillController',
    'RoomsController',
    'RoomChatController',
    'RoomMembershipController',
    'CvController',
    'SkillController',
    'SkillCategoryController',
    'UserSkillController',
    'FacultyController',
    'MajorController',
    'AnnouncementController',
    'DashboardController',
    'savedPostController',
    'LoginController'
];

foreach ($legacyControllers as $controller) {
    $file = __DIR__ . "/../controllers/{$controller}.php";
    if (file_exists($file)) {
        require_once $file;
    }
}
