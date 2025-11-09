<?php

require_once __DIR__ . '/../utils/DbConnection.php';

class StudentController {

    
    public static function addStudent() {
        global $pdo;
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['student_id'], $input['year'], $input['gpa'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields: student_id, year, or gpa"
            ]);
            return;
        }

        $student_id = (int)$input['student_id'];
        $year = (int)$input['year'];
        $gpa = (float)$input['gpa'];

        // ✅ Check if user exists and is a student
        $stmt = $pdo->prepare("SELECT role FROM Users WHERE user_id = ?");
        $stmt->execute([$student_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode([
                "status" => "error",
                "message" => "User not found"
            ]);
            return;
        }

        if ($user['role'] !== 'Student') {
            echo json_encode([
                "status" => "error",
                "message" => "User must have role 'Student' to be added as a student"
            ]);
            return;
        }

        // ✅ Check if student record already exists
        $stmt = $pdo->prepare("SELECT * FROM Student WHERE student_id = ?");
        $stmt->execute([$student_id]);
        $existingStudent = $stmt->fetch(PDO::FETCH_ASSOC);

        try {
            if ($existingStudent) {
                // 🔁 Update existing student record
                $stmt = $pdo->prepare("
                    UPDATE Student 
                    SET year = ?, gpa = ?
                    WHERE student_id = ?
                ");
                $stmt->execute([$year, $gpa, $student_id]);

                echo json_encode([
                    "status" => "success",
                    "message" => "Existing student record updated successfully"
                ]);
            } else {
                // 🆕 Create new student record
                $stmt = $pdo->prepare("
                    INSERT INTO Student (student_id, year, gpa, points)
                    VALUES (?, ?, ?, 0)
                ");
                $stmt->execute([$student_id, $year, $gpa]);

                echo json_encode([
                    "status" => "success",
                    "message" => "New student record created successfully"
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    
    public static function updateStudent() {
        global $pdo;
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['student_id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing student_id"
            ]);
            return;
        }

        $student_id = (int)$input['student_id'];

        // Get existing student
        $stmt = $pdo->prepare("SELECT * FROM Student WHERE student_id = ?");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            echo json_encode([
                "status" => "error",
                "message" => "Student not found"
            ]);
            return;
        }

        // ✅ Only year and gpa can be changed
        $year = isset($input['year']) ? (int)$input['year'] : $student['year'];
        $gpa = isset($input['gpa']) ? (float)$input['gpa'] : $student['gpa'];

        try {
            $stmt = $pdo->prepare("
                UPDATE Student 
                SET year = ?, gpa = ?
                WHERE student_id = ?
            ");
            $stmt->execute([$year, $gpa, $student_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Student record updated successfully"
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    
    public static function getAllStudents() {
        global $pdo;

        try {
            $stmt = $pdo->query("
                SELECT s.student_id, u.username, u.email, u.faculty_id, u.major_id, s.year, s.gpa, s.points
                FROM Student s
                JOIN Users u ON s.student_id = u.user_id
                ORDER BY s.student_id ASC
            ");
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "count" => count($students),
                "data" => $students
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
