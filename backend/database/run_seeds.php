<?php
require_once __DIR__ . '/../config/autoload.php';
use App\Utils\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    echo "Connected to database.\n";
    
    $sql = file_get_contents(__DIR__ . '/seed_subjects.sql');
    if (!$sql) die("Could not read sql file.");

    // Simple execution
    $pdo->exec($sql);
    
    echo "Seeding completed successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
