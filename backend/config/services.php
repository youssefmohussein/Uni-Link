<?php
/**
 * Service Provider Configuration
 * 
 * Registers all services in the DI container
 */

use App\Utils\Container;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Controllers\AuthController;
use App\Mediators\NotificationMediator;
use App\Mediators\ProjectRoomMediator;

$container = Container::getInstance();

// Register Repositories
$container->singleton('UserRepository', function($c) {
    return new UserRepository();
});

// Register Mediators
$container->singleton('NotificationMediator', function($c) {
    return new NotificationMediator();
});

$container->singleton('ProjectRoomMediator', function($c) {
    return new ProjectRoomMediator($c->get('NotificationMediator'));
});

// Register Services
$container->singleton('AuthService', function($c) {
    return new AuthService($c->get('UserRepository'));
});

// Register Controllers
$container->set('AuthController', function($c) {
    return new AuthController($c->get('AuthService'));
});

$container->set('ProjectController', function($c) {
    return new \App\Controllers\ProjectController();
});


return $container;
