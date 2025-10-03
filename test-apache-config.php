<?php
/**
 * Test Apache Configuration
 * Check if Apache is properly configured
 */

echo "🔧 Testing Apache Configuration\n";
echo "==============================\n\n";

// Test if we can access the application
$baseUrl = 'http://103.23.198.101';

echo "1. Testing Base URL Access:\n";
$response = @file_get_contents($baseUrl);
if ($response !== false) {
    echo "   ✅ Base URL accessible: $baseUrl\n";
    
    // Check if it's Next.js or Laravel
    if (strpos($response, 'Next.js') !== false || strpos($response, '_next') !== false) {
        echo "   📱 Serving Next.js frontend\n";
    } elseif (strpos($response, 'Laravel') !== false || strpos($response, 'csrf-token') !== false) {
        echo "   🐘 Serving Laravel backend\n";
    } else {
        echo "   ❓ Unknown application\n";
    }
} else {
    echo "   ❌ Base URL not accessible\n";
}

echo "\n2. Testing Super Admin URL:\n";
$superAdminUrl = 'http://103.23.198.101/super-admin';
$response = @file_get_contents($superAdminUrl);
if ($response !== false) {
    echo "   ✅ Super Admin URL accessible: $superAdminUrl\n";
    
    // Check content
    if (strpos($response, 'Super Admin') !== false) {
        echo "   ✅ Shows Super Admin content\n";
    } elseif (strpos($response, 'Dashboard Guru') !== false) {
        echo "   ❌ Shows Dashboard Guru (WRONG!)\n";
    } else {
        echo "   ❓ Unknown content\n";
    }
    
    // Check for double URL in response
    if (strpos($response, '/super-admin/super-admin') !== false) {
        echo "   ❌ Contains double URL in response\n";
    } else {
        echo "   ✅ No double URL in response\n";
    }
} else {
    echo "   ❌ Super Admin URL not accessible\n";
}

echo "\n3. Testing Static Assets:\n";
$assetUrls = [
    'http://103.23.198.101/super-admin/build/assets/app.js',
    'http://103.23.198.101/super-admin/build/assets/app.css',
    'http://103.23.198.101/super-admin/build/manifest.json'
];

foreach ($assetUrls as $assetUrl) {
    $response = @file_get_contents($assetUrl);
    if ($response !== false) {
        echo "   ✅ Asset accessible: " . basename($assetUrl) . "\n";
    } else {
        echo "   ❌ Asset not accessible: " . basename($assetUrl) . "\n";
    }
}

echo "\n4. Testing Double URL Issue:\n";
$doubleUrl = 'http://103.23.198.101/super-admin/super-admin';
$response = @file_get_contents($doubleUrl);
if ($response !== false) {
    echo "   ⚠️  Double URL is accessible (this might be the problem)\n";
} else {
    echo "   ✅ Double URL not accessible (good)\n";
}

echo "\n5. Testing Server Headers:\n";
$headers = @get_headers('http://103.23.198.101/super-admin');
if ($headers) {
    foreach ($headers as $header) {
        if (stripos($header, 'server:') === 0) {
            echo "   Server: " . trim(substr($header, 7)) . "\n";
        }
        if (stripos($header, 'content-type:') === 0) {
            echo "   Content-Type: " . trim(substr($header, 13)) . "\n";
        }
    }
} else {
    echo "   ❌ Could not get headers\n";
}

echo "\n✅ Apache configuration test completed!\n";
echo "\n💡 If you see issues:\n";
echo "   1. Check Apache virtual host configuration\n";
echo "   2. Check if Next.js is intercepting /super-admin requests\n";
echo "   3. Check if there are any redirects in .htaccess\n";
echo "   4. Verify that Laravel is properly configured\n";
?>
