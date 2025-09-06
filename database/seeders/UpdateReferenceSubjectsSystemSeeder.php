<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateReferenceSubjectsSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "📚 Updating reference subjects system according to new rules...\n";

        // Check if tables exist
        if (!Schema::hasTable('program_studi_subjects')) {
            echo "❌ Table program_studi_subjects not found!\n";
            return;
        }

        if (!Schema::hasTable('subjects')) {
            echo "❌ Table subjects not found!\n";
            return;
        }

        // Clear all existing mappings
        DB::table('program_studi_subjects')->truncate();
        echo "🧹 Cleared all existing mappings\n";

        // Define reference subjects based on Pusmendik table
        $referenceSubjects = [
            // HUMANIORA
            'Seni' => [
                'merdeka' => ['Seni Budaya'],
                '2013_ipa' => ['Seni Budaya'],
                '2013_ips' => ['Seni Budaya'],
                '2013_bahasa' => ['Seni Budaya']
            ],
            'Sejarah' => [
                'merdeka' => ['Sejarah'],
                '2013_ipa' => ['Sejarah Indonesia'],
                '2013_ips' => ['Sejarah Indonesia'],
                '2013_bahasa' => ['Sejarah Indonesia']
            ],
            'Linguistik' => [
                'merdeka' => ['Bahasa Indonesia Lanjutan', 'Bahasa Inggris Lanjutan'],
                '2013_ipa' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                '2013_ips' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                '2013_bahasa' => ['Bahasa Indonesia', 'Bahasa Inggris']
            ],
            'Sastra' => [
                'merdeka' => ['Bahasa Indonesia Lanjutan', 'Bahasa Arab', 'Bahasa Jerman', 'Bahasa Prancis', 'Bahasa Jepang', 'Bahasa Korea', 'Bahasa Mandarin'],
                '2013_ipa' => ['Bahasa Indonesia', 'Bahasa Arab', 'Bahasa Jerman', 'Bahasa Prancis', 'Bahasa Jepang', 'Bahasa Korea', 'Bahasa Mandarin'],
                '2013_ips' => ['Bahasa Indonesia', 'Bahasa Arab', 'Bahasa Jerman', 'Bahasa Prancis', 'Bahasa Jepang', 'Bahasa Korea', 'Bahasa Mandarin'],
                '2013_bahasa' => ['Bahasa Indonesia', 'Bahasa Arab', 'Bahasa Jerman', 'Bahasa Prancis', 'Bahasa Jepang', 'Bahasa Korea', 'Bahasa Mandarin']
            ],
            'Filsafat' => [
                'merdeka' => ['Sosiologi'],
                '2013_ipa' => ['Sejarah Indonesia'],
                '2013_ips' => ['Sosiologi'],
                '2013_bahasa' => ['Antropologi']
            ],

            // ILMU SOSIAL
            'Sosial' => [
                'merdeka' => ['Sosiologi'],
                '2013_ipa' => ['Sejarah Indonesia'],
                '2013_ips' => ['Sosiologi'],
                '2013_bahasa' => ['Antropologi']
            ],
            'Ekonomi' => [
                'merdeka' => ['Ekonomi', 'Matematika Lanjutan'],
                '2013_ipa' => ['Matematika'],
                '2013_ips' => ['Ekonomi', 'Matematika'],
                '2013_bahasa' => ['Matematika']
            ],
            'Pertahanan' => [
                'merdeka' => ['PPKn/Pendidikan Pancasila'],
                '2013_ipa' => ['PPKn/Pendidikan Pancasila'],
                '2013_ips' => ['PPKn/Pendidikan Pancasila'],
                '2013_bahasa' => ['PPKn/Pendidikan Pancasila']
            ],
            'Psikologi' => [
                'merdeka' => ['Sosiologi', 'Matematika Lanjutan'],
                '2013_ipa' => ['Matematika'],
                '2013_ips' => ['Sosiologi'],
                '2013_bahasa' => ['Matematika']
            ],

            // ILMU ALAM
            'Kimia' => [
                'merdeka' => ['Kimia'],
                '2013_ipa' => ['Kimia'],
                '2013_ips' => ['Kimia'],
                '2013_bahasa' => ['Kimia']
            ],
            'Ilmu Kebumian' => [
                'merdeka' => ['Fisika', 'Matematika Lanjutan'],
                '2013_ipa' => ['Fisika', 'Matematika'],
                '2013_ips' => ['Fisika', 'Geografi'],
                '2013_bahasa' => ['Fisika', 'Matematika']
            ],
            'Ilmu Kelautan' => [
                'merdeka' => ['Biologi'],
                '2013_ipa' => ['Biologi'],
                '2013_ips' => ['Biologi', 'Geografi'],
                '2013_bahasa' => ['Biologi']
            ],
            'Biologi' => [
                'merdeka' => ['Biologi'],
                '2013_ipa' => ['Biologi'],
                '2013_ips' => ['Biologi'],
                '2013_bahasa' => ['Biologi']
            ],
            'Biofisika' => [
                'merdeka' => ['Fisika'],
                '2013_ipa' => ['Fisika'],
                '2013_ips' => ['Fisika'],
                '2013_bahasa' => ['Fisika']
            ],
            'Fisika' => [
                'merdeka' => ['Fisika'],
                '2013_ipa' => ['Fisika'],
                '2013_ips' => ['Fisika'],
                '2013_bahasa' => ['Fisika']
            ],
            'Astronomi' => [
                'merdeka' => ['Fisika', 'Matematika Lanjutan'],
                '2013_ipa' => ['Fisika', 'Matematika'],
                '2013_ips' => ['Fisika', 'Matematika'],
                '2013_bahasa' => ['Fisika', 'Matematika']
            ],

            // ILMU FORMAL
            'Komputer' => [
                'merdeka' => ['Matematika Lanjutan'],
                '2013_ipa' => ['Matematika'],
                '2013_ips' => ['Matematika'],
                '2013_bahasa' => ['Matematika']
            ],
            'Logika' => [
                'merdeka' => ['Matematika Lanjutan'],
                '2013_ipa' => ['Matematika'],
                '2013_ips' => ['Matematika'],
                '2013_bahasa' => ['Matematika']
            ],
            'Matematika' => [
                'merdeka' => ['Matematika Lanjutan'],
                '2013_ipa' => ['Matematika'],
                '2013_ips' => ['Matematika'],
                '2013_bahasa' => ['Matematika']
            ],

            // ILMU TERAPAN - Sample (can be expanded)
            'Ilmu Pertanian' => [
                'merdeka' => ['Biologi'],
                '2013_ipa' => ['Biologi'],
                '2013_ips' => ['Biologi'],
                '2013_bahasa' => ['Biologi']
            ],
            'Teknik Rekayasa' => [
                'merdeka' => ['Fisika', 'Kimia', 'Matematika Lanjutan'],
                '2013_ipa' => ['Fisika', 'Kimia', 'Matematika'],
                '2013_ips' => ['Fisika', 'Kimia', 'Matematika'],
                '2013_bahasa' => ['Fisika', 'Kimia', 'Matematika']
            ],
            'Ilmu Kedokteran' => [
                'merdeka' => ['Biologi', 'Kimia'],
                '2013_ipa' => ['Biologi', 'Kimia'],
                '2013_ips' => ['Biologi', 'Kimia'],
                '2013_bahasa' => ['Biologi', 'Kimia']
            ]
        ];

        // Get all subjects
        $subjects = DB::table('subjects')->pluck('id', 'name');
        
        // Get all program studi
        $programStudi = DB::table('program_studi')->get();

        $totalMappings = 0;

        foreach ($programStudi as $program) {
            $programName = $program->name;
            echo "📖 Processing: {$programName}\n";

            if (!isset($referenceSubjects[$programName])) {
                echo "  ⚠️ No reference subjects defined for: {$programName}\n";
                continue;
            }

            $programReferences = $referenceSubjects[$programName];

            foreach ($programReferences as $curriculum => $subjectNames) {
                foreach ($subjectNames as $subjectName) {
                    // Find subject ID
                    $subjectId = null;
                    foreach ($subjects as $name => $id) {
                        if (stripos($name, $subjectName) !== false || stripos($subjectName, $name) !== false) {
                            $subjectId = $id;
                            break;
                        }
                    }

                    if ($subjectId) {
                        // Insert mapping
                        DB::table('program_studi_subjects')->insert([
                            'program_studi_id' => $program->id,
                            'subject_id' => $subjectId,
                            'kurikulum_type' => $curriculum,
                            'is_required' => false,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        echo "  ✅ {$curriculum}: {$subjectName} (ID: {$subjectId})\n";
                        $totalMappings++;
                    } else {
                        echo "  ❌ Subject not found: {$subjectName}\n";
                    }
                }
            }
        }

        echo "\n🎉 Reference subjects system update completed!\n";
        echo "📊 Total mappings created: {$totalMappings}\n";
        echo "📋 System follows new rules:\n";
        echo "  - Based on frequency across curricula (Merdeka & 2013)\n";
        echo "  - Multiple choices (dan/atau) both included\n";
        echo "  - Most relevant to program characteristics\n";
        echo "  - Excludes mandatory subjects (Bahasa Indonesia, Bahasa Inggris, Matematika)\n";
    }
}
