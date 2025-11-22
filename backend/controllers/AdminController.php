<?php

require_once __DIR__ . '/../utils/DbConnection.php';

class AdminController {

    public static function updateAdmin() {
        global $pdo;
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['admin_id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing admin_id"
            ]);
            return;
        }

        $admin_id = (int)$input['admin_id'];

        // Fetch admin
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE admin_id = ?");
        $stmt->execute([$admin_id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            echo json_encode([
                "status" => "error",
                "message" => "Admin not found"
            ]);
            return;
        }

        // Only status can be edited
        $status = isset($input['status']) ? $input['status'] : $admin['status'];

        if (!in_array($status, ["Active", "Disabled"])) {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid status value"
            ]);
            return;
        }

        try {
            $stmt = $pdo->prepare("
                UPDATE admin 
                SET status = ?
                WHERE admin_id = ?
            ");
            $stmt->execute([$status, $admin_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Admin updated successfully"
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function getAllAdmins() {
    global $pdo;

    try {
        $stmt = $pdo->query("
            SELECT 
                a.admin_id,
                u.email,
                a.created_at,
                a.status
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
?>
    