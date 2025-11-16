<?php
require_once __DIR__ . '/../utils/DbConnection.php';

class ProjectSkillController {

    // Add skills to a project
    public static function addProjectSkills() {
        global $pdo;
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['project_id'], $input['skills']) || !is_array($input['skills'])) {
            echo json_encode(["status"=>"error","message"=>"Missing project_id or skills array"]);
            return;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO ProjectSkill (project_id, skill_id) VALUES (?, ?)");
            foreach ($input['skills'] as $skill) {
                $stmt->execute([$input['project_id'], $skill['skill_id']]);
            }
            echo json_encode(["status"=>"success","message"=>"Skills added to project"]);
        } catch (PDOException $e) {
            echo json_encode(["status"=>"error","message"=>$e->getMessage()]);
        }
    }

    // Get project skills
    public static function getProjectSkills() {
        global $pdo;
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['project_id'])) {
            echo json_encode(["status"=>"error","message"=>"Missing project_id"]);
            return;
        }

        $stmt = $pdo->prepare("
            SELECT ps.skill_id, s.skill_name, s.category_id 
            FROM ProjectSkill ps 
            JOIN Skill s ON ps.skill_id = s.skill_id 
            WHERE ps.project_id=?
        ");
        $stmt->execute([$input['project_id']]);
        $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status"=>"success","data"=>$skills]);
    }

    // Update project skills
    public static function updateProjectSkills() {
        global $pdo;
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['project_id'], $input['skills']) || !is_array($input['skills'])) {
            echo json_encode(["status"=>"error","message"=>"Missing project_id or skills array"]);
            return;
        }

        try {
            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM ProjectSkill WHERE project_id=?")->execute([$input['project_id']]);
            $stmt = $pdo->prepare("INSERT INTO ProjectSkill (project_id, skill_id) VALUES (?, ?)");
            foreach ($input['skills'] as $skill) {
                $stmt->execute([$input['project_id'], $skill['skill_id']]);
            }
            $pdo->commit();
            echo json_encode(["status"=>"success","message"=>"Project skills updated"]);
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            echo json_encode(["status"=>"error","message"=>$e->getMessage()]);
        }
    }

    // Delete project skills
    public static function deleteProjectSkills() {
        global $pdo;
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['project_id'])) {
            echo json_encode(["status"=>"error","message"=>"Missing project_id"]);
            return;
        }
        $pdo->prepare("DELETE FROM ProjectSkill WHERE project_id=?")->execute([$input['project_id']]);
        echo json_encode(["status"=>"success","message"=>"Project skills deleted"]);
    }
}
?>
