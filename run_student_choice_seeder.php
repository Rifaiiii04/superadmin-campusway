<?php

/**
 * Script untuk menjalankan StudentChoiceSeeder secara manual
 * Jalankan dengan: php run_student_choice_seeder.php
 */

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\Student;
use App\Models\MajorRecommendation;
use App\Models\StudentChoice;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "🚀 Starting StudentChoiceSeeder...\n\n";

    // Get some students and majors
    $students = Student::take(5)->get();
    $majors = MajorRecommendation::where('is_active', true)->take(10)->get();

    if ($students->isEmpty()) {
        echo "❌ No students found. Please run StudentSeeder first.\n";
        exit(1);
    }

    if ($majors->isEmpty()) {
        echo "❌ No majors found. Please run MajorRecommendationSeeder first.\n";
        exit(1);
    }

    echo "📊 Found {$students->count()} students and {$majors->count()} majors\n\n";

    $createdCount = 0;
    $skippedCount = 0;

    foreach ($students as $index => $student) {
        // Skip some students to have variety (some with choice, some without)
        if ($index % 3 === 0) {
            echo "⏭️  Skipping student {$student->name} (no choice)\n";
            continue;
        }

        // Pick a random major
        $randomMajor = $majors->random();

        // Check if student already has a choice
        $existingChoice = StudentChoice::where('student_id', $student->id)->first();
        
        if (!$existingChoice) {
            StudentChoice::create([
                'student_id' => $student->id,
                'major_id' => $randomMajor->id,
            ]);

            echo "✅ Created choice for student {$student->name} -> {$randomMajor->major_name}\n";
            $createdCount++;
        } else {
            echo "ℹ️  Student {$student->name} already has a choice\n";
            $skippedCount++;
        }
    }

    echo "\n🎉 StudentChoiceSeeder completed!\n\n";
    
    // Show summary
    $totalChoices = StudentChoice::count();
    $totalStudents = Student::count();
    $studentsWithChoice = StudentChoice::distinct('student_id')->count();
    
    echo "📈 Summary:\n";
    echo "- Total students: {$totalStudents}\n";
    echo "- Students with choice: {$studentsWithChoice}\n";
    echo "- Students without choice: " . ($totalStudents - $studentsWithChoice) . "\n";
    echo "- Total choices: {$totalChoices}\n";
    echo "- Created: {$createdCount}\n";
    echo "- Skipped: {$skippedCount}\n\n";

    // Show some examples
    echo "🔍 Sample choices:\n";
    $sampleChoices = StudentChoice::with(['student', 'major'])
        ->take(3)
        ->get();
    
    foreach ($sampleChoices as $choice) {
        echo "- {$choice->student->name} ({$choice->student->nisn}) -> {$choice->major->major_name} ({$choice->major->category})\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}
