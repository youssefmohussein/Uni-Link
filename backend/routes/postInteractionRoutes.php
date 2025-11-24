<?php
require_once __DIR__ . '/../controllers/PostInteractionController.php';

function registerPostInteractionRoutes($request, $method) {
    switch (true) {
        case $request === '/addInteraction' && $method === 'POST':
            PostInteractionController::addInteraction();
            break;

        case $request === '/getInteractionsByPost' && $method === 'POST':
            PostInteractionController::getInteractionsByPost();
            break;

        case $request === '/deleteInteraction' && $method === 'POST':
            PostInteractionController::deleteInteraction();
            break;

        default:
            return false;
    }
    return true;
}
