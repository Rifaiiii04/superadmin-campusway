<?php

/**
 * Optimized Seeder Script untuk menghindari timeout
 * Menggunakan batch processing dan memory optimization
 */

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\Student;
use App\Models\MajorRecommendation;
use App\Models\StudentChoice;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Set memory dan time limit
ini_set('memory_limit', '1024M');
set_time_limit(300);

try {
    echo "🚀 Starting Optimized StudentChoiceSeeder...\n\n";

    // Disable query logging untuk performa
    DB::disableQueryLog();

    // Get students dan majors dengan limit yang lebih kecil
    $students = Student::select('id', 'name', 'nisn')->take(10)->get();
    $majors = MajorRecommendation::select('id', 'major_name', 'category')
        ->where('is_active', true)
        ->take(20)
        ->get();

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
    $batchSize = 5; // Process in smaller batches

    // Process students in batches
    $studentBatches = $students->chunk($batchSize);
    
    foreach ($studentBatches as $batchIndex => $studentBatch) {
        echo "🔄 Processing batch " . ($batchIndex + 1) . " of " . $studentBatches->count() . "\n";
        
        foreach ($studentBatch as $index => $student) {
            // Skip some students to have variety
            if ($index % 3 === 0) {
                echo "⏭️  Skipping student {$student->name} (no choice)\n";
                continue;
            }

            // Pick a random major
            $randomMajor = $majors->random();

            // Check if student already has a choice (optimized query)
            $existingChoice = StudentChoice::where('student_id', $student->id)
                ->select('id')
                ->first();
            
            if (!$existingChoice) {
                // Use insert instead of create for better performance
                StudentChoice::insert([
                    'student_id' => $student->id,
                    'major_id' => $randomMajor->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                echo "✅ Created choice for student {$student->name} -> {$randomMajor->major_name}\n";
                $createdCount++;
            } else {
                echo "ℹ️  Student {$student->name} already has a choice\n";
                $skippedCount++;
            }
        }
        
        // Clear memory after each batch
        unset($studentBatch);
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
        
        echo "✅ Batch " . ($batchIndex + 1) . " completed\n\n";
    }

    echo "🎉 Optimized StudentChoiceSeeder completed!\n\n";
    
    // Show summary with optimized queries
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

    // Show some examples with optimized query
    echo "🔍 Sample choices:\n";
    $sampleChoices = StudentChoice::with(['student:id,name,nisn', 'major:id,major_name,category'])
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
