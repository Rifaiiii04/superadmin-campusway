<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing delete student API with authentication...\n\n";

// Get a test student
$student = DB::table('students')->first();

if (!$student) {
    echo "❌ No students found in database\n";
    exit;
}

echo "📊 Test student found:\n";
echo "  - ID: {$student->id}\n";
echo "  - Name: {$student->name}\n";
echo "  - NISN: {$student->nisn}\n";
echo "  - School ID: {$student->school_id}\n\n";

// Get school data
$school = DB::table('schools')->where('id', $student->school_id)->first();
if (!$school) {
    echo "❌ School not found\n";
    exit;
}

echo "🏫 School found:\n";
echo "  - ID: {$school->id}\n";
echo "  - NPSN: {$school->npsn}\n";
echo "  - Name: {$school->name}\n\n";

// Create a test token
$timestamp = time();
$token = base64_encode("{$school->id}|{$timestamp}|{$school->npsn}");

echo "🔑 Test token created:\n";
echo "  - Token: " . substr($token, 0, 20) . "...\n";
echo "  - Decoded: " . base64_decode($token) . "\n\n";

// Test API endpoint with curl
$url = "http://127.0.0.1:8000/api/school/students/{$student->id}";
$headers = [
    "Authorization: Bearer {$token}",
    "Content-Type: application/json"
];

echo "🌐 Testing API endpoint:\n";
echo "  - URL: {$url}\n";
echo "  - Method: DELETE\n";
echo "  - Headers: " . implode(', ', $headers) . "\n\n";

// Use curl to test
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
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

// Check if student still exists
$studentExists = DB::table('students')->where('id', $student->id)->exists();
echo "  - Student still exists: " . ($studentExists ? 'YES' : 'NO') . "\n";

if (!$studentExists) {
    echo "🎉 Student successfully deleted via API!\n";
} else {
    echo "❌ Student was not deleted via API\n";
}

echo "\n📊 Current student count: " . DB::table('students')->count() . "\n";
