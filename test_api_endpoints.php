<?php
// =====================================================
// Test API Endpoints
// =====================================================

echo "==========================================\n";
echo "Test API Endpoints\n";
echo "==========================================\n\n";

// Test 1: Schools API
echo "🔍 Test 1: Schools API\n";
$url1 = "http://103.23.198.101/super-admin/api/web/schools";
$response1 = file_get_contents($url1);
echo "📋 URL: " . $url1 . "\n";
echo "📋 Response: " . substr($response1, 0, 200) . "...\n";
echo "📋 Status: " . (strpos($response1, '"success":true') !== false ? 'SUCCESS' : 'FAILED') . "\n\n";

// Test 2: School Login API
echo "🔍 Test 2: School Login API\n";
$url2 = "http://103.23.198.101/super-admin/api/school/login";
$data2 = json_encode(['npsn' => '11223345', 'password' => 'password123']);

$context2 = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
        'content' => $data2
    ]
]);

$response2 = file_get_contents($url2, false, $context2);
echo "📋 URL: " . $url2 . "\n";
echo "📋 Data: " . $data2 . "\n";
echo "📋 Response: " . $response2 . "\n";
echo "📋 Status: " . (strpos($response2, '"success":true') !== false ? 'SUCCESS' : 'FAILED') . "\n\n";

// Test 3: TKA Schedules API
echo "🔍 Test 3: TKA Schedules API\n";
$url3 = "http://103.23.198.101/super-admin/api/web/tka-schedules";
$response3 = file_get_contents($url3);
echo "📋 URL: " . $url3 . "\n";
echo "📋 Response: " . substr($response3, 0, 200) . "...\n";
echo "📋 Status: " . (strpos($response3, '"success":true') !== false ? 'SUCCESS' : 'FAILED') . "\n\n";

// Test 4: CORS Headers
echo "🔍 Test 4: CORS Headers\n";
$headers = get_headers("http://103.23.198.101/super-admin/api/web/schools", 1);
echo "📋 CORS Headers:\n";
foreach ($headers as $key => $value) {
    if (stripos($key, 'access-control') !== false || stripos($key, 'origin') !== false) {
        echo "   " . $key . ": " . (is_array($value) ? implode(', ', $value) : $value) . "\n";
    }
}

echo "\n==========================================\n";
echo "API Endpoints Test Complete!\n";
echo "==========================================\n";
?>