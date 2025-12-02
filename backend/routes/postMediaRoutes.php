<?php
require_once __DIR__ . '/../controllers/PostMediaController.php';

function registerPostMediaRoutes($request, $method) {
    switch (true) {
        case $request === '/uploadMedia' && $method === 'POST':
            PostMediaController::uploadMedia();
            break;

        case $request === '/addMedia' && $method === 'POST':
            PostMediaController::addMedia();
            break;

        case $request === '/getAllMedia' && $method === 'GET':
            PostMediaController::getAllMedia();
            break;

        case $request === '/getMediaById' && $method === 'GET':
            if (isset($_GET['media_id'])) {
                $media_id = (int)$_GET['media_id'];
                PostMediaController::getMediaById($media_id);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Missing media_id parameter"
                ]);
            }
            break;

        case $request === '/updateMedia' && $method === 'POST':
            PostMediaController::updateMedia();
            break;

        case $request === '/deleteMedia' && $method === 'POST':
            PostMediaController::deleteMedia();
            break;

        default:
            return false;
    }
    return true;
}
