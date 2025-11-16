<?php
require_once __DIR__ . '/../utils/DbConnection.php';

class SkillController {

    // Add Skill
    public static function addSkill() {
        global $pdo;
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['skill_name'], $input['category_id'])) {
            echo json_encode(["status"=>"error","message"=>"Missing skill_name or category_id"]);
            return;
        }
        $stmt = $pdo->prepare("INSERT INTO Skill (skill_name, category_id) VALUES (?, ?)");
        $stmt->execute([$input['skill_name'], $input['category_id']]);
        echo json_encode(["status"=>"success","skill_id"=>$pdo->lastInsertId()]);
    }

    // Get Skills
    public static function getSkills() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM Skill");
        $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status"=>"success","data"=>$skills]);
    }

    // Update Skill
    public static function updateSkill() {
        global $pdo;
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['skill_id'], $input['skill_name'], $input['category_id'])) {
            echo json_encode(["status"=>"error","message"=>"Missing parameters"]);
            return;
        }
        $stmt = $pdo->prepare("UPDATE Skill SET skill_name=?, category_id=? WHERE skill_id=?");
        $stmt->execute([$input['skill_name'], $input['category_id'], $input['skill_id']]);
        echo json_encode(["status"=>"success","message"=>"Skill updated"]);
    }

    // Delete Skill
    public static function deleteSkill() {
        global $pdo;
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['skill_id'])) {
            echo json_encode(["status"=>"error","message"=>"Missing skill_id"]);
            return;
        }
        $stmt = $pdo->prepare("DELETE FROM Skill WHERE skill_id=?");
        $stmt->execute([$input['skill_id']]);
        echo json_encode(["status"=>"success","message"=>"Skill deleted"]);
    }
}
?>
