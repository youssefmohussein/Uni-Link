<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Controllers\AuthController;

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        AuthController::checkAccess('Manager');
        
        $users = $this->userModel->getAll();
        
        $data = [
            'users' => $users
        ];

        $content = __DIR__ . '/../Views/users/index.php';
        include __DIR__ . '/../Views/shared/layout.php';
    }

    public function create()
    {
        AuthController::checkAccess('Manager');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'role' => $_POST['role']
            ];

            try {
                $this->userModel->create($data);
                \App\Helpers\Session::setFlash('success', 'User created successfully.');
            } catch (\PDOException $e) {
                if ($e->getCode() == 23000) {
                    \App\Helpers\Session::setFlash('error', 'A user with this email already exists.');
                } else {
                    \App\Helpers\Session::setFlash('error', 'Database error: ' . $e->getMessage());
                }
            }
            
            header("Location: index.php?page=users");
            exit();
        }
    }

    public function update()
    {
        AuthController::checkAccess('Manager');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['userId'];
            $data = [
                'email' => $_POST['email'],
                'role' => $_POST['role']
            ];
            
            try {
                if (!empty($_POST['password'])) {
                    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $this->userModel->updatePassword($id, $hash);
                }

                $this->userModel->update($id, $data);
                \App\Helpers\Session::setFlash('success', 'User updated successfully.');
            } catch (\PDOException $e) {
                if ($e->getCode() == 23000) {
                    \App\Helpers\Session::setFlash('error', 'A user with this email already exists.');
                } else {
                    \App\Helpers\Session::setFlash('error', 'Database error: ' . $e->getMessage());
                }
            }

            header("Location: index.php?page=users");
            exit();
        }
    }

    public function delete()
    {
        AuthController::checkAccess('Manager');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userId'])) {
            $id = $_POST['userId'];
            
            try {
                // Prevent deleting yourself
                if ($id == \App\Helpers\Session::get('user_id')) {
                    \App\Helpers\Session::setFlash('error', 'You cannot delete your own account.');
                } else {
                    $this->userModel->delete($id);
                    \App\Helpers\Session::setFlash('success', 'User deleted successfully.');
                }
            } catch (\PDOException $e) {
                \App\Helpers\Session::setFlash('error', 'Database error: ' . $e->getMessage());
            }

            header("Location: index.php?page=users");
            exit();
        }
    }
}
