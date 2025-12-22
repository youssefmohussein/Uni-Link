<?php
// Script to check leaderboard data
require_once __DIR__ . '/../../backend/config/cors.php';
require_once __DIR__ . '/../../backend/config/database.php';
require_once __DIR__ . '/../../backend/app/Repositories/BaseRepository.php';
require_once __DIR__ . '/../../backend/app/Repositories/StudentRepository.php';

use App\Repositories\StudentRepository;
use Config\Database;

try {
    $db = Database::getInstance()->getConnection();
    $repo = new StudentRepository($db);
    
    echo "Checking top students...\n";
    $students = $repo->getTopByPoints(10);
    
    if (empty($students)) {
        echo "No students found in leaderboard query.\n";
        
        // Debug: Check if students table has any rows
        $stmt = $db->query("SELECT COUNT(*) as count FROM students");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "Total rows in students table: $count\n";
        
        if ($count > 0) {
            echo "Students exist but might not have points or user link is broken.\n";
            // Check raw data
            $stmt = $db->query("SELECT * FROM students LIMIT 5");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            print_r($rows);
        } else {
            echo "Students table is empty. Attempting to populate it from users table...\n";
            
            // Auto-populate students for STUDENT role users
            $stmt = $db->query("SELECT user_id, major_id, faculty_id FROM users WHERE role = 'STUDENT'");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $added = 0;
            foreach ($users as $user) {
                // Check if already in students
                $check = $db->prepare("SELECT student_id FROM students WHERE user_id = ?");
                $check->execute([$user['user_id']]);
                if (!$check->fetch()) {
                    // Start with random points for demo
                    $points = rand(10, 500); 
                    $gpa = rand(200, 400) / 100;
                    $year = rand(1, 4);
                    
                    $insert = $db->prepare("INSERT INTO students (user_id, gpa, year, points, created_at) VALUES (?, ?, ?, ?, NOW())");
                    $insert->execute([$user['user_id'], $gpa, $year, $points]);
                    $added++;
                }
            }
            echo "Added $added students to the students table.\n";
        }
    } else {
        echo "Found " . count($students) . " students:\n";
        foreach ($students as $s) {
            echo "- {$s['username']} ({$s['points']} XP)\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
