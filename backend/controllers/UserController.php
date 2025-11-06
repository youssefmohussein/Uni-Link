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
}
?>
