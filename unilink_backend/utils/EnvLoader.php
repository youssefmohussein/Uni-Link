<?php
function loadEnv($path) {
    if (!file_exists($path)) {
        die(json_encode([
            "status" => "error",
            "message" => ".env file not found at $path"
        ]));
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    $env = [];

    foreach ($lines as $line) {
        $line = trim($line); 
        if (str_starts_with($line, '#')) continue;
        list($name, $value) = explode('=', $line, 2);
        $env[trim($name)] = trim($value);
    }
    return $env;
}
?>
