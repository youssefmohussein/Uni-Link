<?php
require_once 'config.php';
header('Content-Type: text/plain');
$stmt = $pdo->query("DESCRIBE users");
while($row = $stmt->fetch()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
?>
