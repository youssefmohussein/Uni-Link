<?php
// Clear OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo json_encode(['status' => 'success', 'message' => 'OPcache cleared successfully']);
} else {
    echo json_encode(['status' => 'info', 'message' => 'OPcache is not enabled']);
}
