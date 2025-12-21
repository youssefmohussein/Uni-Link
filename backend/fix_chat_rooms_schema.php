<?php
require_once __DIR__ . '/config/autoload.php';

use App\Utils\Database;

try {
    $db = Database::getInstance()->getConnection();

    echo "Starting schema update...\n";

    // Check if columns exist
    $result = $db->query("SHOW COLUMNS FROM chat_rooms LIKE 'faculty_id'");
    if ($result->rowCount() === 0) {
        echo "Adding faculty_id column...\n";
        $db->exec("ALTER TABLE chat_rooms ADD COLUMN faculty_id INT NULL AFTER owner_id");
        $db->exec("ALTER TABLE chat_rooms ADD CONSTRAINT fk_room_faculty FOREIGN KEY (faculty_id) REFERENCES faculties(faculty_id) ON DELETE SET NULL");
    } else {
        echo "faculty_id column already exists.\n";
    }

    $result = $db->query("SHOW COLUMNS FROM chat_rooms LIKE 'professor_id'");
    if ($result->rowCount() === 0) {
        echo "Adding professor_id column...\n";
        $db->exec("ALTER TABLE chat_rooms ADD COLUMN professor_id INT NULL AFTER faculty_id");
        $db->exec("ALTER TABLE chat_rooms ADD CONSTRAINT fk_room_professor FOREIGN KEY (professor_id) REFERENCES users(user_id) ON DELETE SET NULL");
    } else {
        echo "professor_id column already exists.\n";
    }

    echo "Schema update completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
