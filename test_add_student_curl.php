<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing add student with curl...\n\n";

// Get a test school
$school = DB::table('schools')->first();

if (!$school) {
    echo "❌ No schools found in database\n";
    exit;
}

echo "🏫 Test school found:\n";
echo "  - ID: {$school->id}\n";
echo "  - NPSN: {$school->npsn}\n";
echo "  - Name: {$school->name}\n\n";

// Create a test token
$timestamp = time();
$token = base64_encode("{$school->id}|{$timestamp}|{$school->npsn}");

echo "🔑 Test token created:\n";
echo "  - Token: " . substr($token, 0, 20) . "...\n\n";

// Test data
$testData = [
    'nisn' => '6666666666',
    'name' => 'Curl Test Student',
    'kelas' => 'X IPA 1',
    'email' => 'curltest@example.com',
    'phone' => '081234567890',
    'parent_phone' => '081234567891',
    'password' => 'password123',
    'school_id' => $school->id
];

echo "📊 Test data:\n";
foreach ($testData as $key => $value) {
    echo "  - {$key}: {$value}\n";
}
echo "\n";

// Test API endpoint with curl
$url = "http://127.0.0.1:8000/api/school/students";
$headers = [
    "Authorization: Bearer {$token}",
    "Content-Type: application/json"
];

echo "🌐 Testing API endpoint:\n";
echo "  - URL: {$url}\n";
echo "  - Method: POST\n";
echo "  - Headers: " . implode(', ', $headers) . "\n\n";

// Use curl to test
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

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

// Check if student was created
$studentExists = DB::table('students')->where('nisn', $testData['nisn'])->exists();
echo "  - Student created: " . ($studentExists ? 'YES' : 'NO') . "\n";

if ($studentExists) {
    echo "🎉 Student successfully created via API!\n";
} else {
    echo "❌ Student was not created via API\n";
}

echo "\n📊 Current student count: " . DB::table('students')->count() . "\n";
