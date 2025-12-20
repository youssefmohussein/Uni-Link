<?php
require_once __DIR__ . '/config/autoload.php';
require_once __DIR__ . '/app/Utils/EnvLoader.php';
require_once __DIR__ . '/app/Utils/Database.php';

use App\Utils\Database;

try {
    $db = Database::getInstance()->getConnection();

    echo "Checking chat_rooms table schema:\n";
    $stmt = $db->query("DESCRIBE chat_rooms");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "{$column['Field']} - {$column['Type']} - Null: {$column['Null']} - Default: " . ($column['Default'] ?? 'NULL') . "\n";
    }

    echo "\nChecking room_members table schema:\n";
    try {
        $stmt = $db->query("DESCRIBE room_members");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            echo "{$column['Field']} - {$column['Type']} - Null: {$column['Null']} - Default: " . ($column['Default'] ?? 'NULL') . "\n";
        }
    } catch (Exception $e) {
        echo "room_members table error: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
