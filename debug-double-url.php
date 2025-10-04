<?php
/**
 * Debug Double URL Issue
 * Comprehensive debugging for the double URL problem
 */

echo "🐛 Debugging Double URL Issue\n";
echo "============================\n\n";

// Test 1: Check current URL and redirects
echo "1. Testing URL Redirects:\n";
$testUrls = [
    'http://103.23.198.101/super-admin',
    'http://103.23.198.101/super-admin/',
    'http://103.23.198.101/super-admin/login',
    'http://103.23.198.101/super-admin/dashboard',
];

foreach ($testUrls as $url) {
    echo "\nTesting: $url\n";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'follow_location' => false,
            'timeout' => 10,
            'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    $headers = $http_response_header ?? [];
    
    $statusCode = 0;
    $redirectLocation = '';
    $contentType = '';
    
    foreach ($headers as $header) {
        if (strpos($header, 'HTTP/') === 0) {
            $statusCode = (int) substr($header, 9, 3);
        }
        if (stripos($header, 'Location:') === 0) {
            $redirectLocation = trim(substr($header, 9));
        }
        if (stripos($header, 'Content-Type:') === 0) {
            $contentType = trim(substr($header, 13));
        }
    }
    
    echo "  Status: $statusCode\n";
    echo "  Content-Type: $contentType\n";
    if ($redirectLocation) {
        echo "  Redirect: $redirectLocation\n";
    }
    
    if ($statusCode === 200) {
        // Analyze content
        if (strpos($response, 'Super Admin') !== false) {
            echo "  ✅ Contains 'Super Admin'\n";
        }
        if (strpos($response, 'Dashboard Guru') !== false) {
            echo "  ❌ Contains 'Dashboard Guru' (WRONG!)\n";
        }
        if (strpos($response, '/super-admin/super-admin') !== false) {
            echo "  ❌ Contains double URL\n";
        }
        if (strpos($response, 'csrf-token') !== false) {
            echo "  ✅ Contains CSRF token (Laravel)\n";
        }
        if (strpos($response, '_next') !== false) {
            echo "  ⚠️  Contains Next.js assets\n";
        }
        
        // Check for specific forms
        if (strpos($response, 'name="username"') !== false) {
            echo "  ✅ Contains username field\n";
        }
        if (strpos($response, 'name="npsn"') !== false) {
            echo "  ❌ Contains NPSN field (Guru form)\n";
        }
    }
}

echo "\n\n2. Testing Static Assets:\n";
$assetBase = 'http://103.23.198.101/super-admin/build/assets/';
$testAssets = [
    'app.js',
    'app.css',
    'manifest.json'
];

foreach ($testAssets as $asset) {
    $assetUrl = $assetBase . $asset;
    $response = @file_get_contents($assetUrl);
    if ($response !== false) {
        echo "  ✅ $asset - " . strlen($response) . " bytes\n";
    } else {
        echo "  ❌ $asset - Not found\n";
    }
}

echo "\n\n3. Testing Apache Configuration:\n";
// Test if Apache is properly configured
$apacheTestUrl = 'http://103.23.198.101/super-admin/test';
$response = @file_get_contents($apacheTestUrl);
if ($response !== false) {
    echo "  ✅ Apache is serving Laravel\n";
} else {
    echo "  ❌ Apache might not be properly configured\n";
}

echo "\n\n4. Testing Next.js Interference:\n";
// Test if Next.js is intercepting requests
$nextjsTestUrl = 'http://103.23.198.101/super-admin';
$response = @file_get_contents($nextjsTestUrl);
if ($response !== false) {
    if (strpos($response, 'Next.js') !== false || strpos($response, '_next') !== false) {
        echo "  ❌ Next.js is intercepting /super-admin requests\n";
    } else {
        echo "  ✅ Next.js is not interfering\n";
    }
}

echo "\n\n5. Recommendations:\n";
echo "  - Check Apache virtual host configuration\n";
echo "  - Ensure /super-admin is properly aliased to Laravel\n";
echo "  - Check if Next.js has any catch-all routes\n";
echo "  - Verify .htaccess files don't conflict\n";
echo "  - Check if there are any redirects in Laravel routes\n";

echo "\n✅ Debug completed!\n";
?>
