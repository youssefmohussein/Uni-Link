<?php
require_once 'Admin/app/Core/Database.php';
require_once 'Admin/app/Helpers/Session.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT user_id, email, full_name, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($users);
    echo "</pre>";
} catch (Exception $e) {
    echo $e->getMessage();
}
