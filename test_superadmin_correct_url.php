<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Super Admin Dashboard with correct URL...\n\n";

// Test the correct dashboard endpoint
$url = "http://127.0.0.1:8000/super-admin";
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
echo "  - Response Length: " . strlen($response) . " characters\n";

if ($httpCode == 200) {
    echo "  - Response Type: " . (strpos($response, 'Inertia') !== false ? 'Inertia View' : 'JSON/Other') . "\n";
    if (strpos($response, 'studentsPerMajor') !== false) {
        echo "  - Contains studentsPerMajor: YES\n";
    } else {
        echo "  - Contains studentsPerMajor: NO\n";
    }
} else {
    echo "  - Response: " . substr($response, 0, 500) . "...\n";
}

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

echo "\n📊 Summary:\n";
echo "  - Total majors with students: " . $studentsPerMajor->count() . "\n";
echo "  - Total student choices: " . DB::table('student_choices')->count() . "\n";
echo "  - Data is ready for frontend display: " . ($studentsPerMajor->count() > 0 ? 'YES' : 'NO') . "\n";
