<?php
require_once __DIR__ . '/config/autoload.php';

use App\Repositories\UserRepository;
use App\Utils\Database;

try {
    $db = Database::getInstance()->getConnection();
    $userRepo = new UserRepository($db);

    // Get a faculty and major for demo
    $faculty = $db->query("SELECT faculty_id FROM faculties LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $major = $db->query("SELECT major_id FROM majors LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    
    $facultyId = $faculty['faculty_id'] ?? null;
    $majorId = $major['major_id'] ?? null;

    // 1. Create Student
    $studentUsername = 'student';
    if (!$userRepo->findByUsername($studentUsername)) {
        $studentData = [
            'username' => $studentUsername,
            'email' => 'student@unilink.com',
            'password' => password_hash('student123', PASSWORD_DEFAULT),
            'role' => 'STUDENT',
            'faculty_id' => $facultyId,
            'major_id' => $majorId,
            'bio' => 'Computer Science Student',
            'profile_image' => 'uploads/defaults/student.png'
        ];
        $studentUserId = $userRepo->create($studentData);
        
        // Add to students table
        $stmt = $db->prepare("INSERT INTO students (user_id, year, gpa, points, enrollment_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$studentUserId, 3, 3.8, 50, date('Y-m-d')]);
        echo "Student user created: student / student123\n";
    }

    // 2. Create Professor
    $profUsername = 'professor';
    if (!$userRepo->findByUsername($profUsername)) {
        $profData = [
            'username' => $profUsername,
            'email' => 'prof@unilink.com',
            'password' => password_hash('prof123', PASSWORD_DEFAULT),
            'role' => 'PROFESSOR',
            'faculty_id' => $facultyId,
            'major_id' => $majorId,
            'bio' => 'Professor of Computer Science',
            'profile_image' => 'uploads/defaults/professor.png'
        ];
        $profUserId = $userRepo->create($profData);
        
        // Add to professors table
        $stmt = $db->prepare("INSERT INTO professors (user_id, academic_rank, department, office_location) VALUES (?, ?, ?, ?)");
        $stmt->execute([$profUserId, 'Associate Professor', 'Computer Science', 'Building A, Room 302']);
        echo "Professor user created: professor / prof123\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
