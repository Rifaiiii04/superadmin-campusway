<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Super Admin Dashboard API...\n\n";

// Test the dashboard endpoint
$url = "http://127.0.0.1:8000/super-admin/dashboard";
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

echo "📡 Super Admin Dashboard API Response:\n";
echo "  - HTTP Code: {$httpCode}\n";
echo "  - Response: {$response}\n";

if ($error) {
    echo "  - Error: {$error}\n";
}

// Also test the data directly from database
echo "\n🔍 Direct Database Test:\n";

// Simulate the SuperAdminController dashboard method
$studentsPerMajor = DB::table('student_choices')
    ->join('major_recommendations', 'student_choices.major_id', '=', 'major_recommendations.id')
    ->selectRaw('major_recommendations.major_name, COUNT(*) as student_count')
    ->groupBy('major_recommendations.major_name')
    ->orderBy('student_count', 'desc')
    ->get();

echo "📈 Students per Major (from database):\n";
foreach ($studentsPerMajor as $major) {
    echo "  - {$major->major_name}: {$major->student_count} students\n";
}

// Check if this data would be passed to the frontend
echo "\n📊 Data that should be passed to frontend:\n";
echo "  - studentsPerMajor count: " . $studentsPerMajor->count() . "\n";
echo "  - Data: " . json_encode($studentsPerMajor->toArray()) . "\n";
