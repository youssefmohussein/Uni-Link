<?php

require_once __DIR__ . '/../utils/DbConnection.php';

class FacultyController {

    public static function addFaculty() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['faculty_name']) || empty(trim($input['faculty_name']))) {
            echo json_encode([
                "status" => "error",
                "message" => "Faculty name is required"
            ]);
            return;
        }

        $faculty_name = trim($input['faculty_name']);

        try {
            // Check if name already exists
            $check = $pdo->prepare("SELECT * FROM Faculty WHERE faculty_name = ?");
            $check->execute([$faculty_name]);
            if ($check->fetch()) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Faculty already exists"
                ]);
                return;
            }

            // Insert new faculty
            $stmt = $pdo->prepare("INSERT INTO Faculty (faculty_name) VALUES (?)");
            $stmt->execute([$faculty_name]);

            echo json_encode([
                "status" => "success",
                "message" => "Faculty added successfully"
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    } 

    public static function getAllFaculties() {
        global $pdo;

        try {
            $stmt = $pdo->query("SELECT * FROM Faculty ORDER BY faculty_id ASC");
            $faculties = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "count" => count($faculties),
                "data" => $faculties
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function updateFaculty() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input || !isset($input['faculty_id'], $input['faculty_name'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing faculty_id or faculty_name"
            ]);
            return;
        }

        $faculty_id = (int)$input['faculty_id'];
        $faculty_name = trim($input['faculty_name']);

        // Validate
        $stmt = $pdo->prepare("SELECT * FROM Faculty WHERE faculty_id = ?");
        $stmt->execute([$faculty_id]);
        if (!$stmt->fetch()) {
            echo json_encode([
                "status" => "error",
                "message" => "Faculty not found"
            ]);
            return;
        }

        try {
            $stmt = $pdo->prepare("UPDATE Faculty SET faculty_name = ? WHERE faculty_id = ?");
            $stmt->execute([$faculty_name, $faculty_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Faculty updated successfully"
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function deleteFaculty() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input || !isset($input['faculty_id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing faculty_id"
            ]);
            return;
        }

        $faculty_id = (int)$input['faculty_id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM Faculty WHERE faculty_id = ?");
            $stmt->execute([$faculty_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Faculty deleted successfully"
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
