<?php
// oauth/google_callback.php - Handle Google OAuth callback
session_start();
require_once __DIR__ . '/../config.php';

$clientId = getenv('GOOGLE_CLIENT_ID') ?: (defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : null);
$clientSecret = getenv('GOOGLE_CLIENT_SECRET') ?: (defined('GOOGLE_CLIENT_SECRET') ? GOOGLE_CLIENT_SECRET : null);

if (!$clientId || !$clientSecret) {
    echo "Google OAuth is not configured. Set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET environment variables or define them in config.php";
    exit;
}

// Validate state
if (empty($_GET['state']) || empty($_SESSION['oauth2state']) || !hash_equals($_SESSION['oauth2state'], $_GET['state'])) {
    error_log('Invalid OAuth state');
    die('Invalid OAuth state');
}

if (!isset($_GET['code'])) {
    die('No code returned');
}

$code = $_GET['code'];
// Build redirect URI same as before
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$redirectUri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $basePath . '/google_callback.php';

// Exchange code for tokens
$tokenUrl = 'https://oauth2.googleapis.com/token';
$postFields = http_build_query([
    'code' => $code,
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'redirect_uri' => $redirectUri,
    'grant_type' => 'authorization_code',
]);

$ch = curl_init($tokenUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
$response = curl_exec($ch);
if (curl_errno($ch)) {
    error_log('cURL error: ' . curl_error($ch));
    die('Error fetching token');
}
curl_close($ch);

$tokenData = json_decode($response, true);
if (empty($tokenData['access_token'])) {
    error_log('Token response: ' . $response);
    die('Failed to obtain access token');
}

$accessToken = $tokenData['access_token'];

// Fetch userinfo
$ch = curl_init('https://openidconnect.googleapis.com/v1/userinfo');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
$userInfo = curl_exec($ch);
if (curl_errno($ch)) {
    error_log('cURL error (userinfo): ' . curl_error($ch));
    die('Error fetching user info');
}
curl_close($ch);

$user = json_decode($userInfo, true);
if (empty($user['email'])) {
    error_log('Userinfo: ' . $userInfo);
    die('Failed to obtain user email from Google');
}

$email = $user['email'];
$fullName = $user['name'] ?? $user['email'];
$googleId = $user['sub'] ?? null;

try {
    // Use existing $pdo from config.php if present, else create new
    if (!isset($pdo)) {
        $host = 'localhost'; $db = 'terra_fusion'; $userdb = 'root'; $pass = ''; $charset = 'utf8mb4';
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $userdb, $pass, $options);
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT user_id, full_name, email, role, phone FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Log in existing user
        $_SESSION['user_id'] = $existing['user_id'];
        $_SESSION['full_name'] = $existing['full_name'];
        $_SESSION['email'] = $existing['email'];
        $_SESSION['user_phone'] = $existing['phone'] ?? '';
        $_SESSION['role'] = $existing['role'] ?? 'Customer';
        $_SESSION['logged_in'] = true;

    } else {
        // Create a new user
        $random_password = bin2hex(random_bytes(8));
        $hashed_password = password_hash($random_password, PASSWORD_DEFAULT);
        $role = 'Waiter';
        $is_active = 1;

        $insert = $pdo->prepare("INSERT INTO users (full_name, email, phone, password_hash, role, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $insert->execute([$fullName, $email, '', $hashed_password, $role, $is_active]);
        $userId = $pdo->lastInsertId();

        $_SESSION['user_id'] = $userId;
        $_SESSION['full_name'] = $fullName;
        $_SESSION['email'] = $email;
        $_SESSION['user_phone'] = '';
        $_SESSION['role'] = $role;
        $_SESSION['logged_in'] = true;
    }

    // small role_id mapping as in auth.php
    $roleMap = ['Manager' => 4, 'Chef Boss' => 3, 'Table Manager' => 2, 'Waiter' => 1];
    $_SESSION['role_id'] = $roleMap[$_SESSION['role']] ?? 1;

    // Fingerprint
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $_SESSION['fingerprint'] = hash('sha256', $userAgent . $ip);

    // Redirect to profile
    header('Location: /TerraFusion/userprofile.php');
    exit;

} catch (PDOException $e) {
    error_log('DB error during Google OAuth: ' . $e->getMessage());
    die('Database error');
}
