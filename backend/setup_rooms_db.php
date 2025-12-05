<?php
require_once __DIR__ . '/utils/DbConnection.php';

try {
    // ProjectRooms Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS ProjectRooms (
            room_id INT AUTO_INCREMENT PRIMARY KEY,
            room_name VARCHAR(255) NOT NULL,
            description TEXT,
            created_by INT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES Users(user_id) ON DELETE CASCADE
        )
    ");
    echo "Table 'ProjectRooms' created or already exists.<br>";

    // RoomMessages Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS RoomMessages (
            message_id INT AUTO_INCREMENT PRIMARY KEY,
            room_id INT NOT NULL,
            user_id INT NOT NULL,
            content TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (room_id) REFERENCES ProjectRooms(room_id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
        )
    ");
    echo "Table 'RoomMessages' created or already exists.<br>";

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>