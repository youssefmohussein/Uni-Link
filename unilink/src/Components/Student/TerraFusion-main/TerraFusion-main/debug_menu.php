<?php
// debug_menu.php
require_once 'config/database.php';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // 1. Check database connection
    echo "<h2>✅ Database Connection Successful</h2>";
    
    // 2. Show table structure
    echo "<h3>Table Structure (meals):</h3>";
    $stmt = $pdo->query("DESCRIBE meals");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
    
    // 3. Show all available menu items
    echo "<h3>All Available Menu Items:</h3>";
    $sql = "SELECT * FROM meals WHERE availability = 'Available' AND quantity > 0";
    $stmt = $pdo->query($sql);
    $items = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    if (count($items) > 0) {
        echo "<pre>";
        print_r($items);
        echo "</pre>";
    } else {
        echo "No available items found in the database.<br>";
    }
    
    // 4. Check if any items exist at all
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM meals");
    $count = $stmt->fetch(PDO::FETCH_OBJ)->count;
    echo "Total items in meals table: $count<br>";
    
    // 5. Check for common issues
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM meals WHERE availability != 'Available'");
    $notAvailable = $stmt->fetch(PDO::FETCH_OBJ)->count;
    echo "Items not marked as 'Available': $notAvailable<br>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM meals WHERE quantity <= 0");
    $noQuantity = $stmt->fetch(PDO::FETCH_OBJ)->count;
    echo "Items with quantity <= 0: $noQuantity<br>";
    
} catch (PDOException $e) {
    die("❌ Database Connection Failed: " . $e->getMessage());
}
