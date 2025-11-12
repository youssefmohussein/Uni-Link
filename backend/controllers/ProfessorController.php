<?php

require_once __DIR__ . '/../utils/DbConnection.php';

class ProfessorController {

    public static function addProfessor() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (
            !$input ||
            !isset($input['professor_id'], $input['department'], $input['specialization']) ||
            empty(trim($input['department'])) ||
            empty(trim($input['specialization']))
        ) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields: professor_id, department, specialization"
            ]);
            return;
        }

        $professor_id   = (int)$input['professor_id'];
        $department     = trim($input['department']);
        $specialization = trim($input['specialization']);

        try {
            // ✅ Check if professor_id exists in Users
            $checkUser = $pdo->prepare("SELECT * FROM Users WHERE user_id = ?");
            $checkUser->execute([$professor_id]);
            if (!$checkUser->fetch()) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Invalid professor_id: User not found"
                ]);
                return;
            }

            // ✅ Check if already exists in Professor table
            $checkProf = $pdo->prepare("SELECT * FROM Professor WHERE professor_id = ?");
            $checkProf->execute([$professor_id]);
            if ($checkProf->fetch()) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Professor record already exists"
                ]);
                return;
            }

            // ✅ Insert new professor record
            $stmt = $pdo->prepare("
                INSERT INTO Professor (professor_id, department, specialization)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$professor_id, $department, $specialization]);

            echo json_encode([
                "status" => "success",
                "message" => "Professor added successfully"
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function getAllProfessors() {
        global $pdo;

        try {
            $stmt = $pdo->query("
                SELECT 
                    p.professor_id, 
                    u.username,
                    u.email,
                    p.department, 
                    p.specialization
                FROM Professor p
                JOIN Users u ON p.professor_id = u.user_id
                ORDER BY p.professor_id ASC
            ");
            $professors = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "count" => count($professors),
                "data" => $professors
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }


    public static function updateProfessor() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (
            !$input ||
            !isset($input['professor_id']) ||
            (!isset($input['department']) && !isset($input['specialization']))
        ) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields: professor_id and (department or specialization)"
            ]);
            return;
        }

        $professor_id   = (int)$input['professor_id'];
        $department     = isset($input['department']) ? trim($input['department']) : null;
        $specialization = isset($input['specialization']) ? trim($input['specialization']) : null;

        // ✅ Validate professor exists
        $stmt = $pdo->prepare("SELECT * FROM Professor WHERE professor_id = ?");
        $stmt->execute([$professor_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existing) {
            echo json_encode([
                "status" => "error",
                "message" => "Professor not found"
            ]);
            return;
        }

        // Keep old values if not provided
        $department     = $department ?: $existing['department'];
        $specialization = $specialization ?: $existing['specialization'];

        try {
            $stmt = $pdo->prepare("
                UPDATE Professor
                SET department = ?, specialization = ?
                WHERE professor_id = ?
            ");
            $stmt->execute([$department, $specialization, $professor_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Professor updated successfully"
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
