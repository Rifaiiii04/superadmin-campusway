<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixMandatorySubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Memperbaiki mata pelajaran wajib sesuai ketentuan:
     * - SMA/MA: Bahasa Indonesia, Bahasa Inggris, Matematika (3 wajib)
     * - SMK/MAK: Bahasa Indonesia, Bahasa Inggris, Matematika (3 wajib)
     */
    public function run(): void
    {
        echo "📚 Memperbaiki mata pelajaran wajib sesuai ketentuan...\n";

        // Check if subjects table exists
        if (!Schema::hasTable('subjects')) {
            echo "❌ Table subjects not found!\n";
            return;
        }

        // Hapus mata pelajaran wajib yang salah
        DB::table('subjects')
            ->where('subject_type', 'Wajib')
            ->whereIn('name', [
                'Matematika Lanjutan',
                'Bahasa Indonesia Lanjutan', 
                'Bahasa Inggris Lanjutan',
                'Matematika Lanjutan SMK',
                'Bahasa Indonesia Lanjutan SMK',
                'Bahasa Inggris Lanjutan SMK'
            ])
            ->delete();

        echo "🧹 Menghapus mata pelajaran wajib yang salah\n";

        // Mata pelajaran wajib yang benar untuk SMA/MA
        $smaMandatorySubjects = [
            [
                'name' => 'Bahasa Indonesia',
                'code' => 'BI',
                'description' => 'Mata pelajaran wajib - Bahasa Indonesia',
                'education_level' => 'SMA/MA',
                'subject_type' => 'Wajib',
                'subject_number' => 1,
                'is_required' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Bahasa Inggris',
                'code' => 'BE',
                'description' => 'Mata pelajaran wajib - Bahasa Inggris',
                'education_level' => 'SMA/MA',
                'subject_type' => 'Wajib',
                'subject_number' => 2,
                'is_required' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Matematika',
                'code' => 'MTK',
                'description' => 'Mata pelajaran wajib - Matematika',
                'education_level' => 'SMA/MA',
                'subject_type' => 'Wajib',
                'subject_number' => 3,
                'is_required' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Mata pelajaran wajib yang benar untuk SMK/MAK
        $smkMandatorySubjects = [
            [
                'name' => 'Bahasa Indonesia',
                'code' => 'BI',
                'description' => 'Mata pelajaran wajib - Bahasa Indonesia',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Wajib',
                'subject_number' => 1,
                'is_required' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Bahasa Inggris',
                'code' => 'BE',
                'description' => 'Mata pelajaran wajib - Bahasa Inggris',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Wajib',
                'subject_number' => 2,
                'is_required' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Matematika',
                'code' => 'MTK',
                'description' => 'Mata pelajaran wajib - Matematika',
                'education_level' => 'SMK/MAK',
                'subject_type' => 'Wajib',
                'subject_number' => 3,
                'is_required' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Insert mata pelajaran wajib SMA/MA
        foreach ($smaMandatorySubjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                [
                    'name' => $subject['name'],
                    'education_level' => $subject['education_level'],
                    'subject_type' => $subject['subject_type']
                ],
                $subject
            );
        }

        // Insert mata pelajaran wajib SMK/MAK
        foreach ($smkMandatorySubjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                [
                    'name' => $subject['name'],
                    'education_level' => $subject['education_level'],
                    'subject_type' => $subject['subject_type']
                ],
                $subject
            );
        }

        echo "✅ Mata pelajaran wajib SMA/MA: 3 mata pelajaran\n";
        echo "✅ Mata pelajaran wajib SMK/MAK: 3 mata pelajaran\n";

        // Pastikan Produk/PKK ada untuk SMK/MAK
        $produkPKK = [
            'name' => 'Produk/Projek Kreatif dan Kewirausahaan',
            'code' => 'PPKK',
            'description' => 'Mata pelajaran pilihan wajib untuk SMK/MAK - Produk/Projek Kreatif dan Kewirausahaan',
            'education_level' => 'SMK/MAK',
            'subject_type' => 'Pilihan_Wajib',
            'subject_number' => 19,
            'is_required' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('subjects')->updateOrInsert(
            [
                'name' => $produkPKK['name'],
                'education_level' => $produkPKK['education_level'],
                'subject_type' => $produkPKK['subject_type']
            ],
            $produkPKK
        );

        echo "✅ Produk/PKK untuk SMK/MAK: Tersedia\n";
        echo "\n🎉 Perbaikan mata pelajaran wajib selesai!\n";
    }
}
