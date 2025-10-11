<?php
// =====================================================
// Test API Direct
// =====================================================

echo "==========================================\n";
echo "Test API Direct\n";
echo "==========================================\n\n";

// Bootstrap Laravel
require_once '/var/www/superadmin/superadmin-campusway/vendor/autoload.php';
$app = require_once '/var/www/superadmin/superadmin-campusway/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "✅ Laravel bootstrap: OK\n\n";

// Test API endpoint directly
echo "🔍 Test: API endpoint directly\n";

try {
    // Create request
    $request = new Illuminate\Http\Request();
    $request->merge([
        'npsn' => '11223345',
        'password' => 'password123'
    ]);
    
    // Set headers
    $request->headers->set('Content-Type', 'application/json');
    $request->headers->set('Accept', 'application/json');
    
    echo "📋 Request data: " . json_encode($request->all()) . "\n";
    echo "📋 Headers: " . json_encode($request->headers->all()) . "\n";
    
    // Call controller
    $controller = new App\Http\Controllers\SchoolAuthController();
    $response = $controller->login($request);
    
    echo "✅ Controller executed\n";
    echo "📋 Response type: " . get_class($response) . "\n";
    
    $data = $response->getData(true);
    echo "📋 Response data:\n";
    print_r($data);
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "📋 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
} catch (Error $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    echo "📋 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n==========================================\n";
echo "API Direct Test Complete!\n";
echo "==========================================\n";
?>