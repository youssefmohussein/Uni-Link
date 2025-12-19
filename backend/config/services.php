<?php
/**
 * Service Provider Configuration
 * 
 * Registers all services in the DI container
 */

use App\Utils\Container;

// Repositories
use App\Repositories\UserRepository;
use App\Repositories\StudentRepository;
use App\Repositories\ProfessorRepository;
use App\Repositories\AdminRepository;
use App\Repositories\PostRepository;
use App\Repositories\CommentRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\SkillRepository;
use App\Repositories\FacultyRepository;
use App\Repositories\MajorRepository;
use App\Repositories\AnnouncementRepository;
use App\Repositories\CvRepository;
use App\Repositories\ProjectRoomRepository;
use App\Repositories\SubjectRepository;

// Services
use App\Services\AuthService;
use App\Services\UserService;
use App\Services\PostService;
use App\Services\ProjectService;
use App\Services\FacultyService;
use App\Services\AnnouncementService;
use App\Services\SkillService;
use App\Services\CommentService;
use App\Services\PostInteractionService;
use App\Services\SavedPostService;
use App\Services\DashboardService;
use App\Services\CvService;
use App\Services\ProjectRoomService;
use App\Services\SubjectService;

// Controllers
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\StudentController;
use App\Controllers\ProfessorController;
use App\Controllers\AdminController;
use App\Controllers\PostController;
use App\Controllers\CommentController;
use App\Controllers\PostInteractionController;
use App\Controllers\ProjectController;
use App\Controllers\FacultyController;
use App\Controllers\MajorController;
use App\Controllers\SkillController;
use App\Controllers\UserSkillController;
use App\Controllers\AnnouncementController;
use App\Controllers\ProjectRoomController;
use App\Controllers\SavedPostController;
use App\Controllers\DashboardController;
use App\Controllers\CvController;
use App\Controllers\SkillCategoryController;
use App\Controllers\HealthController;


// Mediators
use App\Mediators\NotificationMediator;
use App\Mediators\ProjectRoomMediator;

$container = Container::getInstance();

// ============================================
// Register Repositories
// ============================================

$container->singleton('UserRepository', function($c) {
    return new UserRepository();
});

$container->singleton('StudentRepository', function($c) {
    return new StudentRepository();
});

$container->singleton('ProfessorRepository', function($c) {
    return new ProfessorRepository();
});

$container->singleton('AdminRepository', function($c) {
    return new AdminRepository();
});

$container->singleton('PostRepository', function($c) {
    return new PostRepository();
});

$container->singleton('CommentRepository', function($c) {
    return new CommentRepository();
});

$container->singleton('ProjectRepository', function($c) {
    return new ProjectRepository();
});

$container->singleton('SkillRepository', function($c) {
    return new SkillRepository();
});

$container->singleton('FacultyRepository', function($c) {
    return new FacultyRepository();
});

$container->singleton('MajorRepository', function($c) {
    return new MajorRepository();
});

$container->singleton('AnnouncementRepository', function($c) {
    return new AnnouncementRepository();
});

$container->singleton('CvRepository', function($c) {
    return new CvRepository();
});

$container->singleton('ProjectRoomRepository', function($c) {
    return new ProjectRoomRepository();
});

$container->singleton('SubjectRepository', function($c) {
    return new SubjectRepository();
});

// ============================================
// Register Mediators
// ============================================

$container->singleton('NotificationMediator', function($c) {
    return new NotificationMediator();
});

$container->singleton('ProjectRoomMediator', function($c) {
    return new ProjectRoomMediator($c->get('NotificationMediator'));
});

// ============================================
// Register Services
// ============================================

$container->singleton('AuthService', function($c) {
    return new AuthService($c->get('UserRepository'));
});

$container->singleton('UserService', function($c) {
    return new UserService(
        $c->get('UserRepository'),
        $c->get('StudentRepository'),
        $c->get('ProfessorRepository'),
        $c->get('AdminRepository')
    );
});

$container->singleton('PostService', function($c) {
    return new PostService(
        $c->get('PostRepository'),
        $c->get('CommentRepository')
    );
});

$container->singleton('ProjectService', function($c) {
    return new ProjectService(
        $c->get('ProjectRepository'),
        $c->get('SkillRepository')
    );
});

$container->singleton('FacultyService', function($c) {
    return new FacultyService(
        $c->get('FacultyRepository'),
        $c->get('MajorRepository')
    );
});

$container->singleton('AnnouncementService', function($c) {
    return new AnnouncementService($c->get('AnnouncementRepository'));
});

$container->singleton('SkillService', function($c) {
    return new SkillService(
        $c->get('SkillRepository'),
        $c->get('UserRepository')
    );
});

$container->singleton('CommentService', function($c) {
    return new CommentService(
        $c->get('CommentRepository'),
        $c->get('PostRepository')
    );
});

$container->singleton('PostInteractionService', function($c) {
    return new PostInteractionService();
});

$container->singleton('SavedPostService', function($c) {
    return new SavedPostService($c->get('PostRepository'));
});

$container->singleton('DashboardService', function($c) {
    return new DashboardService(
        $c->get('UserRepository'),
        $c->get('PostRepository'),
        $c->get('ProjectRepository'),
        $c->get('StudentRepository')
    );
});

$container->singleton('CvService', function($c) {
    return new CvService($c->get('CvRepository'));
});

$container->singleton('ProjectRoomService', function($c) {
    return new ProjectRoomService($c->get('ProjectRoomRepository'));
});

$container->singleton('SubjectService', function($c) {
    return new SubjectService($c->get('SubjectRepository'));
});

// ============================================
// Register Controllers
// ============================================

$container->set('AuthController', function($c) {
    return new AuthController($c->get('AuthService'));
});

$container->set('UserController', function($c) {
    return new UserController($c->get('UserService'));
});

$container->set('StudentController', function($c) {
    return new StudentController($c->get('UserService'));
});

$container->set('ProfessorController', function($c) {
    return new ProfessorController($c->get('UserService'));
});

$container->set('AdminController', function($c) {
    return new AdminController($c->get('UserService'));
});

$container->set('PostController', function($c) {
    return new PostController($c->get('PostService'));
});

$container->set('CommentController', function($c) {
    return new CommentController($c->get('CommentService'));
});

$container->set('PostInteractionController', function($c) {
    return new PostInteractionController($c->get('PostInteractionService'));
});

$container->set('ProjectController', function($c) {
    return new ProjectController($c->get('ProjectService'));
});

$container->set('FacultyController', function($c) {
    return new FacultyController($c->get('FacultyService'));
});

$container->set('MajorController', function($c) {
    return new MajorController($c->get('FacultyService'));
});

$container->set('SkillController', function($c) {
    return new SkillController($c->get('SkillService'));
});

$container->set('UserSkillController', function($c) {
    return new UserSkillController($c->get('SkillService'));
});

$container->set('AnnouncementController', function($c) {
    return new AnnouncementController($c->get('AnnouncementService'));
});

$container->set('ProjectRoomController', function($c) {
    return new ProjectRoomController(
        $c->get('ProjectRoomService')
    );
});

$container->set('SavedPostController', function($c) {
    return new SavedPostController($c->get('SavedPostService'));
});

$container->set('DashboardController', function($c) {
    return new DashboardController($c->get('DashboardService'));
});

$container->set('CvController', function($c) {
    return new CvController();
});

$container->set('SkillCategoryController', function($c) {
    return new SkillCategoryController($c->get('SkillService'));
});

$container->set('HealthController', function($c) {
    return new HealthController();
});

return $container;
