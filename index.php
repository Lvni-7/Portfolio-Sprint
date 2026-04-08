<?php

// config les errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// autoload manual des classes (PSR-4 style)
spl_autoload_register(function ($class) {
    if (strncmp('App\\', $class, 4) !== 0) return;

    $relative_class = substr($class, 4);
    $parts = explode('\\', $relative_class);
    
    if (count($parts) > 1) {
        $parts[0] = strtolower($parts[0]);
    }

    $file = __DIR__ . '/' . implode('/', $parts) . '.php';
    if (file_exists($file)) require_once $file;
});

use App\Services\Routing;

// init le router et handle la request
$router = new Routing();
$router->handleRequest();
