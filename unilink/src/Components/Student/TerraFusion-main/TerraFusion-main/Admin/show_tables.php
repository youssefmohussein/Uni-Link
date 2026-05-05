<?php
require 'app/Models/Database.php';
$db = \App\Models\Database::getInstance()->getConnection();
$stmt = $db->query('SHOW TABLES');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo array_values($row)[0] . PHP_EOL;
}
?>