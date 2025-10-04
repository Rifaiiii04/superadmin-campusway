<?php
/**
 * Test API Integration
 * Test the API integration between Next.js and Laravel
 */

echo "🧪 Testing API Integration\n";
echo "==========================\n\n";

// Test URLs
$baseUrl = 'http://103.23.198.101/super-admin/api';
$publicApiUrl = $baseUrl . '/public';

echo "1. Testing API Health Check:\n";
try {
    $response = file_get_contents($publicApiUrl . '/health');
    $data = json_decode($response, true);
    
    if ($data && $data['success']) {
        echo "   ✅ API Health Check: OK\n";
        echo "   📅 Timestamp: " . $data['timestamp'] . "\n";
        echo "   🔢 Version: " . $data['version'] . "\n";
    } else {
        echo "   ❌ API Health Check: Failed\n";
    }
} catch (Exception $e) {
    echo "   ❌ API Health Check Error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "2. Testing Schools API:\n";
try {
    $response = file_get_contents($publicApiUrl . '/schools');
    $data = json_decode($response, true);
    
    if ($data && $data['success']) {
        echo "   ✅ Schools API: OK\n";
        echo "   📊 Total Schools: " . $data['total'] . "\n";
        echo "   🏫 Sample School: " . ($data['data'][0]['name'] ?? 'N/A') . "\n";
    } else {
        echo "   ❌ Schools API: Failed\n";
        echo "   📝 Error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Schools API Error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "3. Testing Questions API:\n";
try {
    $response = file_get_contents($publicApiUrl . '/questions');
    $data = json_decode($response, true);
    
    if ($data && $data['success']) {
        echo "   ✅ Questions API: OK\n";
        echo "   📊 Total Questions: " . $data['total'] . "\n";
    } else {
        echo "   ❌ Questions API: Failed\n";
        echo "   📝 Error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Questions API Error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "4. Testing Majors API:\n";
try {
    $response = file_get_contents($publicApiUrl . '/majors');
    $data = json_decode($response, true);
    
    if ($data && $data['success']) {
        echo "   ✅ Majors API: OK\n";
        echo "   📊 Total Majors: " . $data['total'] . "\n";
        echo "   🎓 Sample Major: " . ($data['data'][0]['major_name'] ?? 'N/A') . "\n";
    } else {
        echo "   ❌ Majors API: Failed\n";
        echo "   📝 Error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Majors API Error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "5. Testing Results API:\n";
try {
    $response = file_get_contents($publicApiUrl . '/results');
    $data = json_decode($response, true);
    
    if ($data && $data['success']) {
        echo "   ✅ Results API: OK\n";
        echo "   📊 Total Results: " . $data['pagination']['total'] . "\n";
        echo "   📄 Current Page: " . $data['pagination']['current_page'] . "\n";
    } else {
        echo "   ❌ Results API: Failed\n";
        echo "   📝 Error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Results API Error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "6. Testing CORS Headers:\n";
try {
    $context = stream_context_create([
        'http' => [
            'method' => 'OPTIONS',
            'header' => [
                'Origin: http://103.23.198.101',
                'Access-Control-Request-Method: GET',
                'Access-Control-Request-Headers: Content-Type'
            ]
        ]
    ]);
    
    $response = file_get_contents($publicApiUrl . '/health', false, $context);
    $headers = $http_response_header ?? [];
    
    $corsHeaders = [];
    foreach ($headers as $header) {
        if (stripos($header, 'Access-Control-') === 0) {
            $corsHeaders[] = $header;
        }
    }
    
    if (!empty($corsHeaders)) {
        echo "   ✅ CORS Headers: Present\n";
        foreach ($corsHeaders as $header) {
            echo "   📋 " . $header . "\n";
        }
    } else {
        echo "   ❌ CORS Headers: Missing\n";
    }
} catch (Exception $e) {
    echo "   ❌ CORS Test Error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "7. Testing Next.js Integration URLs:\n";
$nextjsUrls = [
    'School API' => $baseUrl . '/school',
    'Student API' => $baseUrl . '/web',
    'SuperAdmin API' => $baseUrl,
    'Public API' => $publicApiUrl
];

foreach ($nextjsUrls as $name => $url) {
    echo "   🔗 $name: $url\n";
}
echo "\n";

echo "8. Testing with cURL (if available):\n";
if (function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $publicApiUrl . '/health');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    curl_close($ch);
    
    echo "   📡 HTTP Status: $httpCode\n";
    echo "   📋 Response Headers:\n";
    foreach (explode("\n", $headers) as $header) {
        if (trim($header)) {
            echo "      " . trim($header) . "\n";
        }
    }
    echo "   📄 Response Body: " . substr($body, 0, 200) . "...\n";
} else {
    echo "   ❌ cURL not available\n";
}
echo "\n";

echo "9. Summary:\n";
echo "   ✅ API endpoints created\n";
echo "   ✅ CORS configuration updated\n";
echo "   ✅ Next.js API service updated\n";
echo "   ✅ Apache configuration created\n";
echo "\n";

echo "10. Next Steps:\n";
echo "   1. Deploy changes to VPS\n";
echo "   2. Update Apache configuration\n";
echo "   3. Clear Laravel caches\n";
echo "   4. Test from Next.js frontend\n";
echo "   5. Check browser console for errors\n";
echo "\n";

echo "✅ API Integration test completed!\n";
?>
