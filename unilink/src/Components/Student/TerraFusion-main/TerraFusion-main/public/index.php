<?php

/**
 * TerraFusion Front Controller
 * Entry point for all requests
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load autoloader
require_once __DIR__ . '/../app/autoload.php';

// Load helpers
require_once __DIR__ . '/../app/helpers/helpers.php';

// Initialize error handler
\App\Libs\ErrorHandler::init();

// Load routes
require_once __DIR__ . '/../routes/web.php';

