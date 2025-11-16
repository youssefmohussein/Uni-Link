<?php
require_once __DIR__ . '/../utils/DbConnection.php';

class SkillCategoryController {

    // Add Category
    public static function addCategory() {
        global $pdo;
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['category_name'], $input['user_id'])) {
            echo json_encode(["status"=>"error","message"=>"Missing category_name or user_id"]);
            return;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO SkillCategory (category_name, user_id) VALUES (?, ?)");
            $stmt->execute([$input['category_name'], $input['user_id']]);
            echo json_encode(["status"=>"success","message"=>"Category added","category_id"=>$pdo->lastInsertId()]);
        } catch (PDOException $e) {
            echo json_encode(["status"=>"error","message"=>$e->getMessage()]);
        }
    }

    // Get all categories
    public static function getCategories() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM SkillCategory");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status"=>"success","data"=>$categories]);
    }

    // Update category
    public static function updateCategory() {
        global $pdo;
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['category_id'], $input['category_name'])) {
            echo json_encode(["status"=>"error","message"=>"Missing category_id or category_name"]);
            return;
        }
        $stmt = $pdo->prepare("UPDATE SkillCategory SET category_name=? WHERE category_id=?");
        $stmt->execute([$input['category_name'], $input['category_id']]);
        echo json_encode(["status"=>"success","message"=>"Category updated"]);
    }

    // Delete category
    public static function deleteCategory() {
        global $pdo;
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['category_id'])) {
            echo json_encode(["status"=>"error","message"=>"Missing category_id"]);
            return;
        }
        $stmt = $pdo->prepare("DELETE FROM SkillCategory WHERE category_id=?");
        $stmt->execute([$input['category_id']]);
        echo json_encode(["status"=>"success","message"=>"Category deleted"]);
    }
}
?>