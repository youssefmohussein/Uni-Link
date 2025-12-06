<?php
require_once __DIR__ . '/../utils/DbConnection.php';

class ProjectController {

    public static function addProject() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input || !isset($input['title'], $input['description'], $input['owner_id'])) {
            echo json_encode(["status" => "error", "message" => "Missing required fields"]);
            return;
        }

        $title        = trim($input['title']);
        $description  = trim($input['description']);
        $file_path    = $input['file_path'] ?? null;
        $owner_id     = (int)$input['owner_id'];
        $supervisor_id = $input['supervisor_id'] ?? null;

        try {
            $stmt = $pdo->prepare("
                INSERT INTO Project (title, description, file_path, owner_id, supervisor_id, status, created_at)
                VALUES (?, ?, ?, ?, ?, 'Pending', NOW())
            ");
            $stmt->execute([$title, $description, $file_path, $owner_id, $supervisor_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Project created successfully"
            ]);

        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
    public static function getAllProjects() {
        global $pdo;

        try {
            $stmt = $pdo->query("SELECT * FROM Project ORDER BY created_at DESC");
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "count" => count($projects),
                "data" => $projects
            ]);

        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
    public static function getProjectById() {
        global $pdo;

        // Get project_id from query string or request body
        $project_id = null;
        if (isset($_GET['project_id'])) {
            $project_id = (int)$_GET['project_id'];
        } else {
            $input = json_decode(file_get_contents("php://input"), true);
            $project_id = isset($input['project_id']) ? (int)$input['project_id'] : null;
        }

        if (!$project_id) {
            echo json_encode(["status" => "error", "message" => "Project ID is required"]);
            return;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM Project WHERE project_id = ?");
            $stmt->execute([$project_id]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$project) {
                echo json_encode(["status" => "error", "message" => "Project not found"]);
                return;
            }

            echo json_encode(["status" => "success", "data" => $project]);

        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
    public static function updateProject() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input || !isset($input['project_id'], $input['owner_id'])) {
            echo json_encode(["status" => "error", "message" => "Missing required fields"]);
            return;
        }

        $project_id = (int)$input['project_id'];
        $owner_id   = (int)$input['owner_id'];

        // check ownership
        $check = $pdo->prepare("SELECT owner_id FROM Project WHERE project_id = ?");
        $check->execute([$project_id]);
        $project = $check->fetch(PDO::FETCH_ASSOC);

        if (!$project) {
            echo json_encode(["status" => "error", "message" => "Project not found"]);
            return;
        }

        if ($project['owner_id'] != $owner_id) {
            echo json_encode(["status" => "error", "message" => "You cannot edit this project"]);
            return;
        }

        $title       = $input['title'] ?? null;
        $description = $input['description'] ?? null;
        $file_path   = $input['file_path'] ?? null;

        try {
            $stmt = $pdo->prepare("
                UPDATE Project 
                SET title = ?, description = ?, file_path = ?, updated_at = NOW()
                WHERE project_id = ?
            ");
            $stmt->execute([$title, $description, $file_path, $project_id]);

            echo json_encode(["status" => "success", "message" => "Project updated successfully"]);

        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
    public static function deleteProject() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['project_id'], $input['owner_id'])) {
            echo json_encode(["status" => "error", "message" => "Missing required fields"]);
            return;
        }

        $project_id = (int)$input['project_id'];
        $owner_id   = (int)$input['owner_id'];

        // verify owner
        $check = $pdo->prepare("SELECT owner_id FROM Project WHERE project_id = ?");
        $check->execute([$project_id]);

        $project = $check->fetch(PDO::FETCH_ASSOC);
        if (!$project || $project['owner_id'] != $owner_id) {
            echo json_encode(["status" => "error", "message" => "Unauthorized deletion"]);
            return;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM Project WHERE project_id = ?");
            $stmt->execute([$project_id]);

            echo json_encode(["status" => "success", "message" => "Project deleted"]);

        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
    public static function addComment() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['project_id'], $input['user_id'], $input['comment'])) {
            echo json_encode(["status" => "error", "message" => "Missing fields"]);
            return;
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO ProjectComment (project_id, user_id, comment, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([
                (int)$input['project_id'],
                (int)$input['user_id'],
                trim($input['comment'])
            ]);

            echo json_encode(["status" => "success", "message" => "Comment added"]);

        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
    public static function getComments($project_id) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("
                SELECT c.*, u.username
                FROM ProjectComment c
                JOIN Users u ON u.user_id = c.user_id
                WHERE c.project_id = ?
                ORDER BY c.created_at ASC
            ");
            $stmt->execute([(int)$project_id]);
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(["status" => "success", "data" => $comments]);

        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
    public static function addGrade() {
        // Alias for updateStatusAndGrade
        self::updateStatusAndGrade();
    }

    public static function updateStatusAndGrade() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['project_id'], $input['professor_id'], $input['status'], $input['grade'])) {
            echo json_encode(["status" => "error", "message" => "Missing fields"]);
            return;
        }

        $project_id   = (int)$input['project_id'];
        $professor_id = (int)$input['professor_id'];
        $status       = $input['status'];
        $grade        = (float)$input['grade'];

        // check professor role
        $roleCheck = $pdo->prepare("SELECT role FROM Users WHERE user_id = ?");
        $roleCheck->execute([$professor_id]);
        $role = $roleCheck->fetchColumn();

        if ($role !== "Professor") {
            echo json_encode(["status" => "error", "message" => "Only professors can grade"]);
            return;
        }

        // validate status
        $validStatus = ['Pending', 'Approved', 'Rejected'];
        if (!in_array($status, $validStatus)) {
            echo json_encode(["status" => "error", "message" => "Invalid status"]);
            return;
        }

        try {
            $stmt = $pdo->prepare("
                UPDATE Project
                SET status = ?, grade = ?, updated_at = NOW(), supervisor_id = ?
                WHERE project_id = ?
            ");
            $stmt->execute([$status, $grade, $professor_id, $project_id]);

            echo json_encode(["status" => "success", "message" => "Evaluation updated"]);

        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // Get all projects for a specific user
    public static function getUserProjects() {
        global $pdo;

        // Get user_id from query string or request body
        $user_id = null;
        if (isset($_GET['user_id'])) {
            $user_id = (int)$_GET['user_id'];
        } else {
            $input = json_decode(file_get_contents("php://input"), true);
            $user_id = isset($input['user_id']) ? (int)$input['user_id'] : null;
        }

        if (!$user_id) {
            echo json_encode([
                "status" => "error",
                "message" => "User ID is required"
            ]);
            return;
        }

        try {
            $stmt = $pdo->prepare("
                SELECT 
                    p.project_id,
                    p.title,
                    p.description,
                    p.file_path,
                    p.status,
                    p.grade,
                    p.created_at,
                    p.updated_at,
                    u.username as supervisor_name
                FROM Project p
                LEFT JOIN Users u ON p.supervisor_id = u.user_id
                WHERE p.owner_id = ?
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([$user_id]);
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "count" => count($projects),
                "data" => $projects
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }
}

