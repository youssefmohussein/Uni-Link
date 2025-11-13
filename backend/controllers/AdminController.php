<?php

require_once __DIR__ . '/../utils/DbConnection.php';

class AdminController {

    // 🔁 Update existing admin only
    public static function updateAdmin() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        // 🧠 Validate input
        if (!$input || !isset($input['admin_id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required field: admin_id"
            ]);
            return;
        }

        $admin_id = $input['admin_id'];
        $privilege = isset($input['privilege_level']) ? $input['privilege_level'] : null;
        $created_by = isset($input['created_by_admin_id']) ? $input['created_by_admin_id'] : null;

        try {
            // 🔍 Check if admin exists
            $checkStmt = $pdo->prepare("SELECT * FROM Admin WHERE admin_id = ?");
            $checkStmt->execute([$admin_id]);
            $exists = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if (!$exists) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Admin not found"
                ]);
                return;
            }

            // ✅ Update only allowed fields
            $fields = [];
            $values = [];

            if ($privilege !== null) {
                $fields[] = "privilege_level = ?";
                $values[] = $privilege;
            }

            if ($created_by !== null) {
                $fields[] = "created_by_admin_id = ?";
                $values[] = $created_by;
            }

            if (empty($fields)) {
                echo json_encode([
                    "status" => "error",
                    "message" => "No fields to update"
                ]);
                return;
            }

            $values[] = $admin_id;

            $sql = "UPDATE Admin SET " . implode(", ", $fields) . " WHERE admin_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

            echo json_encode([
                "status" => "success",
                "message" => "Admin updated successfully",
                "data" => [
                    "admin_id" => $admin_id,
                    "privilege_level" => $privilege,
                    "created_by_admin_id" => $created_by
                ]
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    // ✅ Get all admins
    public static function getAllAdmins() {
        global $pdo;

        try {
            $stmt = $pdo->query("
                SELECT 
                    a.admin_id,
                    u.username,
                    u.email,
                    a.privilege_level,
                    a.created_by_admin_id
                FROM Admin a
                JOIN Users u ON a.admin_id = u.user_id
                ORDER BY a.admin_id ASC
            ");
            $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "count" => count($admins),
                "data" => $admins
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }
}
