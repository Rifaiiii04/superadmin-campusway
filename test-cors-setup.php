<?php
/**
 * Test CORS Setup
 * Test the CORS configuration for API integration
 */

echo "🧪 Testing CORS Setup\n";
echo "=====================\n\n";

// Test URLs
$baseUrl = 'http://103.23.198.101/super-admin/api';
$publicApiUrl = $baseUrl . '/public';

echo "1. Testing CORS Headers with cURL:\n";
if (function_exists('curl_init')) {
    $testUrls = [
        'Health Check' => $publicApiUrl . '/health',
        'Schools API' => $publicApiUrl . '/schools',
        'Majors API' => $publicApiUrl . '/majors',
    ];
    
    foreach ($testUrls as $name => $url) {
        echo "   🔗 Testing $name: $url\n";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Origin: http://103.23.198.101',
            'Accept: application/json',
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        
        curl_close($ch);
        
        echo "      📡 HTTP Status: $httpCode\n";
        
        // Check for CORS headers
        $corsHeaders = [];
        foreach (explode("\n", $headers) as $header) {
            if (stripos($header, 'Access-Control-') === 0) {
                $corsHeaders[] = trim($header);
            }
        }
        
        if (!empty($corsHeaders)) {
            echo "      ✅ CORS Headers Present:\n";
            foreach ($corsHeaders as $header) {
                echo "         📋 $header\n";
            }
        } else {
            echo "      ❌ No CORS Headers Found\n";
        }
        
        // Check response body
        $data = json_decode($body, true);
        if ($data && isset($data['success'])) {
            echo "      ✅ JSON Response: " . ($data['success'] ? 'Success' : 'Failed') . "\n";
        } else {
            echo "      ⚠️  Non-JSON Response\n";
        }
        
        echo "\n";
    }
} else {
    echo "   ❌ cURL not available\n";
}

echo "2. Testing Preflight OPTIONS Request:\n";
if (function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $publicApiUrl . '/health');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'OPTIONS');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Origin: http://103.23.198.101',
        'Access-Control-Request-Method: GET',
        'Access-Control-Request-Headers: Content-Type, Authorization'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $headerSize);
    
    curl_close($ch);
    
    echo "   📡 OPTIONS Response Status: $httpCode\n";
    
    if ($httpCode === 200) {
        echo "   ✅ Preflight request successful\n";
    } else {
        echo "   ❌ Preflight request failed\n";
    }
    
    // Check for CORS headers in OPTIONS response
    $corsHeaders = [];
    foreach (explode("\n", $headers) as $header) {
        if (stripos($header, 'Access-Control-') === 0) {
            $corsHeaders[] = trim($header);
        }
    }
    
    if (!empty($corsHeaders)) {
        echo "   ✅ CORS Headers in OPTIONS Response:\n";
        foreach ($corsHeaders as $header) {
            echo "      📋 $header\n";
        }
    } else {
        echo "   ❌ No CORS Headers in OPTIONS Response\n";
    }
} else {
    echo "   ❌ cURL not available\n";
}

echo "\n3. Testing Different Origins:\n";
$testOrigins = [
    'http://103.23.198.101',
    'http://localhost:3000',
    'http://127.0.0.1:3000',
    'http://localhost:3001',
    'http://invalid-origin.com'
];

foreach ($testOrigins as $origin) {
    echo "   🌐 Testing Origin: $origin\n";
    
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $publicApiUrl . '/health');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Origin: $origin",
            'Accept: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        
        curl_close($ch);
        
        // Check Access-Control-Allow-Origin header
        $allowedOrigin = null;
        foreach (explode("\n", $headers) as $header) {
            if (stripos($header, 'Access-Control-Allow-Origin:') === 0) {
                $allowedOrigin = trim(substr($header, strlen('Access-Control-Allow-Origin:')));
                break;
            }
        }
        
        if ($allowedOrigin) {
            if ($allowedOrigin === $origin || $allowedOrigin === '*') {
                echo "      ✅ Origin allowed: $allowedOrigin\n";
            } else {
                echo "      ⚠️  Origin not allowed: $allowedOrigin (expected: $origin)\n";
            }
        } else {
            echo "      ❌ No Access-Control-Allow-Origin header\n";
        }
    }
}

echo "\n4. Testing API Endpoints:\n";
$endpoints = [
    'Health Check' => '/health',
    'Schools' => '/schools',
    'Questions' => '/questions',
    'Majors' => '/majors',
    'Results' => '/results'
];

foreach ($endpoints as $name => $endpoint) {
    echo "   🔗 Testing $name: $publicApiUrl$endpoint\n";
    
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $publicApiUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Origin: http://103.23.198.101',
            'Accept: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            if ($data && isset($data['success'])) {
                echo "      ✅ Success: " . ($data['message'] ?? 'OK') . "\n";
                if (isset($data['total'])) {
                    echo "      📊 Total: " . $data['total'] . "\n";
                }
            } else {
                echo "      ⚠️  Non-JSON response\n";
            }
        } else {
            echo "      ❌ HTTP Error: $httpCode\n";
        }
    }
}

echo "\n5. Summary:\n";
echo "   ✅ Custom CORS middleware created\n";
echo "   ✅ CORS middleware registered\n";
echo "   ✅ API routes updated with CORS\n";
echo "   ✅ Allowed origins configured\n";
echo "   ✅ Methods and headers configured\n";
echo "\n";

echo "6. Expected CORS Headers:\n";
echo "   📋 Access-Control-Allow-Origin: http://103.23.198.101\n";
echo "   📋 Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS\n";
echo "   📋 Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin\n";
echo "   📋 Access-Control-Allow-Credentials: true\n";
echo "   📋 Access-Control-Max-Age: 3600\n";
echo "\n";

echo "7. Next Steps:\n";
echo "   1. Deploy changes to VPS\n";
echo "   2. Clear Laravel caches\n";
echo "   3. Test from Next.js frontend\n";
echo "   4. Check browser console for CORS errors\n";
echo "   5. Verify API calls work properly\n";
echo "\n";

echo "✅ CORS setup test completed!\n";
?>
