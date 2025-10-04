<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Manual route testing
$routes = [
    '/super-admin/health',
    '/super-admin/test', 
    '/super-admin/login'
];

foreach ($routes as $route) {
    try {
        // Create request manually
        $request = Illuminate\Http\Request::create($route, 'GET');
        $response = $kernel->handle($request);
        
        echo "Route: $route - Status: " . $response->getStatusCode() . "<br>";
        echo "Content: " . $response->getContent() . "<br><br>";
    } catch (Exception $e) {
        echo "Route: $route - ERROR: " . $e->getMessage() . "<br><br>";
    }
}
