<?php
session_start();

// Database configuration
$host = 'localhost';
$db   = 'terra_fusion';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, $options);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]));
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        try {
            $stmt = $pdo->prepare("SELECT user_id, password_hash, full_name, email, phone, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_phone'] = $user['phone'];
                $_SESSION['role'] = $user['role'];
                
                // Role ID mapping for Admin dashboard visibility
                $roleMap = ['Manager' => 4, 'Chef Boss' => 3, 'Table Manager' => 2, 'Waiter' => 1];
                $_SESSION['role_id'] = $roleMap[$user['role']] ?? 1;
                
                $_SESSION['logged_in'] = true;
                
                // Fingerprint for Admin dashboard compatibility
                $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI';
                $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
                $_SESSION['fingerprint'] = hash('sha256', $userAgent . $ip);

                $redirect = 'userprofile.php'; // Default for Customer and others
                
                if ($user['role'] === 'Waiter') {
                    $redirect = 'Admin/public/index.php?page=orders';
                } elseif (in_array($user['role'], ['Manager', 'Chef Boss', 'Table Manager'])) {
                    $redirect = 'Admin/public/index.php';
                }
                
                echo json_encode(['success' => true, 'redirect' => $redirect]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error occurred']);
        }
    } 
    elseif ($action === 'register') {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($username) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
            echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
            exit;
        }

        if ($password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
            exit;
        }

        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Email already registered']);
                exit;
            }

            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'Waiter'; // Default role
            $is_active = 1;
            
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password_hash, role, is_active) VALUES (?, ?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$username, $email, $phone, $hashed_password, $role, $is_active])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['full_name'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['user_phone'] = $phone;
                $_SESSION['role'] = $role;
                $_SESSION['role_id'] = 1; // Default 'Waiter' level
                $_SESSION['logged_in'] = true;
                
                // Fingerprint for Admin dashboard compatibility
                $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI';
                $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
                $_SESSION['fingerprint'] = hash('sha256', $userAgent . $ip);
                
                echo json_encode(['success' => true, 'redirect' => 'index.php']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
            }
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
        }
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
