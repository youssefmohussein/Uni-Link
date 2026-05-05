<?php
// oauth/google.php - Start Google OAuth flow
session_start();
require_once __DIR__ . '/../config.php';

$clientId = getenv('GOOGLE_CLIENT_ID') ?: (defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : null);
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$redirectUri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $basePath . '/google_callback.php';
$action = $_GET['action'] ?? 'login'; // login or register

if (!$clientId) {
    // Friendly message for developer
    echo "Google OAuth is not configured. Set GOOGLE_CLIENT_ID environment variable or define GOOGLE_CLIENT_ID in config.php";
    exit;
}

// Create a state token to prevent request forgery.
$state = bin2hex(random_bytes(16));
$_SESSION['oauth2state'] = $state;
$_SESSION['oauth2next'] = $action;

$scope = urlencode('openid email profile');
$authUrl = 'https://accounts.google.com/o/oauth2/v2/auth'
    . '?response_type=code'
    . '&client_id=' . urlencode($clientId)
    . '&redirect_uri=' . urlencode($redirectUri)
    . '&scope=' . $scope
    . '&state=' . $state
    . '&prompt=select_account';

// Redirect to Google
header('Location: ' . $authUrl);
exit;
