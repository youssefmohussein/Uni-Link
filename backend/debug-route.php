<?php
$routes = require_once __DIR__ . '/config/routes.php';
$requestUri = '/api/faculty';
$requestMethod = 'GET';

echo "Testing route: $requestMethod $requestUri\n";

$matched = false;
foreach ($routes as $route => $handler) {
    list($method, $path) = explode(' ', $route, 2);

    if ($method === $requestMethod && $path === $requestUri) {
        echo "MATCH FOUND: $route\n";
        $matched = true;
        break;
    }
}

if (!$matched) {
    echo "NO MATCH FOUND\n";
    echo "Available keys (first 10):\n";
    print_r(array_slice(array_keys($routes), 0, 10));

    // Check specifically for the key
    $target = 'GET /api/faculty';
    if (isset($routes[$target])) {
        echo "Target key exists in array!\n";
    } else {
        echo "Target key DOES NOT exist in array!\n";
        // Search for similar keys
        foreach (array_keys($routes) as $key) {
            if (strpos($key, '/api/faculty') !== false) {
                echo "Similar key found: '$key' (Length: " . strlen($key) . ")\n";
                echo "Hex: " . bin2hex($key) . "\n";
            }
        }
    }
}
