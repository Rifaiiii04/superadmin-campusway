<?php
require_once __DIR__.'/../vendor/autoload.php';

try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    echo "Step 1: App created successfully<br>";
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "Step 2: Kernel created successfully<br>";
    
    $request = Illuminate\Http\Request::capture();
    echo "Step 3: Request captured successfully<br>";
    
    $response = $kernel->handle($request);
    echo "Step 4: Request handled successfully<br>";
    
    echo "Laravel FULLY BOOTSTRAPPED!<br>";
    
} catch (Exception $e) {
    echo "BOOTSTRAP FAILED at step<br>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
}
