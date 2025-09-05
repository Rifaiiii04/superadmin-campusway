<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateSubjectsPusmendikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🔄 Starting Subjects Update to Pusmendik Standard...\n";

        // Clear existing subjects
        DB::table('subjects')->truncate();
        echo "✅ Cleared existing subjects\n";

        // Insert new subjects according to Pusmendik standard
        $subjects = [
            // Mata Pelajaran Wajib (3 mata pelajaran)
            [
                'subject_number' => 1,
                'code' => 'MTK_L',
                'name' => 'Matematika Lanjutan',
                'type' => 'wajib',
                'is_required' => true,
                'is_active' => true,
                'description' => 'Mata pelajaran matematika tingkat lanjut untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject_number' => 2,
                'code' => 'BIN_L',
                'name' => 'Bahasa Indonesia Lanjutan',
                'type' => 'wajib',
                'is_required' => true,
                'is_active' => true,
                'description' => 'Mata pelajaran bahasa Indonesia tingkat lanjut untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject_number' => 3,
                'code' => 'BIG_L',
                'name' => 'Bahasa Inggris Lanjutan',
                'type' => 'wajib',
                'is_required' => true,
                'is_active' => true,
                'description' => 'Mata pelajaran bahasa Inggris tingkat lanjut untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Mata Pelajaran Pilihan (16 mata pelajaran)
            [
                'subject_number' => 4,
                'code' => 'FIS',
                'name' => 'Fisika',
                'type' => 'pilihan',
                'is_required' => false,
                'is_active' => true,
                'description' => 'Mata pelajaran fisika untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject_number' => 5,
                'code' => 'KIM',
                'name' => 'Kimia',
                'type' => 'pilihan',
                'is_required' => false,
                'is_active' => true,
                'description' => 'Mata pelajaran kimia untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject_number' => 6,
                'code' => 'BIO',
                'name' => 'Biologi',
                'type' => 'pilihan',
                'is_required' => false,
                'is_active' => true,
                'description' => 'Mata pelajaran biologi untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject_number' => 7,
                'code' => 'EKO',
                'name' => 'Ekonomi',
                'type' => 'pilihan',
                'is_required' => false,
                'is_active' => true,
                'description' => 'Mata pelajaran ekonomi untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject_number' => 8,
                'code' => 'SOS',
                'name' => 'Sosiologi',
                'type' => 'pilihan',
                'is_required' => false,
                'is_active' => true,
                'description' => 'Mata pelajaran sosiologi untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject_number' => 9,
                'code' => 'GEO',
                'name' => 'Geografi',
                'type' => 'pilihan',
                'is_required' => false,
                'is_active' => true,
                'description' => 'Mata pelajaran geografi untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject_number' => 10,
                'code' => 'SEJ',
                'name' => 'Sejarah',
                'type' => 'pilihan',
                'is_required' => false,
                'is_active' => true,
                'description' => 'Mata pelajaran sejarah untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject_number' => 11,
                'code' => 'ANT',
                'name' => 'Antropologi',
                'type' => 'pilihan',
                'is_required' => false,
                'is_active' => true,
                'description' => 'Mata pelajaran antropologi untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject_number' => 12,
                'code' => 'PPKN',
                'name' => 'PPKn/Pendidikan Pancasila',
                'type' => 'pilihan',
                'is_required' => false,
                'is_active' => true,
                'description' => 'Mata pelajaran PPKn/Pendidikan Pancasila untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject_number' => 13,
                'code' => 'BAR',
                'name' => 'Bahasa Arab',
                'type' => 'pilihan',
                'is_required' => false,
                'is_active' => true,
                'description' => 'Mata pelajaran bahasa Arab untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject_number' => 14,
                'code' => 'BJE',
                'name' => 'Bahasa Jerman',
                'type' => 'pilihan',
                'is_required' => false,
                'is_active' => true,
                'description' => 'Mata pelajaran bahasa Jerman untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject_number' => 15,
                'code' => 'BPR',
                'name' => 'Bahasa Prancis',
                'type' => 'pilihan',
                'is_required' => false,
                'is_active' => true,
                'description' => 'Mata pelajaran bahasa Prancis untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject_number' => 16,
                'code' => 'BJP',
                'name' => 'Bahasa Jepang',
                'type' => 'pilihan',
                'is_required' => false,
                'is_active' => true,
                'description' => 'Mata pelajaran bahasa Jepang untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject_number' => 17,
                'code' => 'BKO',
                'name' => 'Bahasa Korea',
                'type' => 'pilihan',
                'is_required' => false,
                'is_active' => true,
                'description' => 'Mata pelajaran bahasa Korea untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject_number' => 18,
                'code' => 'BMA',
                'name' => 'Bahasa Mandarin',
                'type' => 'pilihan',
                'is_required' => false,
                'is_active' => true,
                'description' => 'Mata pelajaran bahasa Mandarin untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject_number' => 19,
                'code' => 'PKK',
                'name' => 'Produk/Projek Kreatif dan Kewirausahaan',
                'type' => 'pilihan',
                'is_required' => false,
                'is_active' => true,
                'description' => 'Mata pelajaran produk/projek kreatif dan kewirausahaan untuk persiapan ujian masuk perguruan tinggi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert subjects
        DB::table('subjects')->insert($subjects);
        echo "✅ Inserted " . count($subjects) . " subjects\n";

        // Show summary
        $wajibCount = DB::table('subjects')->where('type', 'wajib')->count();
        $pilihanCount = DB::table('subjects')->where('type', 'pilihan')->count();
        
        echo "\n📊 Subjects Summary:\n";
        echo "  - Mata Pelajaran Wajib: {$wajibCount}\n";
        echo "  - Mata Pelajaran Pilihan: {$pilihanCount}\n";
        echo "  - Total: " . ($wajibCount + $pilihanCount) . " subjects\n";

        echo "\n🎉 Subjects Update Completed Successfully!\n";
        echo "✅ All subjects now follow Pusmendik standard (19 subjects)\n";
    }
}
