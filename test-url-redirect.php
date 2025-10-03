<?php
/**
 * Test URL Redirect Script
 * Test the URL redirect behavior
 */

echo "🔍 Testing URL Redirect Behavior\n";
echo "===============================\n\n";

// Test different URLs
$testUrls = [
    'http://103.23.198.101/super-admin',
    'http://103.23.198.101/super-admin/',
    'http://103.23.198.101/super-admin/login',
    'http://103.23.198.101/super-admin/dashboard',
    'http://103.23.198.101/super-admin/super-admin',
    'http://103.23.198.101/super-admin/super-admin/',
];

foreach ($testUrls as $url) {
    echo "Testing: $url\n";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'follow_location' => false, // Don't follow redirects
            'timeout' => 10
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    $headers = $http_response_header ?? [];
    
    $statusCode = 0;
    $redirectLocation = '';
    
    foreach ($headers as $header) {
        if (strpos($header, 'HTTP/') === 0) {
            $statusCode = (int) substr($header, 9, 3);
        }
        if (stripos($header, 'Location:') === 0) {
            $redirectLocation = trim(substr($header, 9));
        }
    }
    
    echo "  Status: $statusCode\n";
    if ($redirectLocation) {
        echo "  Redirect: $redirectLocation\n";
    }
    
    if ($statusCode === 200) {
        // Check if response contains login form
        if (strpos($response, 'Super Admin') !== false) {
            echo "  ✅ Shows Super Admin login\n";
        } elseif (strpos($response, 'Dashboard Guru') !== false) {
            echo "  ❌ Shows Dashboard Guru (WRONG!)\n";
        } else {
            echo "  ⚠️  Unknown content\n";
        }
    }
    
    echo "\n";
}

echo "✅ URL testing completed!\n";
?>
