<?php
/**
 * Test Login Fix Script
 * Test the specific login issue after fixes
 */

echo "🧪 Testing Login Fix\n";
echo "===================\n\n";

// Simulate a login request
$testData = [
    'username' => 'admin', // Change this to your actual admin username
    'password' => 'password' // Change this to your actual admin password
];

echo "1. Testing Login Process:\n";
echo "   Username: " . $testData['username'] . "\n";
echo "   Password: " . str_repeat('*', strlen($testData['password'])) . "\n\n";

// Test the login endpoint
$loginUrl = 'http://103.23.198.101/super-admin/login';
$postData = http_build_query($testData);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => $postData,
        'follow_location' => false // Don't follow redirects automatically
    ]
]);

echo "2. Making Login Request:\n";
echo "   URL: $loginUrl\n";
echo "   Method: POST\n";
echo "   Data: " . $postData . "\n\n";

$response = @file_get_contents($loginUrl, false, $context);

if ($response === false) {
    echo "❌ Failed to make request\n";
    exit(1);
}

// Get response headers
$headers = $http_response_header ?? [];
$statusCode = 0;

foreach ($headers as $header) {
    if (strpos($header, 'HTTP/') === 0) {
        $statusCode = (int) substr($header, 9, 3);
        break;
    }
}

echo "3. Response Analysis:\n";
echo "   Status Code: $statusCode\n";

if ($statusCode === 302) {
    // Check for redirect location
    $redirectLocation = '';
    foreach ($headers as $header) {
        if (stripos($header, 'Location:') === 0) {
            $redirectLocation = trim(substr($header, 9));
            break;
        }
    }
    
    echo "   Redirect Location: $redirectLocation\n";
    
    if (strpos($redirectLocation, '/dashboard') !== false) {
        echo "   ✅ SUCCESS: Redirecting to correct dashboard\n";
    } elseif (strpos($redirectLocation, '/super-admin') !== false) {
        echo "   ⚠️  WARNING: Still redirecting to /super-admin (double URL issue)\n";
    } else {
        echo "   ❌ UNKNOWN: Redirecting to unexpected location\n";
    }
} elseif ($statusCode === 200) {
    echo "   ⚠️  WARNING: No redirect (login might have failed)\n";
    echo "   Response preview: " . substr($response, 0, 200) . "...\n";
} else {
    echo "   ❌ ERROR: Unexpected status code\n";
}

echo "\n4. Testing Dashboard Access:\n";
$dashboardUrl = 'http://103.23.198.101/super-admin/dashboard';
$dashboardResponse = @file_get_contents($dashboardUrl);

if ($dashboardResponse !== false) {
    echo "   ✅ Dashboard accessible: $dashboardUrl\n";
} else {
    echo "   ❌ Dashboard not accessible: $dashboardUrl\n";
}

echo "\n5. Testing Frontend Integration:\n";
$frontendUrl = 'http://103.23.198.101';
$frontendResponse = @file_get_contents($frontendUrl);

if ($frontendResponse !== false) {
    echo "   ✅ Frontend accessible: $frontendUrl\n";
    
    // Check if frontend shows login modal
    if (strpos($frontendResponse, 'login') !== false || strpos($frontendResponse, 'modal') !== false) {
        echo "   ⚠️  WARNING: Frontend might be showing login modal\n";
    } else {
        echo "   ✅ Frontend not showing login modal\n";
    }
} else {
    echo "   ❌ Frontend not accessible: $frontendUrl\n";
}

echo "\n✅ Test completed!\n";
echo "\n💡 If login redirects to wrong location, check:\n";
echo "   1. SuperAdminController login method\n";
echo "   2. RouteServiceProvider HOME constant\n";
echo "   3. HandleInertiaRequests middleware\n";
echo "   4. Auth guard configuration\n";
?>
