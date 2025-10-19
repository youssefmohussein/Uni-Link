<?php

$host = "localhost";      
$user = "root";           
$password = "";           
$dbname = "unilink_Db";   


$conn = new mysqli($host, $user, $password, $dbname);


if ($conn->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $conn->connect_error
    ]));
}

$conn->set_charset("utf8mb4");


?>
