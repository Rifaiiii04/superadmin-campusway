<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ProgramStudiSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create program_studi_subjects table if not exists
        if (!Schema::hasTable('program_studi_subjects')) {
            Schema::create('program_studi_subjects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('program_studi_id')->constrained('program_studi');
                $table->foreignId('subject_id')->constrained('subjects');
                $table->enum('kurikulum_type', ['merdeka', '2013_ipa', '2013_ips', '2013_bahasa']);
                $table->boolean('is_required')->default(false);
                $table->timestamps();
            });
        }

        // Get subject IDs
        $subjects = DB::table('subjects')->pluck('id', 'code')->toArray();
        
        // Get program studi IDs
        $programStudi = DB::table('program_studi')->pluck('id', 'name')->toArray();

        // Mapping data according to the business process diagram
        $mappings = [
            // HUMANIORA - Seni
            [
                'program_studi' => 'Seni',
                'subjects' => [
                    'merdeka' => ['SENI'], // Seni Budaya
                    '2013_ipa' => ['SENI'],
                    '2013_ips' => ['SENI'],
                    '2013_bahasa' => ['SENI'],
                ]
            ],
            
            // HUMANIORA - Sejarah
            [
                'program_studi' => 'Sejarah',
                'subjects' => [
                    'merdeka' => ['SEJ'],
                    '2013_ipa' => ['SEJ'],
                    '2013_ips' => ['SEJ'],
                    '2013_bahasa' => ['SEJ'],
                ]
            ],
            
            // HUMANIORA - Linguistik
            [
                'program_studi' => 'Linguistik',
                'subjects' => [
                    'merdeka' => ['BIN_L', 'BIG_L'],
                    '2013_ipa' => ['BIN_L', 'BIG_L'],
                    '2013_ips' => ['BIN_L', 'BIG_L'],
                    '2013_bahasa' => ['BIN_L', 'BIG_L'],
                ]
            ],
            
            // HUMANIORA - Sastra
            [
                'program_studi' => 'Sastra',
                'subjects' => [
                    'merdeka' => ['BIN_L', 'BAR', 'BJE', 'BPR', 'BJP', 'BKO', 'BMA'],
                    '2013_ipa' => ['BIN_L', 'BAR', 'BJE', 'BPR', 'BJP', 'BKO', 'BMA'],
                    '2013_ips' => ['BIN_L', 'BAR', 'BJE', 'BPR', 'BJP', 'BKO', 'BMA'],
                    '2013_bahasa' => ['BIN_L', 'BAR', 'BJE', 'BPR', 'BJP', 'BKO', 'BMA'],
                ]
            ],
            
            // HUMANIORA - Filsafat
            [
                'program_studi' => 'Filsafat',
                'subjects' => [
                    'merdeka' => ['SOS'],
                    '2013_ipa' => ['SEJ'],
                    '2013_ips' => ['SOS'],
                    '2013_bahasa' => ['ANT'],
                ]
            ],
            
            // ILMU SOSIAL - Sosial
            [
                'program_studi' => 'Sosial',
                'subjects' => [
                    'merdeka' => ['SOS'],
                    '2013_ipa' => ['SEJ'],
                    '2013_ips' => ['SOS'],
                    '2013_bahasa' => ['ANT'],
                ]
            ],
            
            // ILMU SOSIAL - Ekonomi
            [
                'program_studi' => 'Ekonomi',
                'subjects' => [
                    'merdeka' => ['EKO', 'MTK_L'],
                    '2013_ipa' => ['MTK_L'],
                    '2013_ips' => ['EKO', 'MTK_L'],
                    '2013_bahasa' => ['MTK_L'],
                ]
            ],
            
            // ILMU SOSIAL - Pertahanan
            [
                'program_studi' => 'Pertahanan',
                'subjects' => [
                    'merdeka' => ['PPKN'],
                    '2013_ipa' => ['PPKN'],
                    '2013_ips' => ['PPKN'],
                    '2013_bahasa' => ['PPKN'],
                ]
            ],
            
            // ILMU SOSIAL - Psikologi
            [
                'program_studi' => 'Psikologi',
                'subjects' => [
                    'merdeka' => ['SOS', 'MTK_L'],
                    '2013_ipa' => ['MTK_L'],
                    '2013_ips' => ['SOS'],
                    '2013_bahasa' => ['MTK_L'],
                ]
            ],
            
            // ILMU ALAM - Kimia
            [
                'program_studi' => 'Kimia',
                'subjects' => [
                    'merdeka' => ['KIM'],
                    '2013_ipa' => ['KIM'],
                    '2013_ips' => ['KIM'],
                    '2013_bahasa' => ['KIM'],
                ]
            ],
            
            // ILMU ALAM - Ilmu Kebumian
            [
                'program_studi' => 'Ilmu Kebumian',
                'subjects' => [
                    'merdeka' => ['FIS', 'MTK_L'],
                    '2013_ipa' => ['FIS', 'MTK_L'],
                    '2013_ips' => ['FIS', 'GEO'],
                    '2013_bahasa' => ['FIS', 'MTK_L'],
                ]
            ],
            
            // ILMU ALAM - Ilmu Kelautan
            [
                'program_studi' => 'Ilmu Kelautan',
                'subjects' => [
                    'merdeka' => ['BIO'],
                    '2013_ipa' => ['BIO'],
                    '2013_ips' => ['BIO', 'GEO'],
                    '2013_bahasa' => ['BIO'],
                ]
            ],
            
            // ILMU ALAM - Biologi
            [
                'program_studi' => 'Biologi',
                'subjects' => [
                    'merdeka' => ['BIO'],
                    '2013_ipa' => ['BIO'],
                    '2013_ips' => ['BIO'],
                    '2013_bahasa' => ['BIO'],
                ]
            ],
            
            // ILMU ALAM - Biofisika
            [
                'program_studi' => 'Biofisika',
                'subjects' => [
                    'merdeka' => ['FIS'],
                    '2013_ipa' => ['FIS'],
                    '2013_ips' => ['FIS'],
                    '2013_bahasa' => ['FIS'],
                ]
            ],
            
            // ILMU ALAM - Fisika
            [
                'program_studi' => 'Fisika',
                'subjects' => [
                    'merdeka' => ['FIS'],
                    '2013_ipa' => ['FIS'],
                    '2013_ips' => ['FIS'],
                    '2013_bahasa' => ['FIS'],
                ]
            ],
            
            // ILMU ALAM - Astronomi
            [
                'program_studi' => 'Astronomi',
                'subjects' => [
                    'merdeka' => ['FIS', 'MTK_L'],
                    '2013_ipa' => ['FIS', 'MTK_L'],
                    '2013_ips' => ['FIS', 'MTK_L'],
                    '2013_bahasa' => ['FIS', 'MTK_L'],
                ]
            ],
            
            // ILMU FORMAL - Matematika
            [
                'program_studi' => 'Matematika',
                'subjects' => [
                    'merdeka' => ['MTK_L'],
                    '2013_ipa' => ['MTK_L'],
                    '2013_ips' => ['MTK_L'],
                    '2013_bahasa' => ['MTK_L'],
                ]
            ],
            
            // ILMU TERAPAN - Teknik
            [
                'program_studi' => 'Teknik',
                'subjects' => [
                    'merdeka' => ['FIS', 'KIM', 'MTK_L'],
                    '2013_ipa' => ['FIS', 'KIM', 'MTK_L'],
                    '2013_ips' => ['FIS', 'KIM', 'MTK_L'],
                    '2013_bahasa' => ['FIS', 'KIM', 'MTK_L'],
                ]
            ],
            
            // ILMU TERAPAN - Kedokteran
            [
                'program_studi' => 'Kedokteran',
                'subjects' => [
                    'merdeka' => ['BIO', 'KIM', 'FIS'],
                    '2013_ipa' => ['BIO', 'KIM', 'FIS'],
                    '2013_ips' => ['BIO', 'KIM', 'FIS'],
                    '2013_bahasa' => ['BIO', 'KIM', 'FIS'],
                ]
            ],
            
            // ILMU TERAPAN - Pertanian
            [
                'program_studi' => 'Pertanian',
                'subjects' => [
                    'merdeka' => ['BIO', 'KIM'],
                    '2013_ipa' => ['BIO', 'KIM'],
                    '2013_ips' => ['BIO', 'KIM'],
                    '2013_bahasa' => ['BIO', 'KIM'],
                ]
            ],
            
            // ILMU TERAPAN - Teknologi Informasi
            [
                'program_studi' => 'Teknologi Informasi',
                'subjects' => [
                    'merdeka' => ['MTK_L', 'FIS'],
                    '2013_ipa' => ['MTK_L', 'FIS'],
                    '2013_ips' => ['MTK_L', 'FIS'],
                    '2013_bahasa' => ['MTK_L', 'FIS'],
                ]
            ],
        ];

        // Insert mappings
        foreach ($mappings as $mapping) {
            $programStudiId = $programStudi[$mapping['program_studi']] ?? null;
            if (!$programStudiId) continue;

            foreach ($mapping['subjects'] as $kurikulumType => $subjectCodes) {
                foreach ($subjectCodes as $subjectCode) {
                    $subjectId = $subjects[$subjectCode] ?? null;
                    if (!$subjectId) continue;

                    DB::table('program_studi_subjects')->insert([
                        'program_studi_id' => $programStudiId,
                        'subject_id' => $subjectId,
                        'kurikulum_type' => $kurikulumType,
                        'is_required' => in_array($subjectCode, ['MTK_L', 'BIN_L', 'BIG_L']), // Wajib untuk mata pelajaran wajib
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
