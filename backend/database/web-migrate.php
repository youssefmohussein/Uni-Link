<?php
/**
 * Web-Accessible Database Migration
 * 
 * SECURITY WARNING: Remove this file after running the migration!
 * This file should NEVER be accessible in production.
 */

// Only allow access from localhost
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die('Access denied. This script can only be run from localhost.');
}

// Require database configuration
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

try {
    $pdo = Database::getInstance()->getConnection();
    
    // Read schema file
    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found: $schemaFile");
    }
    
    $sql = file_get_contents($schemaFile);
    
    // Split SQL into statements
    $statements = [];
    $inTrigger = false;
    $currentStatement = '';
    $lines = explode("\n", $sql);
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Skip comments and empty lines
        if (empty($line) || strpos($line, '--') === 0) {
            continue;
        }
        
        if (stripos($line, 'DELIMITER $$') === 0) {
            $inTrigger = true;
            continue;
        }
        
        if (stripos($line, 'DELIMITER ;') === 0) {
            if (!empty($currentStatement)) {
                $statements[] = $currentStatement;
                $currentStatement = '';
            }
            $inTrigger = false;
            continue;
        }
        
        $currentStatement .= $line . "\n";
        
        if ($inTrigger) {
            if (substr(rtrim($line), -2) === '$$') {
                $currentStatement = rtrim($currentStatement);
                $currentStatement = substr($currentStatement, 0, -2);
                $statements[] = $currentStatement;
                $currentStatement = '';
            }
        } else {
            if (substr(rtrim($line), -1) === ';') {
                $statements[] = $currentStatement;
                $currentStatement = '';
            }
        }
    }
    
    // Execute statements
    $results = [
        'success' => [],
        'errors' => [],
        'tables_created' => 0,
        'triggers_created' => 0,
        'data_seeded' => 0
    ];
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement)) continue;
        
        try {
            $pdo->exec($statement);
            
            if (stripos($statement, 'CREATE TABLE') === 0) {
                preg_match('/CREATE TABLE `?(\w+)`?/i', $statement, $matches);
                $tableName = $matches[1] ?? 'unknown';
                $results['success'][] = "Created table: $tableName";
                $results['tables_created']++;
            } elseif (stripos($statement, 'CREATE TRIGGER') === 0) {
                preg_match('/CREATE TRIGGER `?(\w+)`?/i', $statement, $matches);
                $triggerName = $matches[1] ?? 'unknown';
                $results['success'][] = "Created trigger: $triggerName";
                $results['triggers_created']++;
            } elseif (stripos($statement, 'INSERT INTO') === 0) {
                preg_match('/INSERT INTO `?(\w+)`?/i', $statement, $matches);
                $tableName = $matches[1] ?? 'unknown';
                $results['success'][] = "Seeded data: $tableName";
                $results['data_seeded']++;
            }
        } catch (PDOException $e) {
            $results['errors'][] = [
                'statement' => substr($statement, 0, 100) . '...',
                'error' => $e->getMessage()
            ];
        }
    }
    
    // Get table count
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Migration completed',
        'results' => $results,
        'total_tables' => count($tables),
        'tables' => $tables
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
