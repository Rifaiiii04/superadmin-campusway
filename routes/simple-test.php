<?php
// Simple direct test - bypass Laravel complexity
if (isset($_GET['test'])) {
    echo "SIMPLE PHP TEST - SERVER IS WORKING";
    exit;
}

// Test basic Laravel
require_once __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

try {
    // Test View class
    if (class_exists('Illuminate\View\View')) {
        echo "✅ View class exists\n";
    } else {
        echo "❌ View class missing\n";
    }
    
    // Test Inertia class  
    if (class_exists('Inertia\Inertia')) {
        echo "✅ Inertia class exists\n";
    } else {
        echo "❌ Inertia class missing\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
