<?php
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Helpers/Security.php';
require_once __DIR__ . '/../app/Helpers/Session.php';

use App\Controllers\AuthController;

// Simple Autoloader for this file since it might be accessed directly before index.php autoloader runs fully if not careful, 
// but realistically index.php is the entry point. However, login.php is specified as an entry point in prompt.
// We will rely on manual requires here for simplicity as it is a standalone entry outside the main router flow for GET, 
// but form submission usually goes to index.php or self. Let's make it self-contained for display.

// Redirect if already logged in
if (AuthController::isLoggedIn()) {
    header("Location: index.php?page=dashboard");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = AuthController::login($username, $password);
    if ($result['success']) {
        $redirect = \App\Helpers\Session::get('redirect_after_login', 'index.php?page=dashboard');
        \App\Helpers\Session::remove('redirect_after_login');
        
        session_write_close();
        header("Location: $redirect");
        exit();
    } else {
        $error = $result['message'] ?? "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Terra Fusion Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1a;
            color: #c9b078;
            font-family: 'Playfair Display', serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card {
            background-color: #000;
            border: 1px solid #c9b078;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(201, 176, 120, 0.2);
            width: 100%;
            max-width: 400px;
        }
        .form-control {
            background-color: #333;
            border: 1px solid #555;
            color: #fff;
        }
        .form-control:focus {
            background-color: #444;
            color: #fff;
            border-color: #c9b078;
            box-shadow: 0 0 0 0.25rem rgba(201, 176, 120, 0.25);
        }
        .btn-gold {
            background-color: #c9b078;
            color: #000;
            font-weight: bold;
            border: none;
        }
        .btn-gold:hover {
            background-color: #bfa060;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h2 class="text-center mb-4">Terra Fusion</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-gold w-100">Login</button>
        </form>
    </div>
</body>
</html>
