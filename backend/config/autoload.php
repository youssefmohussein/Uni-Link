<?php
/**
 * PSR-4 Autoloader for Uni-Link Backend
 * 
 * This autoloader maps the App namespace to the app/ directory
 * and the ML namespace to the ml/ directory
 * and automatically loads classes when they are referenced.
 */

spl_autoload_register(function ($class) {
    // Define namespace mappings
    $namespaces = [
        'App\\' => __DIR__ . '/../app/',
        'ML\\' => __DIR__ . '/../ml/',
    ];
    
    foreach ($namespaces as $prefix => $baseDir) {
        // Check if the class uses this namespace prefix
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            // Get the relative class name
            $relativeClass = substr($class, $len);
            
            // Replace namespace separators with directory separators
            // and append .php
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            
            // If the file exists, require it
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});
