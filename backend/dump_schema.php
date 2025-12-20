<?php
require_once __DIR__ . '/config/autoload.php';
require_once __DIR__ . '/app/Utils/EnvLoader.php';
require_once __DIR__ . '/app/Utils/Database.php';

use App\Utils\Database;

try {
    $db = Database::getInstance()->getConnection();

    $tables = ['chat_rooms', 'room_members'];

    foreach ($tables as $table) {
        echo "=== SCHEMA FOR $table ===\n";
        $stmt = $db->query("DESCRIBE $table");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            echo str_pad($column['Field'], 20) . " | " .
                str_pad($column['Type'], 20) . " | " .
                str_pad("Null: " . $column['Null'], 10) . " | " .
                "Default: " . ($column['Default'] === null ? 'NULL' : $column['Default']) . "\n";
        }
        echo "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
