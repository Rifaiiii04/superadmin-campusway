<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing TKA Schedules API...\n\n";

// Test upcoming schedules endpoint
$url = "http://127.0.0.1:8000/api/web/tka-schedules/upcoming";

echo "🌐 Testing API endpoint:\n";
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

echo "📡 API Response:\n";
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
