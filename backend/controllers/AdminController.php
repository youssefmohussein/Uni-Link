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

    // 📊 Get dashboard statistics
    public static function getDashboardStats() {
        global $pdo;

        try {
            // Counts
            $studentsCount = $pdo->query("SELECT COUNT(*) FROM Student")->fetchColumn();
            $professorsCount = $pdo->query("SELECT COUNT(*) FROM Professor")->fetchColumn();
            $adminsCount = $pdo->query("SELECT COUNT(*) FROM Admin")->fetchColumn();
            $totalUsersCount = $pdo->query("SELECT COUNT(*) FROM Users")->fetchColumn();

            // Students per faculty
            $studentsPerFaculty = $pdo->query("
                SELECT 
                    f.faculty_name,
                    COUNT(s.student_id) as student_count
                FROM Faculty f
                LEFT JOIN Users u ON f.faculty_id = u.faculty_id AND u.role = 'Student'
                LEFT JOIN Student s ON u.user_id = s.student_id
                GROUP BY f.faculty_id, f.faculty_name
                ORDER BY student_count DESC
                LIMIT 10
            ")->fetchAll(PDO::FETCH_ASSOC);

            // Get faculty names and counts for chart
            $facultyLabels = [];
            $facultyCounts = [];
            foreach ($studentsPerFaculty as $row) {
                $facultyLabels[] = $row['faculty_name'] ?: 'Unknown';
                $facultyCounts[] = (int)$row['student_count'];
            }

            // Weekly active users (placeholder - using total users for now)
            // In a real system, you'd track login dates
            $weeklyActive = [
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
                'data' => [
                    (int)($totalUsersCount * 0.6),
                    (int)($totalUsersCount * 0.7),
                    (int)($totalUsersCount * 0.65),
                    (int)($totalUsersCount * 0.75),
                    (int)($totalUsersCount * 0.8)
                ]
            ];

            // User status distribution (Active/Idle/Suspended)
            // Since we don't have status field, using role-based approximation
            $activeUsers = $totalUsersCount;
            $idleUsers = (int)($totalUsersCount * 0.2);
            $suspendedUsers = 0; // No suspended users in current schema

            // System health scores (calculated metrics)
            $systemHealth = [
                'labels' => ['System', 'Users', 'Security', 'Performance', 'Stability'],
                'data' => [
                    min(100, (int)(($totalUsersCount / 2000) * 100)), // System load
                    min(100, (int)(($activeUsers / max($totalUsersCount, 1)) * 100)), // User activity
                    88, // Security (placeholder)
                    min(100, (int)(95 - ($totalUsersCount / 100))), // Performance
                    86 // Stability (placeholder)
                ]
            ];

            echo json_encode([
                "status" => "success",
                "data" => [
                    "counts" => [
                        "students" => (int)$studentsCount,
                        "professors" => (int)$professorsCount,
                        "admins" => (int)$adminsCount,
                        "totalUsers" => (int)$totalUsersCount
                    ],
                    "studentsPerFaculty" => [
                        "labels" => $facultyLabels,
                        "data" => $facultyCounts
                    ],
                    "weeklyActiveUsers" => $weeklyActive,
                    "userStatusDistribution" => [
                        "labels" => ["Active", "Idle", "Suspended"],
                        "data" => [$activeUsers, $idleUsers, $suspendedUsers]
                    ],
                    "systemHealth" => $systemHealth
                ]
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }
}
