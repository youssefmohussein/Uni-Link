<?php

require_once __DIR__ . '/../utils/DbConnection.php';

class ProfessorController {
 
    public static function addProfessor() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (
            !$input ||
            !isset($input['professor_id'], $input['academic_rank'], $input['office_location'])
        ) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields: professor_id, academic_rank, office_location"
            ]);
            return;
        }

        $professor_id    = (int)$input['professor_id'];
        $academic_rank   = trim($input['academic_rank']);
        $office_location = trim($input['office_location']);

        // ✅ Check user exists and role is Professor
        $stmt = $pdo->prepare("SELECT role FROM Users WHERE user_id = ?");
        $stmt->execute([$professor_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode([
                "status" => "error",
                "message" => "User not found"
            ]);
            return;
        }

        if ($user['role'] !== 'Professor') {
            echo json_encode([
                "status" => "error",
                "message" => "User role must be 'Professor' to add as professor"
            ]);
            return;
        }

        // ✅ Check if professor record already exists
        $stmt = $pdo->prepare("SELECT * FROM Professor WHERE professor_id = ?");
        $stmt->execute([$professor_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        try {
            if ($existing) {
                // 🔁 Update existing record
                $stmt = $pdo->prepare("
                    UPDATE Professor
                    SET academic_rank = ?, office_location = ?
                    WHERE professor_id = ?
                ");
                $stmt->execute([$academic_rank, $office_location, $professor_id]);

                echo json_encode([
                    "status" => "success",
                    "message" => "Existing professor record updated successfully"
                ]);
            } else {
                // 🆕 Insert new record
                $stmt = $pdo->prepare("
                    INSERT INTO Professor (professor_id, academic_rank, office_location)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$professor_id, $academic_rank, $office_location]);

                echo json_encode([
                    "status" => "success",
                    "message" => "New professor record created successfully"
                ]);
            }
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

    if (!$input || !isset($input['professor_id'])) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing professor_id"
        ]);
        return;
    }

    $professor_id = (int)$input['professor_id'];

    // Fetch existing record
    $stmt = $pdo->prepare("SELECT * FROM Professor WHERE professor_id = ?");
    $stmt->execute([$professor_id]);
    $prof = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prof) {
        echo json_encode([
            "status" => "error",
            "message" => "Professor not found"
        ]);
        return;
    }

    // Prepare update fields
    $fields = [];
    $values = [];

    if (isset($input['academic_rank']) && trim($input['academic_rank']) !== '') {
        $fields[] = "academic_rank = ?";
        $values[] = trim($input['academic_rank']);
    }

    if (isset($input['office_location']) && trim($input['office_location']) !== '') {
        $fields[] = "office_location = ?";
        $values[] = trim($input['office_location']);
    }

    if (empty($fields)) {
        echo json_encode([
            "status" => "error",
            "message" => "No fields to update"
        ]);
        return;
    }

    // Append professor_id for WHERE clause
    $values[] = $professor_id;

    try {
        $sql = "UPDATE Professor SET " . implode(", ", $fields) . " WHERE professor_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);

        echo json_encode([
            "status" => "success",
            "message" => "Professor record updated successfully"
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
                    u.phone,
                    u.profile_image,
                    u.bio,
                    u.job_title,
                    f.faculty_name,
                    m.major_name,
                    p.academic_rank,
                    p.office_location
                FROM Professor p
                JOIN Users u ON p.professor_id = u.user_id
                LEFT JOIN faculty f ON u.faculty_id = f.faculty_id
                LEFT JOIN major m ON u.major_id = m.major_id
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

}

?>
