<?php
// backend/test_trending.php

// Adjust paths to point to correct location
require_once __DIR__ . '/config/autoload.php';
require_once __DIR__ . '/config/env_loader.php';
loadEnv(__DIR__ . '/.env');

// Mock container/services or just instantiate Repo manually if complex dependency tree
// But using services.php is best if it works
$container = require_once __DIR__ . '/config/services.php';

try {
    $repo = $container->get('App\Repositories\PostRepository');
    // Call getTrending with null limit (or large one)
    $posts = $repo->getTrending(20, 0);

    echo "--- TRENDING POSTS DEBUG ---\n";
    foreach ($posts as $p) {
        $likes = $p['likes_count'];
        $comments = $p['comments_count'];
        $total = $p['total_engagement'];
        echo "Post ID: {$p['post_id']} | Likes: {$likes} | Comments: {$comments} | Total: {$total} | Date: {$p['created_at']}\n";
    }
    echo "----------------------------\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
