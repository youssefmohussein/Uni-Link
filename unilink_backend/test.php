<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once 'utils/DbConnection.php';

echo json_encode(["status" => "success", "message" => "Database connection working fine"]);
