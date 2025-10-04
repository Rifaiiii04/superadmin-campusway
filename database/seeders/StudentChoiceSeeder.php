<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\MajorRecommendation;
use App\Models\StudentChoice;

class StudentChoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some students and majors
        $students = Student::take(5)->get();
        $majors = MajorRecommendation::where('is_active', true)->take(10)->get();

        if ($students->isEmpty() || $majors->isEmpty()) {
            $this->command->warn('No students or majors found. Please run StudentSeeder and MajorRecommendationSeeder first.');
            return;
        }

        $this->command->info('Creating student choices...');

        foreach ($students as $index => $student) {
            // Skip some students to have variety (some with choice, some without)
            if ($index % 3 === 0) {
                continue; // Skip every 3rd student to leave them without choice
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

                $this->command->info("Created choice for student {$student->name} -> {$randomMajor->major_name}");
            } else {
                $this->command->info("Student {$student->name} already has a choice");
            }
        }

        $this->command->info('Student choices created successfully!');
        
        // Show summary
        $totalChoices = StudentChoice::count();
        $totalStudents = Student::count();
        $studentsWithChoice = StudentChoice::distinct('student_id')->count();
        
        $this->command->info("Summary:");
        $this->command->info("- Total students: {$totalStudents}");
        $this->command->info("- Students with choice: {$studentsWithChoice}");
        $this->command->info("- Students without choice: " . ($totalStudents - $studentsWithChoice));
        $this->command->info("- Total choices: {$totalChoices}");
    }
}
