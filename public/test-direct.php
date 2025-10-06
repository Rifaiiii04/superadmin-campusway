<?php
echo "PHP is working!<br>";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'unknown') . "<br>";
echo "Script Name: " . ($_SERVER['SCRIPT_NAME'] ?? 'unknown') . "<br>";
echo "PHP Version: " . PHP_VERSION . "<br>";

// Test if we can reach Laravel
try {
    require_once __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    echo "Laravel app loaded: YES<br>";
    
    // Test routes
    $routes = Illuminate\Support\Facades\Route::getRoutes();
    echo "Routes count: " . count($routes->getRoutes()) . "<br>";
} catch (Exception $e) {
    echo "Laravel error: " . $e->getMessage() . "<br>";
}
