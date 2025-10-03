<?php
/**
 * Debug Double URL Final
 * Comprehensive debugging for the double URL issue
 */

echo "🐛 Debugging Double URL Issue - FINAL\n";
echo "=====================================\n\n";

// Test different URL patterns to identify the issue
$testUrls = [
    'http://103.23.198.101/super-admin',
    'http://103.23.198.101/super-admin/',
    'http://103.23.198.101/super-admin/login',
    'http://103.23.198.101/super-admin/dashboard',
    'http://103.23.198.101/super-admin/super-admin',
    'http://103.23.198.101/super-admin/super-admin/',
];

echo "1. Testing URL Patterns:\n";
foreach ($testUrls as $url) {
    echo "\nTesting: $url\n";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'follow_location' => false, // Don't follow redirects
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
            echo "  ❌ Contains double URL in content\n";
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
        if (strpos($response, 'name="email"') !== false) {
            echo "  ⚠️  Contains email field\n";
        }
        if (strpos($response, 'name="npsn"') !== false) {
            echo "  ❌ Contains NPSN field (Guru form)\n";
        }
    }
}

echo "\n\n2. Checking for Redirect Loops:\n";
// Test if there are any redirect loops
$redirectUrl = 'http://103.23.198.101/super-admin';
$maxRedirects = 5;
$currentUrl = $redirectUrl;
$redirectCount = 0;

while ($redirectCount < $maxRedirects) {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'follow_location' => false,
            'timeout' => 10
        ]
    ]);
    
    $response = @file_get_contents($currentUrl, false, $context);
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
    
    echo "  Redirect $redirectCount: $currentUrl -> Status: $statusCode\n";
    
    if ($statusCode === 302 && $redirectLocation) {
        echo "    Redirects to: $redirectLocation\n";
        $currentUrl = $redirectLocation;
        $redirectCount++;
        
        // Check if we're getting double URL
        if (strpos($redirectLocation, '/super-admin/super-admin') !== false) {
            echo "    ❌ FOUND DOUBLE URL IN REDIRECT!\n";
            break;
        }
    } else {
        break;
    }
}

echo "\n\n3. Recommendations:\n";
echo "  - Check Apache virtual host configuration\n";
echo "  - Check for any .htaccess redirects\n";
echo "  - Check Laravel routes for redirects\n";
echo "  - Check if Next.js is intercepting requests\n";
echo "  - Clear all caches\n";

echo "\n✅ Double URL debugging completed!\n";
?>
