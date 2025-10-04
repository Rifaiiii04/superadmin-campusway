<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample results for monitoring
        $this->createSampleResults();
        
        // Create sample recommendations
        $this->createSampleRecommendations();
        
        $this->command->info('Sample data created successfully!');
    }
    
    private function createSampleResults()
    {
        // Get existing students
        $students = DB::table('students')->get();
        
        if ($students->count() > 0) {
            $subjects = ['Bahasa Indonesia', 'Matematika', 'Bahasa Inggris', 'Fisika', 'Biologi'];
            
            foreach ($students as $student) {
                foreach ($subjects as $subject) {
                    // Create 1-3 results per student per subject
                    $resultCount = rand(1, 3);
                    
                    for ($i = 0; $i < $resultCount; $i++) {
                        DB::table('results')->insert([
                            'student_id' => $student->id,
                            'subject' => $subject,
                            'score' => rand(60, 95), // Random score between 60-95
                            'created_at' => now()->subDays(rand(1, 30)),
                            'updated_at' => now()->subDays(rand(1, 30)),
                        ]);
                    }
                }
            }
        }
    }
    
    private function createSampleRecommendations()
    {
        // Get existing students
        $students = DB::table('students')->get();
        
        if ($students->count() > 0) {
            $majors = [
                'Teknik Informatika',
                'Teknik Mesin',
                'Teknik Elektro',
                'Kedokteran',
                'Farmasi',
                'Ekonomi',
                'Manajemen',
                'Sastra Inggris',
                'Pendidikan Matematika',
                'Biologi'
            ];
            
            foreach ($students as $student) {
                // Create 2-4 recommendations per student
                $recommendationCount = rand(2, 4);
                $selectedMajors = array_rand(array_flip($majors), $recommendationCount);
                
                foreach ($selectedMajors as $major) {
                    DB::table('recommendations')->insert([
                        'student_id' => $student->id,
                        'major' => $major,
                        'description' => "Rekomendasi jurusan {$major} berdasarkan hasil tes",
                        'confidence_score' => rand(70, 95),
                        'created_at' => now()->subDays(rand(1, 30)),
                        'updated_at' => now()->subDays(rand(1, 30)),
                    ]);
                }
            }
        }
    }
}
