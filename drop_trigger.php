<?php
require_once 'backend/app/Utils/Database.php';

try {
    $db = \App\Utils\Database::getInstance()->getConnection();
    $db->exec('DROP TRIGGER IF EXISTS notify_user_on_mention');
    echo "Trigger 'notify_user_on_mention' dropped successfully.";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
