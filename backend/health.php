<?php
// Standalone health check - bypasses all authentication
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

require_once __DIR__ . '/utils/DbConnection.php';

try {
    // Try to query the Users table to verify database is accessible
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Users LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result !== false) {
        echo json_encode([
            "status" => "success",
            "message" => "Database Connected",
            "connected" => true,
            "user_count" => $result['count']
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Database Not Connected",
            "connected" => false
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database Not Connected",
        "connected" => false,
        "error" => $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database Not Connected",
        "connected" => false,
        "error" => $e->getMessage()
    ]);
}
