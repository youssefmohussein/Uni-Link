<?php
require_once __DIR__ . '/../controllers/ProjectReviewController.php';

function registerProjectReviewRoutes($request, $method) {
    switch (true) {

        // CREATE
        case $request === '/addReview' && $method === 'POST':
            ProjectReviewController::addReview();
            break;

        // GET ALL REVIEWS FOR A PROJECT
        case $request === '/getReviewsByProject' && $method === 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['project_id'])) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Missing project_id"
                ]);
                return true;
            }
            ProjectReviewController::getReviewsByProject($data['project_id']);
            break;

        // GET SINGLE REVIEW
        case preg_match('#^/getReview/(\d+)$#', $request, $matches) && $method === 'GET':
            ProjectReviewController::getReview($matches[1]);
            break;

        // UPDATE
        case $request === '/updateReview' && $method === 'POST':
            ProjectReviewController::updateReview();
            break;

        // DELETE
        case $request === '/deleteReview' && $method === 'POST':
            ProjectReviewController::deleteReview();
            break;

        default:
            return false;
    }
    return true;
}
