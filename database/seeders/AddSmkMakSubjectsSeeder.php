<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSmkMakSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🏭 Adding SMK/MAK subjects according to Pusmendik reference...\n";

        // Check if subjects table exists
        if (!Schema::hasTable('subjects')) {
            echo "❌ Table subjects not found!\n";
            return;
        }

        // SMK/MAK subjects according to Pusmendik reference
        $smkMakSubjects = [
            // Pilihan pertama - Produk/Projek Kreatif dan Kewirausahaan (19)
            [
                'name' => 'Produk/Projek Kreatif dan Kewirausahaan',
                'code' => 'PPKK',
                'description' => 'Mata pelajaran pilihan pertama untuk SMK/MAK - Produk/Projek Kreatif dan Kewirausahaan',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Pertama',
                'subject_number' => 19,
                'type' => 'pilihan'
            ],
            // Pilihan kedua - Mata pelajaran pilihan dari 1-18
            [
                'name' => 'Matematika Lanjutan SMK',
                'code' => 'MLSMK',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - Matematika Lanjutan',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 1,
                'type' => 'pilihan'
            ],
            [
                'name' => 'Bahasa Indonesia Lanjutan SMK',
                'code' => 'BILSMK',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - Bahasa Indonesia Lanjutan',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 2,
                'type' => 'pilihan'
            ],
            [
                'name' => 'Bahasa Inggris Lanjutan SMK',
                'code' => 'BEILSMK',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - Bahasa Inggris Lanjutan',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 3,
                'type' => 'pilihan'
            ],
            [
                'name' => 'Fisika SMK',
                'code' => 'FSMK',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - Fisika',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 4,
                'type' => 'pilihan'
            ],
            [
                'name' => 'Kimia SMK',
                'code' => 'KSMK',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - Kimia',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 5,
                'type' => 'pilihan'
            ],
            [
                'name' => 'Biologi SMK',
                'code' => 'BSMK',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - Biologi',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 6,
                'type' => 'pilihan'
            ],
            [
                'name' => 'Ekonomi SMK',
                'code' => 'ESMK',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - Ekonomi',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 7,
                'type' => 'pilihan'
            ],
            [
                'name' => 'Sosiologi SMK',
                'code' => 'SSMK',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - Sosiologi',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 8,
                'type' => 'pilihan'
            ],
            [
                'name' => 'Geografi SMK',
                'code' => 'GSMK',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - Geografi',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 9,
                'type' => 'pilihan'
            ],
            [
                'name' => 'Sejarah SMK',
                'code' => 'SSMK2',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - Sejarah',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 10,
                'type' => 'pilihan'
            ],
            [
                'name' => 'Antropologi SMK',
                'code' => 'ASMK',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - Antropologi',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 11,
                'type' => 'pilihan'
            ],
            [
                'name' => 'PPKn/Pendidikan Pancasila SMK',
                'code' => 'PPKNSMK',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - PPKn/Pendidikan Pancasila',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 12,
                'type' => 'pilihan'
            ],
            [
                'name' => 'Bahasa Arab SMK',
                'code' => 'BASMK',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - Bahasa Arab',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 13,
                'type' => 'pilihan'
            ],
            [
                'name' => 'Bahasa Jerman SMK',
                'code' => 'BJSMK',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - Bahasa Jerman',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 14,
                'type' => 'pilihan'
            ],
            [
                'name' => 'Bahasa Prancis SMK',
                'code' => 'BPSMK',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - Bahasa Prancis',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 15,
                'type' => 'pilihan'
            ],
            [
                'name' => 'Bahasa Jepang SMK',
                'code' => 'BJPSMK',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - Bahasa Jepang',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 16,
                'type' => 'pilihan'
            ],
            [
                'name' => 'Bahasa Korea SMK',
                'code' => 'BKSMK',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - Bahasa Korea',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 17,
                'type' => 'pilihan'
            ],
            [
                'name' => 'Bahasa Mandarin SMK',
                'code' => 'BMSMK',
                'description' => 'Mata pelajaran pilihan kedua untuk SMK/MAK - Bahasa Mandarin',
                'is_required' => false,
                'is_active' => true,
                'education_level' => 'SMK',
                'subject_type' => 'Pilihan Kedua',
                'subject_number' => 18,
                'type' => 'pilihan'
            ]
        ];

        $addedCount = 0;
        $skippedCount = 0;

        foreach ($smkMakSubjects as $subjectData) {
            // Check if subject already exists
            $existing = DB::table('subjects')
                ->where('name', $subjectData['name'])
                ->first();

            if (!$existing) {
                DB::table('subjects')->insert(array_merge($subjectData, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
                
                echo "✅ Added: {$subjectData['name']} ({$subjectData['subject_type']})\n";
                $addedCount++;
            } else {
                echo "⚠️ Already exists: {$subjectData['name']}\n";
                $skippedCount++;
            }
        }

        echo "\n🎉 SMK/MAK subjects addition completed!\n";
        echo "📊 Subjects added: {$addedCount}\n";
        echo "📊 Subjects skipped: {$skippedCount}\n";
        echo "📋 All subjects follow Pusmendik reference for SMK/MAK\n";
        echo "\n📚 SMK/MAK Subject Structure:\n";
        echo "  🥇 Pilihan Pertama: Produk/Projek Kreatif dan Kewirausahaan (19)\n";
        echo "  🥈 Pilihan Kedua: Mata pelajaran pilihan 1-18\n";
    }
}
