<?php
require_once __DIR__ . '/../config/autoload.php';
use App\Utils\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    echo "Connected to database.\n";
    
    $sql = file_get_contents(__DIR__ . '/add_subjects.sql');
    if (!$sql) die("Could not read sql file.");

    // Split by DELIMITER if possible, but PDO exec might handle it if not for DELIMITER keywords which are client-side.
    // The previous migrate.php handled this manually. 
    // Since my SQL uses DELIMITER, I need to strip it or handle it.
    // actually, PDO doesn't support DELIMITER syntax directly usually.
    // I will use a simpler approach: executing statements one by one and avoiding DELIMITER if possible, 
    // OR reusing the logic from migrate.php.
    
    // Simpler approach for this specific file:
    // 1. Create tables (straightforward)
    // 2. The Procedure part is tricky via PDO directly if not split correctly.
    // I will rewrite the SQL to NOT use Procedure if possible, OR just parse it.
    
    // Let's implement a simple parser for 'DELIMITER $$' ... 'DELIMITER ;'
    // Or... I can just check if columns exist in PHP and then run ALTER. much easier.
    
    // PHASE 1: Create Tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `subjects` (
          `subject_id` INT AUTO_INCREMENT PRIMARY KEY,
          `name` VARCHAR(255) NOT NULL,
          `code` VARCHAR(50),
          `faculty_id` INT,
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (`faculty_id`) REFERENCES `faculties`(`faculty_id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Checked/Created subjects table.\n";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `student_subjects` (
            `student_subject_id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `subject_id` INT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
            FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`subject_id`) ON DELETE CASCADE,
            UNIQUE KEY `unique_student_subject` (`user_id`, `subject_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Checked/Created student_subjects table.\n";

    // PHASE 2: Add Columns safely
    // Check subject_id
    $stmt = $pdo->query("SHOW COLUMNS FROM `chat_rooms` LIKE 'subject_id'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE `chat_rooms` ADD COLUMN `subject_id` INT DEFAULT NULL");
        $pdo->exec("ALTER TABLE `chat_rooms` ADD CONSTRAINT `fk_room_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`subject_id`) ON DELETE SET NULL");
        echo "Added subject_id to chat_rooms.\n";
    } else {
        echo "subject_id already exists in chat_rooms.\n";
    }

    // Check target_subject_id
    $stmt = $pdo->query("SHOW COLUMNS FROM `chat_rooms` LIKE 'target_subject_id'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE `chat_rooms` ADD COLUMN `target_subject_id` INT DEFAULT NULL");
        $pdo->exec("ALTER TABLE `chat_rooms` ADD CONSTRAINT `fk_room_target_subject` FOREIGN KEY (`target_subject_id`) REFERENCES `subjects`(`subject_id`) ON DELETE SET NULL");
        echo "Added target_subject_id to chat_rooms.\n";
    } else {
        echo "target_subject_id already exists in chat_rooms.\n";
    }

    echo "Migration completed successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
