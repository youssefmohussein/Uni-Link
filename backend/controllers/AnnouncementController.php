<?php
require_once __DIR__ . '/../utils/DbConnection.php';

class AnnouncementController
{
    public static function addAnnouncement()
    {
        global $pdo;
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['author_id'], $data['faculty_id'], $data['title'], $data['content'])) {
            echo json_encode(["status" => "error", "message" => "Missing fields"]);
            return;
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO Announcement (author_id, faculty_id, title, content, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $data['author_id'],
                $data['faculty_id'],
                $data['title'],
                $data['content']
            ]);

            echo json_encode(["status" => "success", "message" => "Announcement added"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public static function getAnnouncements($facultyId = null)
    {
        global $pdo;

        try {
            if ($facultyId) {
                $stmt = $pdo->prepare("SELECT * FROM Announcement WHERE faculty_id = ? ORDER BY created_at DESC");
                $stmt->execute([$facultyId]);
            } else {
                $stmt = $pdo->query("SELECT * FROM Announcement ORDER BY created_at DESC");
            }

            $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["status" => "success", "announcements" => $announcements]);

        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public static function getAnnouncement($id)
    {
        global $pdo;

        try {
            $stmt = $pdo->prepare("SELECT * FROM Announcement WHERE announcement_id = ?");
            $stmt->execute([$id]);
            $announcement = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($announcement) {
                echo json_encode(["status" => "success", "announcement" => $announcement]);
            } else {
                echo json_encode(["status" => "error", "message" => "Not found"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public static function updateAnnouncement()
    {
        global $pdo;
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['announcement_id'], $data['title'], $data['content'])) {
            echo json_encode(["status" => "error", "message" => "Missing fields"]);
            return;
        }

        try {
            $stmt = $pdo->prepare("
                UPDATE Announcement 
                SET title = ?, content = ?
                WHERE announcement_id = ?
            ");
            $stmt->execute([
                $data['title'],
                $data['content'],
                $data['announcement_id']
            ]);

            echo json_encode(["status" => "success", "message" => "Announcement updated"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public static function deleteAnnouncement()
    {
        global $pdo;
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['announcement_id'])) {
            echo json_encode(["status" => "error", "message" => "Missing announcement_id"]);
            return;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM Announcement WHERE announcement_id = ?");
            $stmt->execute([$data['announcement_id']]);

            echo json_encode(["status" => "success", "message" => "Announcement deleted"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}
