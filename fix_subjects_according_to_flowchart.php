<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔄 Fixing subjects according to flowchart...\n\n";

// Clear existing subjects
DB::table('subjects')->truncate();
echo "✅ Cleared existing subjects\n";

// Insert subjects according to flowchart
$subjects = [
    // Mata Uji Wajib (3) - Sesuai flowchart
    [
        'subject_number' => 1,
        'code' => 'BI',
        'name' => 'Bahasa Indonesia',
        'type' => 'wajib',
        'is_required' => true,
        'is_active' => true,
        'description' => 'Mata pelajaran wajib Bahasa Indonesia',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 2,
        'code' => 'BE',
        'name' => 'Bahasa Inggris',
        'type' => 'wajib',
        'is_required' => true,
        'is_active' => true,
        'description' => 'Mata pelajaran wajib Bahasa Inggris',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 3,
        'code' => 'MTK',
        'name' => 'Matematika',
        'type' => 'wajib',
        'is_required' => true,
        'is_active' => true,
        'description' => 'Mata pelajaran wajib Matematika',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    
    // Mata Uji Pilihan (19) - Sesuai flowchart
    [
        'subject_number' => 4,
        'code' => 'MTK_L',
        'name' => 'Matematika lanjutan',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Matematika lanjutan',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 5,
        'code' => 'BI_L',
        'name' => 'Bahasa Indonesia lanjutan',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Bahasa Indonesia lanjutan',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 6,
        'code' => 'BE_L',
        'name' => 'Bahasa Inggris lanjutan',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Bahasa Inggris lanjutan',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 7,
        'code' => 'FIS',
        'name' => 'Fisika',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Fisika',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 8,
        'code' => 'KIM',
        'name' => 'Kimia',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Kimia',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 9,
        'code' => 'BIO',
        'name' => 'Biologi',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Biologi',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 10,
        'code' => 'SOS',
        'name' => 'Sosiologi',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Sosiologi',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 11,
        'code' => 'GEO',
        'name' => 'Geografi',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Geografi',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 12,
        'code' => 'SEJ',
        'name' => 'Sejarah',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Sejarah',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 13,
        'code' => 'ANT',
        'name' => 'Antropologi',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Antropologi',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 14,
        'code' => 'PPKN',
        'name' => 'PPKn/Pendidikan Pancasila',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan PPKn/Pendidikan Pancasila',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 15,
        'code' => 'BAR',
        'name' => 'Bahasa Arab',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Bahasa Arab',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 16,
        'code' => 'BPR',
        'name' => 'Bahasa Prancis',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Bahasa Prancis',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 17,
        'code' => 'BJP',
        'name' => 'Bahasa Jepang',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Bahasa Jepang',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 18,
        'code' => 'BKO',
        'name' => 'Bahasa Korea',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Bahasa Korea',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 19,
        'code' => 'BMA',
        'name' => 'Bahasa Mandarin',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Bahasa Mandarin',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 20,
        'code' => 'PPKK',
        'name' => 'Produk/Projek Kreatif dan Kewirausahaan',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Produk/Projek Kreatif dan Kewirausahaan (SMK/MAK)',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 21,
        'code' => 'EKO',
        'name' => 'Ekonomi',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Ekonomi',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'subject_number' => 22,
        'code' => 'BJE',
        'name' => 'Bahasa Jerman',
        'type' => 'pilihan',
        'is_required' => false,
        'is_active' => true,
        'description' => 'Mata pelajaran pilihan Bahasa Jerman',
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

// Insert subjects
DB::table('subjects')->insert($subjects);
echo "✅ Inserted " . count($subjects) . " subjects\n";

// Verify
$wajib = DB::table('subjects')->where('type', 'wajib')->count();
$pilihan = DB::table('subjects')->where('type', 'pilihan')->count();
$total = DB::table('subjects')->count();

echo "\n📊 Verification:\n";
echo "  - Total subjects: {$total}\n";
echo "  - Wajib (3): {$wajib}\n";
echo "  - Pilihan (19): {$pilihan}\n";

echo "\n📖 Mata Uji Wajib (3):\n";
$wajibSubjects = DB::table('subjects')->where('type', 'wajib')->orderBy('subject_number')->get();
foreach ($wajibSubjects as $subject) {
    echo "  {$subject->subject_number}. {$subject->name} ({$subject->code})\n";
}

echo "\n📚 Mata Uji Pilihan (19):\n";
$pilihanSubjects = DB::table('subjects')->where('type', 'pilihan')->orderBy('subject_number')->get();
foreach ($pilihanSubjects as $subject) {
    echo "  {$subject->subject_number}. {$subject->name} ({$subject->code})\n";
}

echo "\n✅ Subjects fixed according to flowchart!\n";
