<?php
session_start();
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $stmt = $pdo->prepare("SELECT user_id, password_hash, full_name, email, phone, role, is_active FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['is_active'] == 0) {
                $error = 'Account is inactive. Please contact support.';
            } else {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name']; // Use full_name key for consistency
                $_SESSION['user_name'] = $user['full_name']; // Keep backward compat if needed, but prefer full_name
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_phone'] = $user['phone']; // keeping legacy session key for now
                $_SESSION['role'] = $user['role'];

                // Update last_login
                $updateParams = [$user['user_id']];
                $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
                $updateStmt->execute($updateParams);
                
                // Redirect based on role
                if ($user['role'] === 'Waiter') {
                     header('Location: Admin/public/index.php?page=orders');
                } elseif (in_array($user['role'], ['Manager', 'Chef Boss', 'Table Manager'])) {
                     header('Location: Admin/public/index.php');
                } else {
                     // Default for Customer and any other roles
                     header('Location: userprofile.php');
                }
                exit;
            }
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - TerraFusion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/userprofile.css">
</head>
<body class="bg-dark">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <form method="POST" action="login.php">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="register.php" class="text-gold">Don't have an account? Register</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
