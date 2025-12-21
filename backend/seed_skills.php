<?php
require_once __DIR__ . '/config/db.php';

use App\Utils\Database;

try {
    $db = Database::getInstance()->getConnection();
    echo "<h1>Skills Seeder</h1>";

    // 1. Seed Categories
    $categories = [
        'Programming' => ['Python', 'Java', 'C++', 'JavaScript', 'PHP', 'Go', 'Rust', 'TypeScript'],
        'Web Development' => ['HTML', 'CSS', 'React', 'Vue', 'Angular', 'Node.js', 'Laravel', 'Django'],
        'Data Science' => ['SQL', 'Pandas', 'NumPy', 'Machine Learning', 'TensorFlow', 'PyTorch'],
        'Design' => ['Photoshop', 'Illustrator', 'Figma', 'UI/UX', 'Graphic Design'],
        'DevOps' => ['Docker', 'Kubernetes', 'AWS', 'Azure', 'CI/CD', 'Linux'],
        'Mobile' => ['Android', 'iOS', 'Flutter', 'React Native', 'Swift', 'Kotlin']
    ];

    $addedCats = 0;
    $addedSkills = 0;

    foreach ($categories as $catName => $skills) {
        // Check/Insert Category
        $stmt = $db->prepare("SELECT category_id FROM skill_categories WHERE name = ?");
        $stmt->execute([$catName]);
        $catId = $stmt->fetchColumn();

        if (!$catId) {
            $stmt = $db->prepare("INSERT INTO skill_categories (name) VALUES (?)");
            $stmt->execute([$catName]);
            $catId = $db->lastInsertId();
            $addedCats++;
            echo "Created Category: <strong>$catName</strong><br>";
        } else {
            echo "Category exists: <strong>$catName</strong><br>";
        }

        // Check/Insert Skills
        foreach ($skills as $skillName) {
            $stmt = $db->prepare("SELECT skill_id FROM skills WHERE name = ? AND category_id = ?");
            $stmt->execute([$skillName, $catId]);
            
            if (!$stmt->fetch()) {
                $stmt = $db->prepare("INSERT INTO skills (name, category_id) VALUES (?, ?)");
                $stmt->execute([$skillName, $catId]);
                $addedSkills++;
                echo "&nbsp;&nbsp;- Added Skill: $skillName<br>";
            }
        }
    }

    echo "<hr>";
    echo "<h3>Seeding Complete!</h3>";
    echo "Added $addedCats categories and $addedSkills skills.<br>";
    echo "You can now return to the application and refresh the page.";

} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo $e->getMessage();
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
