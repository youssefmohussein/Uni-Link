<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Helpers\Security;
use App\Helpers\Session;
use PDOException;

class ProfileController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $userId = Session::get('user_id');
        $user = $this->userModel->getById($userId);
        
        if (!$user) {
            Session::setFlash('error', 'User not found');
            header("Location: index.php?page=dashboard");
            exit();
        }
        
        $flashSuccess = Session::hasFlash('success') ? Session::getFlash('success') : null;
        $flashError = Session::hasFlash('error') ? Session::getFlash('error') : null;
        
        $data = [
            'user' => $user,
            'csrfToken' => Security::generateCSRFToken(),
            'flashSuccess' => $flashSuccess,
            'flashError' => $flashError
        ];

        extract($data);
        $content = __DIR__ . '/../Views/profile/index.php';
        include __DIR__ . '/../Views/shared/layout.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?page=profile");
            exit();
        }

        error_log('PROFILE UPDATE: session id before = ' . session_id());
        error_log('PROFILE UPDATE: SESSION = ' . print_r($_SESSION, true));
        error_log('PROFILE UPDATE: entered controller');
        error_log('POST: ' . print_r($_POST, true));

        try {
            // Verify CSRF token
            Security::verifyCSRFToken($_POST['csrf_token'] ?? '');
            
            $userId = Session::get('user_id');
            $user = $this->userModel->getById($userId);
            
            error_log('PROFILE UPDATE: user row = ' . print_r($user, true));
            
            if (!$user) {
                throw new \Exception('User not found');
            }
            
            // Prepare updated data
            $data = [
                'username' => Security::sanitize($_POST['username'] ?? ''),
                'full_name' => Security::sanitize($_POST['full_name'] ?? ''),
                'email' => filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL)
            ];
            
            // Validate required fields
            if (empty($data['username']) || empty($data['full_name'])) {
                throw new \Exception('Username and full name are required');
            }
            
            // Check if email is valid if provided
            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Invalid email address');
            }
            
            // Check if password is being updated
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            error_log('PROFILE UPDATE: current=' . $currentPassword . ' new=' . $newPassword . ' confirm=' . $confirmPassword);
            
            $passwordUpdated = false;
            if (!empty($currentPassword)) {
                $beforePassword = $user['password_hash'];
                
                if (!password_verify(\App\Helpers\Security::sanitize($currentPassword), $user['password_hash'])) {
                    throw new \Exception('Current password is incorrect');
                }
                
                if (empty($newPassword)) {
                    throw new \Exception('New password is required');
                }
                
                if (!Security::isPasswordStrong($newPassword)) {
                    throw new \Exception('Password must be at least 8 characters long and include uppercase, lowercase, number, and special character');
                }
                
                if ($newPassword !== $confirmPassword) {
                    throw new \Exception('New passwords do not match');
                }
                
                $newHash = password_hash(\App\Helpers\Security::sanitize($newPassword), PASSWORD_DEFAULT);
                error_log('PROFILE UPDATE: new hash = ' . $newHash);
                
                if ($this->userModel->updatePassword($userId, $newHash)) {
                    $passwordUpdated = true;
                    
                    // Integrity check
                    $after = $this->userModel->getById($userId);
                    if ($beforePassword === $after['password_hash']) {
                        error_log('PASSWORD UPDATE ERROR: hash did not change');
                    }
                    if ($user['username'] !== $after['username'] || $user['full_name'] !== $after['full_name'] || $user['email'] !== $after['email']) {
                        error_log('PASSWORD UPDATE WARNING: non-password fields changed unexpectedly');
                    }
                } else {
                    throw new \Exception('Failed to update password');
                }
            }
            
            // Check if other fields changed
            $fieldsChanged = false;
            if ($data['username'] !== $user['username'] || $data['full_name'] !== $user['full_name'] || $data['email'] !== $user['email']) {
                $fieldsChanged = true;
            }
            
            if ($fieldsChanged) {
                // Update only the changed fields
                $updateData = [];
                if ($data['username'] !== $user['username']) $updateData['username'] = $data['username'];
                if ($data['full_name'] !== $user['full_name']) $updateData['full_name'] = $data['full_name'];
                if ($data['email'] !== $user['email']) $updateData['email'] = $data['email'];
                
                if (!$this->userModel->update($userId, $updateData)) {
                    throw new \Exception('Failed to update profile');
                }
            }
            
            // Update session data if username or full name changed
            if ($data['username'] !== $user['username'] || $data['full_name'] !== $user['full_name']) {
                Session::set('username', $data['username']);
                Session::set('full_name', $data['full_name']);
            }
            
            Session::setFlash('success', 'Profile updated successfully');
            
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            error_log('PROFILE UPDATE: exception: ' . $e->getMessage());
        }
        
        header("Location: index.php?page=profile");
        exit();
    }
}
