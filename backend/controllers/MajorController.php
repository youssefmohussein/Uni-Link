<?php

require_once __DIR__ . '/../utils/DbConnection.php';

class MajorController {

    public static function addMajor() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (
            !$input ||
            !isset($input['major_name'], $input['faculty_id']) ||
            empty(trim($input['major_name']))
        ) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields: major_name or faculty_id"
            ]);
            return;
        }

        $major_name = trim($input['major_name']);
        $faculty_id = (int)$input['faculty_id'];

        try {
            // Check if faculty exists
            $checkFaculty = $pdo->prepare("SELECT * FROM Faculty WHERE faculty_id = ?");
            $checkFaculty->execute([$faculty_id]);
            if (!$checkFaculty->fetch()) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Invalid faculty_id: Faculty not found"
                ]);
                return;
            }

            // Check duplicate under same faculty
            $checkMajor = $pdo->prepare("SELECT * FROM Major WHERE major_name = ? AND faculty_id = ?");
            $checkMajor->execute([$major_name, $faculty_id]);
            if ($checkMajor->fetch()) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Major already exists for this faculty"
                ]);
                return;
            }

            // Insert new major
            $stmt = $pdo->prepare("INSERT INTO Major (major_name, faculty_id) VALUES (?, ?)");
            $stmt->execute([$major_name, $faculty_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Major added successfully"
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }


    public static function getAllMajors() {
        global $pdo;

        try {
            $stmt = $pdo->query("
                SELECT 
                    m.major_id, 
                    m.major_name, 
                    m.faculty_id, 
                    f.faculty_name 
                FROM Major m
                JOIN Faculty f ON m.faculty_id = f.faculty_id
                ORDER BY m.major_id ASC
            ");

            $majors = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "count" => count($majors),
                "data" => $majors
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }


    // ⭐ NEW: Get majors by faculty
    public static function getMajorsByFaculty($faculty_id) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("
                SELECT major_id, major_name 
                FROM Major 
                WHERE faculty_id = ?
                ORDER BY major_name ASC
            ");
            $stmt->execute([(int)$faculty_id]);

            $majors = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "count" => count($majors),
                "data" => $majors
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }


    public static function updateMajor() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (
            !$input ||
            !isset($input['major_id'], $input['major_name'], $input['faculty_id'])
        ) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields: major_id, major_name, faculty_id"
            ]);
            return;
        }

        $major_id = (int)$input['major_id'];
        $major_name = trim($input['major_name']);
        $faculty_id = (int)$input['faculty_id'];

        // Validate major exists
        $stmt = $pdo->prepare("SELECT * FROM Major WHERE major_id = ?");
        $stmt->execute([$major_id]);
        if (!$stmt->fetch()) {
            echo json_encode([
                "status" => "error",
                "message" => "Major not found"
            ]);
            return;
        }

        // Validate faculty exists
        $checkFaculty = $pdo->prepare("SELECT * FROM Faculty WHERE faculty_id = ?");
        $checkFaculty->execute([$faculty_id]);
        if (!$checkFaculty->fetch()) {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid faculty_id: Faculty not found"
            ]);
            return;
        }

        // Check duplicate after update
        $checkDuplicate = $pdo->prepare("
            SELECT * FROM Major 
            WHERE major_name = ? AND faculty_id = ? AND major_id != ?
        ");
        $checkDuplicate->execute([$major_name, $faculty_id, $major_id]);

        if ($checkDuplicate->fetch()) {
            echo json_encode([
                "status" => "error",
                "message" => "Another major with this name already exists in the same faculty"
            ]);
            return;
        }

        try {
            $stmt = $pdo->prepare("
                UPDATE Major 
                SET major_name = ?, faculty_id = ? 
                WHERE major_id = ?
            ");
            $stmt->execute([$major_name, $faculty_id, $major_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Major updated successfully"
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }


    public static function deleteMajor() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['major_id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing major_id"
            ]);
            return;
        }

        $major_id = (int)$input['major_id'];

        try {

            // ⭐ Prevent deleting major if users depend on it
            $checkUsers = $pdo->prepare("
                SELECT user_id FROM Users WHERE major_id = ?
            ");
            $checkUsers->execute([$major_id]);

            if ($checkUsers->fetch()) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Cannot delete major because users are assigned to it"
                ]);
                return;
            }

            $stmt = $pdo->prepare("DELETE FROM Major WHERE major_id = ?");
            $stmt->execute([$major_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Major deleted successfully"
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
