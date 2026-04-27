<?php
require_once __DIR__ . '/config/autoload.php';
use App\Utils\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    // Seed Faculties
    $faculties = [
        ['name' => 'Faculty of Engineering', 'description' => 'Engineering and Technology'],
        ['name' => 'Faculty of Science', 'description' => 'Natural Sciences'],
        ['name' => 'Faculty of Commerce', 'description' => 'Business and Finance']
    ];

    foreach ($faculties as $f) {
        $stmt = $db->prepare("INSERT IGNORE INTO faculties (name, description) VALUES (?, ?)");
        $stmt->execute([$f['name'], $f['description']]);
    }

    // Seed Majors
    $engineeringId = $db->query("SELECT faculty_id FROM faculties WHERE name = 'Faculty of Engineering'")->fetchColumn();
    $scienceId = $db->query("SELECT faculty_id FROM faculties WHERE name = 'Faculty of Science'")->fetchColumn();

    if ($engineeringId) {
        $majors = [
            ['faculty_id' => $engineeringId, 'name' => 'Computer Engineering'],
            ['faculty_id' => $engineeringId, 'name' => 'Electrical Engineering']
        ];
        foreach ($majors as $m) {
            $stmt = $db->prepare("INSERT IGNORE INTO majors (faculty_id, name) VALUES (?, ?)");
            $stmt->execute([$m['faculty_id'], $m['name']]);
        }
    }

    if ($scienceId) {
        $majors = [
            ['faculty_id' => $scienceId, 'name' => 'Computer Science'],
            ['faculty_id' => $scienceId, 'name' => 'Physics']
        ];
        foreach ($majors as $m) {
            $stmt = $db->prepare("INSERT IGNORE INTO majors (faculty_id, name) VALUES (?, ?)");
            $stmt->execute([$m['faculty_id'], $m['name']]);
        }
    }

    echo "Faculties and Majors seeded successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
