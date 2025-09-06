<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking Student Major Data...\n\n";

// Check students table
$students = DB::table('students')->get();
echo "📊 Total Students: " . $students->count() . "\n";

if ($students->count() > 0) {
    echo "\n👥 Students List:\n";
    foreach ($students as $student) {
        echo "  - {$student->name} (NISN: {$student->nisn}) - School ID: {$student->school_id}\n";
    }
}

// Check student_choices table
$studentChoices = DB::table('student_choices')->get();
echo "\n📊 Total Student Choices: " . $studentChoices->count() . "\n";

if ($studentChoices->count() > 0) {
    echo "\n🎯 Student Choices:\n";
    foreach ($studentChoices as $choice) {
        echo "  - Student ID: {$choice->student_id}, Major: {$choice->chosen_major}, Priority: {$choice->priority}\n";
    }
} else {
    echo "❌ No student choices found!\n";
}

// Check major_recommendations table
$majors = DB::table('major_recommendations')->get();
echo "\n📊 Total Majors: " . $majors->count() . "\n";

if ($majors->count() > 0) {
    echo "\n🎓 Sample Majors:\n";
    $sampleMajors = $majors->take(5);
    foreach ($sampleMajors as $major) {
        echo "  - {$major->major_name} (Rumpun: {$major->rumpun_ilmu})\n";
    }
}

// Check if there's a relationship between students and majors
echo "\n🔗 Checking Student-Major Relationships:\n";

$studentMajorCounts = DB::table('student_choices')
    ->join('students', 'student_choices.student_id', '=', 'students.id')
    ->join('major_recommendations', 'student_choices.chosen_major', '=', 'major_recommendations.major_name')
    ->select('major_recommendations.major_name', DB::raw('COUNT(*) as student_count'))
    ->groupBy('major_recommendations.major_name')
    ->orderBy('student_count', 'desc')
    ->get();

if ($studentMajorCounts->count() > 0) {
    echo "📈 Students per Major:\n";
    foreach ($studentMajorCounts as $count) {
        echo "  - {$count->major_name}: {$count->student_count} students\n";
    }
} else {
    echo "❌ No student-major relationships found!\n";
}

// Check the API endpoint that should provide this data
echo "\n🌐 Checking API endpoint...\n";

// Test the student statistics API
$url = "http://127.0.0.1:8000/api/student-statistics";
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
