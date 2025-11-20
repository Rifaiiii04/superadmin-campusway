<?php
/**
 * Test script for TKA Schedules endpoint
 * Run this from command line: php test-tka-schedules-endpoint.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "🧪 Testing TKA Schedules Endpoints\n";
echo "===================================\n\n";

// Test 1: GET /api/web/tka-schedules
echo "1️⃣ Testing GET /api/web/tka-schedules\n";
try {
    $request = Illuminate\Http\Request::create('/api/web/tka-schedules', 'GET');
    $response = $kernel->handle($request);
    $data = json_decode($response->getContent(), true);
    
    if ($response->getStatusCode() === 200 && isset($data['success']) && $data['success']) {
        echo "✅ Success! Found " . count($data['data'] ?? []) . " schedules\n";
    } else {
        echo "❌ Failed! Status: " . $response->getStatusCode() . "\n";
        echo "Response: " . $response->getContent() . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 2: GET /api/web/tka-schedules/upcoming
echo "2️⃣ Testing GET /api/web/tka-schedules/upcoming\n";
try {
    $request = Illuminate\Http\Request::create('/api/web/tka-schedules/upcoming', 'GET');
    $response = $kernel->handle($request);
    $data = json_decode($response->getContent(), true);
    
    if ($response->getStatusCode() === 200 && isset($data['success']) && $data['success']) {
        echo "✅ Success! Found " . count($data['data'] ?? []) . " upcoming schedules\n";
    } else {
        echo "❌ Failed! Status: " . $response->getStatusCode() . "\n";
        echo "Response: " . $response->getContent() . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
echo "\n";

// Test 3: Direct database query
echo "3️⃣ Testing Direct Database Query\n";
try {
    $schedules = \App\Models\TkaSchedule::where('is_active', true)
        ->where('end_date', '>=', now())
        ->orderBy('start_date', 'asc')
        ->limit(50)
        ->get();
    
    echo "✅ Database query successful! Found " . $schedules->count() . " upcoming schedules\n";
    
    if ($schedules->count() > 0) {
        echo "Sample schedule:\n";
        $first = $schedules->first();
        echo "  - ID: " . $first->id . "\n";
        echo "  - Title: " . $first->title . "\n";
        echo "  - Start Date: " . $first->start_date . "\n";
        echo "  - End Date: " . $first->end_date . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
echo "\n";

echo "===================================\n";
echo "✅ Testing complete!\n";

