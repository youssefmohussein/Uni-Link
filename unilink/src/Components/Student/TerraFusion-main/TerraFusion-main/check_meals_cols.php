<?php
require_once 'config.php';
try {
    $stmt = $pdo->query("DESCRIBE meals");
    $columns = $stmt->fetchAll();
    echo json_encode($columns, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
