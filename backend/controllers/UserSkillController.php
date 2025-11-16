<?php
require_once __DIR__ . '/../utils/DbConnection.php';

class UserSkillController {

    // Add skills to user
    public static function addUserSkills() {
        global $pdo;
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['user_id'], $input['skills']) || !is_array($input['skills'])) {
            echo json_encode(["status"=>"error","message"=>"Missing user_id or skills array"]);
            return;
        }

        $stmt = $pdo->prepare("INSERT INTO UserSkill (user_id, skill_id) VALUES (?, ?)");
        foreach ($input['skills'] as $skill) {
            $stmt->execute([$input['user_id'], $skill['skill_id']]);
        }
        echo json_encode(["status"=>"success","message"=>"Skills added to user"]);
    }

    // Get user skills
    public static function getUserSkills() {
        global $pdo;
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['user_id'])) {
            echo json_encode(["status"=>"error","message"=>"Missing user_id"]);
            return;
        }
        $stmt = $pdo->prepare("
            SELECT us.skill_id, s.skill_name, s.category_id
            FROM UserSkill us
            JOIN Skill s ON us.skill_id = s.skill_id
            WHERE us.user_id=?
        ");
        $stmt->execute([$input['user_id']]);
        $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status"=>"success","data"=>$skills]);
    }

    // Update user skills
    public static function updateUserSkills() {
        global $pdo;
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['user_id'], $input['skills']) || !is_array($input['skills'])) {
            echo json_encode(["status"=>"error","message"=>"Missing user_id or skills array"]);
            return;
        }
        try {
            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM UserSkill WHERE user_id=?")->execute([$input['user_id']]);
            $stmt = $pdo->prepare("INSERT INTO UserSkill (user_id, skill_id) VALUES (?, ?)");
            foreach ($input['skills'] as $skill) {
                $stmt->execute([$input['user_id'], $skill['skill_id']]);
            }
            $pdo->commit();
            echo json_encode(["status"=>"success","message"=>"User skills updated"]);
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            echo json_encode(["status"=>"error","message"=>$e->getMessage()]);
        }
    }

    // Delete user skills
    public static function deleteUserSkills() {
        global $pdo;
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['user_id'])) {
            echo json_encode(["status"=>"error","message"=>"Missing user_id"]);
            return;
        }
        $pdo->prepare("DELETE FROM UserSkill WHERE user_id=?")->execute([$input['user_id']]);
        echo json_encode(["status"=>"success","message"=>"User skills deleted"]);
    }
}
?>
