<?php
/**
 * Test Final Fix
 * Test the fix for Next.js intercept issue
 */

echo "🧪 Testing Final Fix for Next.js Intercept Issue\n";
echo "===============================================\n\n";

// Test different URLs to verify the fix
$testUrls = [
    'http://103.23.198.101/super-admin',
    'http://103.23.198.101/super-admin/',
    'http://103.23.198.101/super-admin/login',
    'http://103.23.198.101/super-admin/dashboard',
    'http://103.23.198.101/super-admin/super-admin',
    'http://103.23.198.101/super-admin/super-admin/',
    'http://103.23.198.101', // Next.js frontend
];

echo "1. Testing URL Responses:\n";
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
            echo "  ✅ Contains 'Super Admin' (Laravel)\n";
        }
        if (strpos($response, 'Dashboard Guru') !== false) {
            echo "  ❌ Contains 'Dashboard Guru' (Next.js - WRONG!)\n";
        }
        if (strpos($response, 'username') !== false) {
            echo "  ✅ Contains 'username' field (Laravel)\n";
        }
        if (strpos($response, 'NPSN') !== false) {
            echo "  ❌ Contains 'NPSN' field (Next.js - WRONG!)\n";
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
        if (strpos($response, 'Arah Potensi') !== false) {
            echo "  ℹ️  Contains 'Arah Potensi' (Next.js frontend)\n";
        }
    }
}

echo "\n\n2. Expected Results:\n";
echo "   ✅ http://103.23.198.101/super-admin - Should show Laravel login\n";
echo "   ✅ http://103.23.198.101/super-admin/login - Should show Laravel login\n";
echo "   ✅ http://103.23.198.101 - Should show Next.js frontend\n";
echo "   ❌ http://103.23.198.101/super-admin/super-admin - Should NOT exist\n\n";

echo "3. What to Look For:\n";
echo "   ✅ 'Super Admin Login' title (not 'Dashboard Guru')\n";
echo "   ✅ Username field (not NPSN field)\n";
echo "   ✅ Password field\n";
echo "   ✅ No double URL in address bar\n";
echo "   ✅ No modal popup from Next.js\n\n";

echo "4. If Still Showing 'Dashboard Guru':\n";
echo "   - Clear browser cache (Ctrl+F5)\n";
echo "   - Check Apache configuration\n";
echo "   - Verify Apache is handling /super-admin first\n";
echo "   - Check Apache error logs\n";
echo "   - Restart Apache service\n\n";

echo "5. Debug Commands:\n";
echo "   # Check Apache config\n";
echo "   sudo apache2ctl configtest\n";
echo "   \n";
echo "   # Check Apache error logs\n";
echo "   sudo tail -f /var/log/apache2/tka_error.log\n";
echo "   \n";
echo "   # Check if site is enabled\n";
echo "   sudo a2ensite tka\n";
echo "   \n";
echo "   # Restart Apache\n";
echo "   sudo systemctl restart apache2\n";
echo "   \n";
echo "   # Clear Laravel caches\n";
echo "   php artisan config:clear\n";
echo "   php artisan route:clear\n";
echo "   php artisan view:clear\n";
echo "   php artisan cache:clear\n\n";

echo "✅ Final fix testing completed!\n";
echo "\n💡 The key is ensuring Apache handles /super-admin BEFORE Next.js!\n";
?>
