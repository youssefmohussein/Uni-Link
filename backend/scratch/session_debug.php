<?php
session_start();
$id = session_id();
$path = session_save_path();
if (!$path) $path = sys_get_temp_dir();
$writable = is_writable($path);
$session_data = $_SESSION;

echo json_encode([
    'session_id' => $id,
    'session_save_path' => $path,
    'is_writable' => $writable,
    'session_data' => $session_data,
    'php_version' => PHP_VERSION,
    'samesite' => ini_get('session.cookie_samesite'),
    'secure' => ini_get('session.cookie_secure'),
    'httponly' => ini_get('session.cookie_httponly')
]);
