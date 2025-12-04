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

        case $request === '/getUserReaction' && $method === 'POST':
            PostInteractionController::getUserReaction();
            break;

        case $request === '/getReactionCounts' && $method === 'POST':
            PostInteractionController::getReactionCounts();
            break;

        default:
            return false;
    }
    return true;
}
