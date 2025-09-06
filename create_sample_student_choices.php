<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🎯 Creating sample student choices...\n\n";

// Get some students
$students = DB::table('students')->take(5)->get();
echo "📊 Found " . $students->count() . " students\n";

// Get some majors
$majors = DB::table('major_recommendations')->take(10)->get();
echo "📊 Found " . $majors->count() . " majors\n";

if ($students->count() == 0) {
    echo "❌ No students found! Cannot create choices.\n";
    exit;
}

if ($majors->count() == 0) {
    echo "❌ No majors found! Cannot create choices.\n";
    exit;
}

// Create sample choices
$choices = [];
$majorIds = $majors->pluck('id')->toArray();

foreach ($students as $index => $student) {
    // Each student chooses 1-3 majors
    $numChoices = rand(1, 3);
    $selectedMajors = array_rand($majorIds, min($numChoices, count($majorIds)));
    
    if (!is_array($selectedMajors)) {
        $selectedMajors = [$selectedMajors];
    }
    
    foreach ($selectedMajors as $priority => $majorIndex) {
        $choices[] = [
            'student_id' => $student->id,
            'major_id' => $majorIds[$majorIndex],
            'priority' => $priority + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

// Insert choices
DB::table('student_choices')->insert($choices);
echo "✅ Created " . count($choices) . " student choices\n";

// Verify the data
$totalChoices = DB::table('student_choices')->count();
echo "📊 Total student choices now: {$totalChoices}\n";

// Show sample data
echo "\n📋 Sample Student Choices:\n";
$sampleChoices = DB::table('student_choices')
    ->join('students', 'student_choices.student_id', '=', 'students.id')
    ->join('major_recommendations', 'student_choices.major_id', '=', 'major_recommendations.id')
    ->select('students.name as student_name', 'major_recommendations.major_name', 'student_choices.priority')
    ->take(10)
    ->get();

foreach ($sampleChoices as $choice) {
    echo "  - {$choice->student_name} → {$choice->major_name} (Priority: {$choice->priority})\n";
}

// Test the Super Admin dashboard data
echo "\n🧪 Testing Super Admin dashboard data...\n";

$studentsPerMajor = DB::table('student_choices')
    ->join('major_recommendations', 'student_choices.major_id', '=', 'major_recommendations.id')
    ->selectRaw('major_recommendations.major_name, COUNT(*) as student_count')
    ->groupBy('major_recommendations.major_name')
    ->orderBy('student_count', 'desc')
    ->get();

echo "📈 Students per Major:\n";
foreach ($studentsPerMajor as $major) {
    echo "  - {$major->major_name}: {$major->student_count} students\n";
}

echo "\n✅ Sample student choices created successfully!\n";
