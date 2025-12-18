<?php
$routes = require_once __DIR__ . '/config/routes.php';
header('Content-Type: text/plain');
echo "KEYS IN routes.php:\n";
foreach ($routes as $key => $val) {
    echo "'$key'\n";
}
