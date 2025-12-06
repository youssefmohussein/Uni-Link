<?php
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../controllers/PostController.php';

function registerPostRoutes($request, $method) {
    switch (true) {
        case $request === '/addPost' && $method === 'POST':
            AuthMiddleware::requireAuth();
            PostController::addPost();
            break;

        case $request === '/getAllPosts' && $method === 'GET':
            AuthMiddleware::requireAuth();
            PostController::getAllPosts();
            break;
        case $request === '/getPostById' && $method === 'GET':
            AuthMiddleware::requireAuth();
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
            AuthMiddleware::requireAuth();
            PostController::updatePost();
            break;

        case $request === '/deletePost' && $method === 'POST':
            AuthMiddleware::requireAuth();
            PostController::deletePost();
            break;

        case $request === '/searchPosts' && $method === 'GET':
            AuthMiddleware::requireAuth();
            PostController::searchPosts();
            break;

        case $request === '/getUserPosts' && $method === 'GET':
            AuthMiddleware::requireAuth();
            PostController::getUserPosts();
            break;

        default:
            return false;
    }
    return true;
}
