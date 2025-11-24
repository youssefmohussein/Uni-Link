<?php
require_once __DIR__ . '/../utils/DbConnection.php';

class CVController {


    public static function uploadCV() {
        header('Content-Type: application/json');

        // Validate user_id
        if (!isset($_POST['user_id']) || empty($_POST['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
            return;
        }

        $user_id = intval($_POST['user_id']);

        // Validate file
        if (!isset($_FILES['cv_file'])) {
            echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
            return;
        }

        $file = $_FILES['cv_file'];
        $uploadDir = __DIR__ . '/../uploads/';
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Only allow PDF
        if ($fileExtension !== 'pdf') {
            echo json_encode(['status' => 'error', 'message' => 'Only PDF files are allowed']);
            return;
        }

        // Rename file safely
        $newFileName = 'cv_user' . $user_id . '_' . time() . '.pdf';
        $filePath = $uploadDir . $newFileName;
        $dbPath = 'uploads/' . $newFileName; // path to save in DB

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
            return;
        }

        // Save to DB
        try {
            global $pdo;
            // If CV already exists, update it
            $stmt = $pdo->prepare("INSERT INTO CV (user_id, file_path) VALUES (?, ?)
                                   ON DUPLICATE KEY UPDATE file_path = VALUES(file_path), created_at = CURRENT_TIMESTAMP");
            $stmt->execute([$user_id, $dbPath]);

            echo json_encode([
                'status' => 'success',
                'message' => 'CV uploaded successfully',
                'file_path' => $dbPath
            ]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public static function downloadCV($user_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT file_path FROM CV WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $cv = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cv) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'CV not found']);
            return;
        }

        $filePath = __DIR__ . '/../' . $cv['file_path'];
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'File not found on server']);
            return;
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        readfile($filePath);
    }

}
