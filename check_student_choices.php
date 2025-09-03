<?php

/**
 * Script untuk mengecek data pilihan jurusan siswa
 * Jalankan dengan: php check_student_choices.php
 */

require_once 'vendor/autoload.php';

use App\Models\Student;
use App\Models\MajorRecommendation;
use App\Models\StudentChoice;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "🔍 Checking Student Choices Data...\n\n";

    // Check students
    $totalStudents = Student::count();
    echo "📊 Total Students: {$totalStudents}\n";
    
    if ($totalStudents > 0) {
        echo "Sample students:\n";
        $sampleStudents = Student::take(3)->get();
        foreach ($sampleStudents as $student) {
            echo "- ID: {$student->id}, Name: {$student->name}, NISN: {$student->nisn}\n";
        }
    }

    echo "\n";

    // Check majors
    $totalMajors = MajorRecommendation::count();
    $activeMajors = MajorRecommendation::where('is_active', true)->count();
    echo "📊 Total Majors: {$totalMajors} (Active: {$activeMajors})\n";
    
    if ($activeMajors > 0) {
        echo "Sample active majors:\n";
        $sampleMajors = MajorRecommendation::where('is_active', true)->take(3)->get();
        foreach ($sampleMajors as $major) {
            echo "- ID: {$major->id}, Name: {$major->major_name}, Category: {$major->category}\n";
        }
    }

    echo "\n";

    // Check student choices
    $totalChoices = StudentChoice::count();
    $studentsWithChoice = StudentChoice::distinct('student_id')->count();
    $studentsWithoutChoice = $totalStudents - $studentsWithChoice;

    echo "📊 Student Choices:\n";
    echo "- Total choices: {$totalChoices}\n";
    echo "- Students with choice: {$studentsWithChoice}\n";
    echo "- Students without choice: {$studentsWithoutChoice}\n";

    if ($totalChoices > 0) {
        echo "\nSample choices:\n";
        $sampleChoices = StudentChoice::with(['student', 'major'])
            ->take(5)
            ->get();
        
        foreach ($sampleChoices as $choice) {
            echo "- Student: {$choice->student->name} ({$choice->student->nisn}) -> Major: {$choice->major->major_name} ({$choice->major->category})\n";
        }
    }

    echo "\n";

    // Test API endpoint data
    if ($totalStudents > 0) {
        echo "🧪 Testing API endpoint data:\n";
        $testStudent = Student::first();
        $testChoice = StudentChoice::where('student_id', $testStudent->id)->first();
        
        if ($testChoice) {
            echo "✅ Student {$testStudent->name} (ID: {$testStudent->id}) has chosen major ID: {$testChoice->major_id}\n";
            echo "   API Response would be: {\"has_choice\": true, \"selected_major_id\": {$testChoice->major_id}}\n";
        } else {
            echo "ℹ️  Student {$testStudent->name} (ID: {$testStudent->id}) has no choice\n";
            echo "   API Response would be: {\"has_choice\": false, \"selected_major_id\": null}\n";
        }
    }

    echo "\n";

    // Recommendations
    if ($totalChoices === 0) {
        echo "⚠️  No student choices found!\n";
        echo "   Run the seeder to create sample data:\n";
        echo "   1. php artisan db:seed --class=StudentChoiceSeeder\n";
        echo "   2. Or run: php run_student_choice_seeder.php\n";
        echo "   3. Or run: run_seeder.bat (Windows)\n";
    } else {
        echo "✅ Student choices data is available!\n";
        echo "   You can now test the major selection feature in the frontend.\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getFile() . "\n";
    exit(1);
}
