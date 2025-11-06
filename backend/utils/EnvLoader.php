<?php
function loadEnv($path) {
    if (!file_exists($path)) {
        exit(json_encode([
            "status" => "error",
            "message" => ".env file not found at $path"
        ]));
    }

    $env = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (!str_contains($line, '=')) continue;

        [$key, $value] = explode('=', $line, 2);
        $env[trim($key)] = trim($value);
    }

    return $env;
}
?>
