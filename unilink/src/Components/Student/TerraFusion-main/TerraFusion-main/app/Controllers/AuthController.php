<?php

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Models\User;
use App\Middlewares\GuestMiddleware;

class AuthController extends BaseController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * Show login form
     */
    public function showLogin(): void
    {
        $middleware = new GuestMiddleware();
        $middleware->handle();
        
        $this->view('auth/login');
    }

    /**
     * Handle login
     */
    public function login(): void
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        
        $errors = [];
        
        if (empty($email) || empty($password)) {
            $errors[] = 'Email and password are required';
        }
        
        if (empty($errors)) {
            $user = $this->userRepository->findByEmail($email);
            
            if ($user && password_verify($password, $user->password_hash)) {
                // Regenerate session ID on login
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $user->user_id;
                $_SESSION['user'] = $user;
                $_SESSION['last_activity'] = time();
                
                // Redirect admin to admin page
                if ($user->email === 'admin@gmail.com' || $user->password_hash === 'admin123') {
                    $adminUrl = url('Admin/adminpage.html');
                    header("Location: $adminUrl");
                    exit;
                }
                
                // For other users
                $redirectUrl = $_SESSION['redirect_after_login'] ?? url('customer/menu');
                unset($_SESSION['redirect_after_login']);
                
                if ($user->role === 'Chef Boss' || $user->role === 'Manager') {
                    $redirectUrl = url('staff/dashboard');
                }
                
                flash('success', 'Welcome back, ' . $user->full_name . '!');
                $this->redirect($redirectUrl);
                return;
            }
            
            $errors[] = 'Invalid email or password';
        }
        
        $_SESSION['old_input'] = $_POST;
        foreach ($errors as $error) {
            flash('error', $error);
        }
        $this->redirect(url('login'));
    }

    /**
     * Show register form
     */
    public function showRegister(): void
    {
        $middleware = new GuestMiddleware();
        $middleware->handle();
        
        $this->view('auth/register');
    }

    /**
     * Handle registration
     */
    public function register(): void
    {
        $data = [
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'full_name' => filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING),
            'phone' => filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING)
        ];
        
        $errors = $this->validateRequired($data, ['email', 'password', 'full_name']);
        
        // Check if email exists
        if (empty($errors)) {
            $existingUser = $this->userRepository->findByEmail($data['email']);
            if ($existingUser) {
                $errors[] = 'Email already registered';
            }
        }
        
        // Validate password match
        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Passwords do not match';
        }
        
        // Validate password length
        if (strlen($data['password']) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }
        
        if (empty($errors)) {
            // Check if this is the admin registration
            if ($data['email'] === 'admin@gmail.com') {
                $data['role'] = 'Manager';
            } else {
                $data['role'] = 'Waiter'; // Default role
            }
            
            unset($data['password_confirm']);
            
            $user = $this->userRepository->create($data);
            
            // Auto-login after registration
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user->user_id;
            $_SESSION['user'] = $user;
            $_SESSION['last_activity'] = time();
            
            // Redirect admin to admin page
            if ($data['email'] === 'admin@gmail.com') {
                $adminUrl = url('Admin/adminpage.html');
                header("Location: $adminUrl");
                exit;
            }
            
            flash('success', 'Registration successful! Welcome to TerraFusion.');
            $this->redirect(url('customer/menu'));
            return;
        }
        
        $_SESSION['old_input'] = $_POST;
        foreach ($errors as $error) {
            flash('error', $error);
        }
        $this->redirect(url('register'));
    }

    /**
     * Handle logout
     */
    public function logout(): void
    {
        session_destroy();
        flash('success', 'You have been logged out successfully.');
        $this->redirect(url('login'));
    }
}

