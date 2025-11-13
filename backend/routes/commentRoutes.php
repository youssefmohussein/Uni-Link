<?php
require_once __DIR__ . '/../controllers/CommentController.php';

function registerCommentRoutes($request, $method) {
    switch (true) {
        case $request === '/addComment' && $method === 'POST':
            CommentController::addComment();
            break;

        case $request === '/getCommentsByPost' && $method === 'POST':
            CommentController::getCommentsByPost();
            break;

        case $request === '/updateComment' && $method === 'POST':
            CommentController::updateComment();
            break;

        case $request === '/deleteComment' && $method === 'POST':
            CommentController::deleteComment();
            break;

        default:
            return false;
    }
    return true;
}
