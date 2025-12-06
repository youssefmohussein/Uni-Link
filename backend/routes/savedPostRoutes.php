<?php
require_once __DIR__ . '/../controllers/SavedPostController.php';

function registerSavedPostRoutes($request, $method) {
    switch (true) {
        case $request === '/savePost' && $method === 'POST':
            SavedPostController::savePost();
            break;

        case $request === '/unsavePost' && $method === 'POST':
            SavedPostController::unsavePost();
            break;

        case $request === '/getSavedPosts' && $method === 'GET':
            SavedPostController::getSavedPosts();
            break;

        case $request === '/isPostSaved' && $method === 'GET':
            SavedPostController::isPostSaved();
            break;

        default:
            return false;
    }
    return true;
}
?>
