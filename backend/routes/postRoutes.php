<?php
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../controllers/PostController.php';

function registerPostRoutes($request, $method) {
    // Require authentication for all post routes
    AuthMiddleware::requireAuth();
    
    switch (true) {
        case $request === '/addPost' && $method === 'POST':
            PostController::addPost();
            break;

        case $request === '/getAllPosts' && $method === 'GET':
            PostController::getAllPosts();
            break;
        case $request === '/getPostById' && $method === 'GET':
            if (isset($_GET['post_id'])) {
            $post_id = (int)$_GET['post_id'];
            PostController::getPostById($post_id);
            } else {
            echo json_encode([
            "status" => "error",
            "message" => "Missing post_id parameter"
            ]);
            }
            break;
        case $request === '/updatePost' && $method === 'POST':
            PostController::updatePost();
            break;

        case $request === '/deletePost' && $method === 'POST':
            PostController::deletePost();
            break;

        case $request === '/searchPosts' && $method === 'GET':
            PostController::searchPosts();
            break;

        default:
            return false;
    }
    return true;
}
