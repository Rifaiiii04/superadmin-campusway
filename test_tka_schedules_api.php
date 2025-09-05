<?php

// Test TKA Schedules API endpoints
echo "🧪 Testing TKA Schedules API endpoints...\n\n";

// Test 1: Get all TKA schedules
echo "1. Testing GET /api/tka-schedules\n";
$url1 = "http://127.0.0.1:8000/api/tka-schedules";
$response1 = file_get_contents($url1);
$data1 = json_decode($response1, true);

echo "URL: $url1\n";
echo "Response: " . json_encode($data1, JSON_PRETTY_PRINT) . "\n\n";

// Test 2: Get upcoming TKA schedules
echo "2. Testing GET /api/tka-schedules/upcoming\n";
$url2 = "http://127.0.0.1:8000/api/tka-schedules/upcoming";
$response2 = file_get_contents($url2);
$data2 = json_decode($response2, true);

echo "URL: $url2\n";
echo "Response: " . json_encode($data2, JSON_PRETTY_PRINT) . "\n\n";

// Test 3: Test with school_id parameter
echo "3. Testing GET /api/tka-schedules?school_id=1\n";
$url3 = "http://127.0.0.1:8000/api/tka-schedules?school_id=1";
$response3 = file_get_contents($url3);
$data3 = json_decode($response3, true);

echo "URL: $url3\n";
echo "Response: " . json_encode($data3, JSON_PRETTY_PRINT) . "\n\n";

echo "✅ TKA Schedules API test completed!\n";
