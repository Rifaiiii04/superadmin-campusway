<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Teacher TKA Schedules API...\n\n";

// Test teacher API endpoint (without /school prefix)
$url = "http://127.0.0.1:8000/api/tka-schedules/upcoming";

echo "🌐 Testing Teacher API endpoint:\n";
echo "  - URL: {$url}\n";
echo "  - Method: GET\n\n";

// Use curl to test
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "📡 Teacher API Response:\n";
echo "  - HTTP Code: {$httpCode}\n";
echo "  - Response: {$response}\n";

if ($error) {
    echo "  - Error: {$error}\n";
}

// Parse response
$data = json_decode($response, true);
if ($data) {
    echo "\n📊 Parsed Response:\n";
    echo "  - Success: " . ($data['success'] ? 'true' : 'false') . "\n";
    if (isset($data['data'])) {
        echo "  - Data count: " . count($data['data']) . "\n";
        foreach ($data['data'] as $index => $schedule) {
            echo "  - Schedule " . ($index + 1) . ":\n";
            echo "    - ID: {$schedule['id']}\n";
            echo "    - Title: {$schedule['title']}\n";
            echo "    - Start Date: {$schedule['start_date']}\n";
            echo "    - End Date: {$schedule['end_date']}\n";
            echo "    - Status: {$schedule['status']}\n";
        }
    }
} else {
    echo "❌ Failed to parse JSON response\n";
}

echo "\n" . str_repeat("=", 50) . "\n";

// Also test with school_id parameter
$urlWithSchool = "http://127.0.0.1:8000/api/tka-schedules/upcoming?school_id=4";

echo "🌐 Testing Teacher API with school_id:\n";
echo "  - URL: {$urlWithSchool}\n";
echo "  - Method: GET\n\n";

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $urlWithSchool);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_TIMEOUT, 30);
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response2 = curl_exec($ch2);
$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
$error2 = curl_error($ch2);
curl_close($ch2);

echo "📡 Teacher API with school_id Response:\n";
echo "  - HTTP Code: {$httpCode2}\n";
echo "  - Response: {$response2}\n";

if ($error2) {
    echo "  - Error: {$error2}\n";
}
