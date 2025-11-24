<?php
require_once __DIR__ . '/../controllers/CommentController.php';

function registerCommentRoutes($request, $method) {
    switch (true) {

        // CREATE COMMENT
        case $request === '/addComment' && $method === 'POST':
            CommentController::addComment();
            break;

        // READ ALL COMMENTS FOR ENTITY (post or project)
        case $request === '/getComments' && $method === 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['entity_type'], $data['entity_id'])) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Missing entity_type or entity_id"
                ]);
                return true;
            }

            CommentController::getComments($data['entity_type'], $data['entity_id']);
            break;

        // READ SINGLE COMMENT
        case $request === '/getComment' && $method === 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['comment_id'])) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Missing comment_id"
                ]);
                return true;
            }

            CommentController::getComment($data['comment_id']);
            break;

        // UPDATE COMMENT
        case $request === '/updateComment' && $method === 'POST':
            CommentController::updateComment();
            break;

        // DELETE COMMENT
        case $request === '/deleteComment' && $method === 'POST':
            CommentController::deleteComment();
            break;

        default:
            return false;
    }

    return true;
}
