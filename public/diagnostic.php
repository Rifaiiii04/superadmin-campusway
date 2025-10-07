<?php
// Comprehensive diagnostic script
echo "<h1>Super Admin Diagnostic</h1>";

// Check PHP version
echo "<h2>PHP Version: " . phpversion() . "</h2>";

// Check Laravel environment
echo "<h2>Laravel Environment</h2>";
echo "App Name: " . config('app.name') . "<br>";
echo "Environment: " . app()->environment() . "<br>";
echo "Debug: " . (config('app.debug') ? 'true' : 'false') . "<br>";

// Check routes
echo "<h2>Routes</h2>";
$routes = [
    '/super-admin/login',
    '/test-route',
    '/build/assets/app-GSvuFT57.js',
    '/build/assets/app-MFl42rpo.css'
];

foreach ($routes as $route) {
    $url = "http://103.23.198.101{$route}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "{$route}: HTTP {$httpCode}<br>";
}

// Check file existence
echo "<h2>File Existence</h2>";
$files = [
    __DIR__ . '/../resources/views/app.blade.php',
    __DIR__ . '/build/assets/app-GSvuFT57.js',
    __DIR__ . '/build/assets/app-MFl42rpo.css',
    __DIR__ . '/.htaccess'
];

foreach ($files as $file) {
    echo $file . ": " . (file_exists($file) ? "EXISTS" : "MISSING") . "<br>";
}

// Check Apache modules
echo "<h2>Apache Modules (if accessible)</h2>";
$modules = ['mod_rewrite', 'mod_headers', 'mod_mime'];
foreach ($modules as $module) {
    echo "$module: " . (function_exists('apache_get_modules') && in_array($module, apache_get_modules()) ? "ENABLED" : "UNKNOWN") . "<br>";
}

// Check permissions
echo "<h2>Permissions</h2>";
echo "Public directory: " . substr(sprintf('%o', fileperms(__DIR__)), -4) . "<br>";
echo "Storage directory: " . substr(sprintf('%o', fileperms(__DIR__ . '/../storage')), -4) . "<br>";
echo "Bootstrap directory: " . substr(sprintf('%o', fileperms(__DIR__ . '/../bootstrap')), -4) . "<br>";
?>
