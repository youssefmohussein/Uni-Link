<?php
// ================================
// 📦 User Controller (Admin adds users manually)
// ================================

require_once __DIR__ . '/../utils/DbConnection.php';

class UserController {

    public static function addUser() {
        global $pdo;

        // 🧠 Get JSON input
        $input = json_decode(file_get_contents("php://input"), true);

        // ✅ Step 1: Validate fields
        if (!$input || !isset($input['user_id'], $input['username'], $input['email'], $input['password'], $input['role'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields"
            ]);
            return;
        }

        // ✅ Step 2: Clean data
        $user_id   = (int)$input['user_id'];
        $username  = trim($input['username']);
        $email     = trim($input['email']);
        $password  = password_hash($input['password'], PASSWORD_DEFAULT);
        // Normalize and validate role
        $roleInput = $input['role'];
        $role = ucfirst(strtolower(trim($roleInput)));
        $allowedRoles = ['Student', 'Professor', 'Admin'];
        if (!in_array($role, $allowedRoles, true)) {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid role provided"
            ]);
            return;
        }
        $phone     = $input['phone'] ?? null;
        $faculty_id = $input['faculty_id'] ?? null;
        $major_id   = $input['major_id'] ?? null;

        if (empty($faculty_id) && !empty($input['faculty_name'])) {
        $stmt = $pdo->prepare("SELECT faculty_id FROM Faculty WHERE faculty_name = ?");
        $stmt->execute([$input['faculty_name']]);
        $faculty_id = $stmt->fetchColumn();
        if (!$faculty_id) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid faculty name: " . $input['faculty_name']
        ]);
        return;
        }
        }

        if (empty($major_id) && !empty($input['major_name'])) {
        $stmt = $pdo->prepare("SELECT major_id FROM Major WHERE major_name = ?");
        $stmt->execute([$input['major_name']]);
        $major_id = $stmt->fetchColumn();
        if (!$major_id) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid major name: " . $input['major_name']
        ]);
        return;
        }
        }

        
        try {
            // Use transaction to keep Users and role tables consistent
            $pdo->beginTransaction();
            // ✅ Step 3: Insert into Users
            $stmt = $pdo->prepare("
                INSERT INTO Users (user_id, username, email, password, phone, role, faculty_id, major_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$user_id, $username, $email, $password, $phone, $role, $faculty_id, $major_id]);

            // ✅ Step 4: Insert into role-specific table
            if ($role === 'Student') {
                $pdo->prepare("INSERT INTO Student (student_id) VALUES (?)")->execute([$user_id]);
            } elseif ($role === 'Professor') {
                $pdo->prepare("INSERT INTO Professor (professor_id) VALUES (?)")->execute([$user_id]);
            } elseif ($role === 'Admin') {
                $pdo->prepare("INSERT INTO Admin (admin_id) VALUES (?)")->execute([$user_id]);
            }

            // Commit after successful inserts
            $pdo->commit();

            // ✅ Step 5: Response
            echo json_encode([
                "status" => "success",
                "message" => "$role added successfully",
                "user_id" => $user_id
            ]);

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
  }
    
    public static function getAllUsers() {
        global $pdo;

        try {
            $stmt = $pdo->query("SELECT * FROM Users ORDER BY user_id ASC");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Replace real passwords with *****
            foreach ($users as &$user) {
                $user['password'] = '*****';
            }

            echo json_encode([
                "status" => "success",
                "count" => count($users),
                "data" => $users
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function updateUser() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['user_id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing user_id"
            ]);
            return;
        }

        $user_id = (int)$input['user_id'];

        // Fetch existing user
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existingUser) {
            echo json_encode([
                "status" => "error",
                "message" => "User not found"
            ]);
            return;
        }

        // Keep old values if not provided
        $username   = $input['username'] ?? $existingUser['username'];
        $email      = $input['email'] ?? $existingUser['email'];
        $phone      = $input['phone'] ?? $existingUser['phone'];
        $faculty_id = $input['faculty_id'] ?? $existingUser['faculty_id'];
        $major_id   = $input['major_id'] ?? $existingUser['major_id'];
        $role       = $input['role'] ?? $existingUser['role'];

        // Handle password separately
        if (!empty($input['password'])) {
            $password = password_hash($input['password'], PASSWORD_DEFAULT);
        } else {
            $password = $existingUser['password'];
        }

        try {
            $stmt = $pdo->prepare("
                UPDATE Users 
                SET username = ?, email = ?, password = ?, phone = ?, role = ?, faculty_id = ?, major_id = ?
                WHERE user_id = ?
            ");
            $stmt->execute([$username, $email, $password, $phone, $role, $faculty_id, $major_id, $user_id]);

            echo json_encode([
                "status" => "success",
                "message" => "User updated successfully"
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function deleteUser() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input || !isset($input['user_id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing user_id"
            ]);
            return;
        }

        $user_id = (int)$input['user_id'];

        try {
            $pdo->beginTransaction();

            // Delete from role-specific tables first
            $pdo->prepare("DELETE FROM Student WHERE student_id = ?")->execute([$user_id]);
            $pdo->prepare("DELETE FROM Professor WHERE professor_id = ?")->execute([$user_id]);
            $pdo->prepare("DELETE FROM Admin WHERE admin_id = ?")->execute([$user_id]);

            // Then delete from Users
            $stmt = $pdo->prepare("DELETE FROM Users WHERE user_id = ?");
            $stmt->execute([$user_id]);

            $pdo->commit();

            echo json_encode([
                "status" => "success",
                "message" => "User deleted successfully"
            ]);

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }



}
?>
