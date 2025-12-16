<?php
/**
 * Database Migration Script
 * 
 * Executes the database schema creation
 * Run this script to set up the complete database structure
 */

require_once __DIR__ . '/../config/database.php';

class DatabaseMigration {
    private $pdo;
    
    public function __construct() {
        try {
            $this->pdo = Database::getInstance()->getConnection();
            echo "✓ Database connection established\n";
        } catch (Exception $e) {
            die("✗ Database connection failed: " . $e->getMessage() . "\n");
        }
    }
    
    public function migrate() {
        echo "\n========================================\n";
        echo "  DATABASE MIGRATION STARTED\n";
        echo "========================================\n\n";
        
        $schemaFile = __DIR__ . '/schema.sql';
        
        if (!file_exists($schemaFile)) {
            die("✗ Schema file not found: $schemaFile\n");
        }
        
        echo "→ Reading schema file...\n";
        $sql = file_get_contents($schemaFile);
        
        if ($sql === false) {
            die("✗ Failed to read schema file\n");
        }
        
        echo "✓ Schema file loaded\n\n";
        echo "→ Executing migration...\n";
        
        try {
            // Split SQL into individual statements
            $statements = $this->splitSqlStatements($sql);
            $successCount = 0;
            $errorCount = 0;
            
            foreach ($statements as $index => $statement) {
                $statement = trim($statement);
                
                if (empty($statement) || $statement === 'DELIMITER $$' || $statement === 'DELIMITER ;') {
                    continue;
                }
                
                try {
                    $this->pdo->exec($statement);
                    $successCount++;
                    
                    // Show progress for major operations
                    if (stripos($statement, 'CREATE TABLE') === 0) {
                        preg_match('/CREATE TABLE `?(\w+)`?/i', $statement, $matches);
                        $tableName = $matches[1] ?? 'unknown';
                        echo "  ✓ Created table: $tableName\n";
                    } elseif (stripos($statement, 'CREATE TRIGGER') === 0) {
                        preg_match('/CREATE TRIGGER `?(\w+)`?/i', $statement, $matches);
                        $triggerName = $matches[1] ?? 'unknown';
                        echo "  ✓ Created trigger: $triggerName\n";
                    } elseif (stripos($statement, 'INSERT INTO') === 0) {
                        preg_match('/INSERT INTO `?(\w+)`?/i', $statement, $matches);
                        $tableName = $matches[1] ?? 'unknown';
                        echo "  ✓ Seeded data: $tableName\n";
                    }
                } catch (PDOException $e) {
                    $errorCount++;
                    echo "  ✗ Error in statement " . ($index + 1) . ": " . $e->getMessage() . "\n";
                    
                    // Show problematic statement for debugging
                    if (strlen($statement) < 200) {
                        echo "    Statement: " . substr($statement, 0, 100) . "...\n";
                    }
                }
            }
            
            echo "\n========================================\n";
            echo "  MIGRATION COMPLETED\n";
            echo "========================================\n";
            echo "  Success: $successCount statements\n";
            echo "  Errors:  $errorCount statements\n";
            echo "========================================\n\n";
            
            if ($errorCount === 0) {
                echo "✓ All migrations executed successfully!\n\n";
                $this->showDatabaseStats();
            } else {
                echo "⚠ Migration completed with errors. Please review.\n\n";
            }
            
        } catch (Exception $e) {
            echo "\n✗ Migration failed: " . $e->getMessage() . "\n";
            return false;
        }
        
        return true;
    }
    
    private function splitSqlStatements($sql) {
        // Remove comments
        $sql = preg_replace('/--[^\n]*\n/', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        
        // Handle triggers specially (they contain semicolons)
        $statements = [];
        $inTrigger = false;
        $currentStatement = '';
        
        $lines = explode("\n", $sql);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
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
                    $currentStatement = substr($currentStatement, 0, -2); // Remove $$
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
        
        if (!empty($currentStatement)) {
            $statements[] = $currentStatement;
        }
        
        return $statements;
    }
    
    private function showDatabaseStats() {
        echo "Database Statistics:\n";
        echo "--------------------\n";
        
        $tables = [
            'users', 'admins', 'professors', 'students',
            'posts', 'comments', 'post_media', 'post_interactions',
            'projects', 'project_reviews', 'project_skills',
            'chat_rooms', 'room_members', 'chat_messages', 'chat_mentions',
            'notifications', 'cvs', 'skills', 'skill_categories', 'user_skills',
            'faculties', 'majors', 'announcements'
        ];
        
        foreach ($tables as $table) {
            try {
                $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM `$table`");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo sprintf("  %-25s %5d rows\n", $table . ':', $result['count']);
            } catch (PDOException $e) {
                echo sprintf("  %-25s %s\n", $table . ':', 'ERROR');
            }
        }
        
        echo "\n";
    }
    
    public function rollback() {
        echo "\n========================================\n";
        echo "  DATABASE ROLLBACK STARTED\n";
        echo "========================================\n\n";
        
        $tables = [
            'chat_mentions', 'chat_messages', 'room_members', 'chat_rooms',
            'notifications', 'project_reviews', 'project_skills', 'projects',
            'user_skills', 'skills', 'skill_categories', 'cvs',
            'post_interactions', 'post_media', 'comments', 'posts',
            'announcements', 'students', 'professors', 'admins', 'users',
            'majors', 'faculties'
        ];
        
        foreach ($tables as $table) {
            try {
                $this->pdo->exec("DROP TABLE IF EXISTS `$table`");
                echo "  ✓ Dropped table: $table\n";
            } catch (PDOException $e) {
                echo "  ✗ Error dropping $table: " . $e->getMessage() . "\n";
            }
        }
        
        // Drop triggers
        $triggers = [
            'increment_student_points_on_approval',
            'notify_student_on_project_review',
            'notify_author_on_comment',
            'notify_author_on_interaction',
            'notify_user_on_mention'
        ];
        
        foreach ($triggers as $trigger) {
            try {
                $this->pdo->exec("DROP TRIGGER IF EXISTS `$trigger`");
                echo "  ✓ Dropped trigger: $trigger\n";
            } catch (PDOException $e) {
                echo "  ✗ Error dropping trigger $trigger: " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n✓ Rollback completed\n\n";
    }
}

// Run migration
if (php_sapi_name() === 'cli') {
    $migration = new DatabaseMigration();
    
    // Check for rollback flag
    if (isset($argv[1]) && $argv[1] === '--rollback') {
        $migration->rollback();
    } else {
        $migration->migrate();
    }
} else {
    die("This script must be run from the command line.\n");
}
