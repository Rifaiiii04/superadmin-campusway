<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📚 Memperbaiki mata pelajaran wajib...\n";

// Cek data yang ada
$existingSubjects = DB::table('subjects')
    ->where('name', 'Bahasa Indonesia')
    ->get();

echo "Ditemukan " . $existingSubjects->count() . " mata pelajaran Bahasa Indonesia:\n";
foreach ($existingSubjects as $subject) {
    echo "- ID: {$subject->id}, Level: {$subject->education_level}, Type: {$subject->subject_type}\n";
}

// Update mata pelajaran yang sudah ada
echo "\n🔄 Memperbarui mata pelajaran yang sudah ada...\n";

// Update untuk SMA/MA
DB::table('subjects')
    ->where('name', 'Bahasa Indonesia')
    ->where('education_level', 'SMA/MA')
    ->update([
        'subject_type' => 'Wajib',
        'subject_number' => 1,
        'is_required' => true,
        'updated_at' => now()
    ]);

DB::table('subjects')
    ->where('name', 'Bahasa Inggris')
    ->where('education_level', 'SMA/MA')
    ->update([
        'subject_type' => 'Wajib',
        'subject_number' => 2,
        'is_required' => true,
        'updated_at' => now()
    ]);

DB::table('subjects')
    ->where('name', 'Matematika')
    ->where('education_level', 'SMA/MA')
    ->update([
        'subject_type' => 'Wajib',
        'subject_number' => 3,
        'is_required' => true,
        'updated_at' => now()
    ]);

// Update untuk SMK/MAK
DB::table('subjects')
    ->where('name', 'Bahasa Indonesia')
    ->where('education_level', 'SMK/MAK')
    ->update([
        'subject_type' => 'Wajib',
        'subject_number' => 1,
        'is_required' => true,
        'updated_at' => now()
    ]);

DB::table('subjects')
    ->where('name', 'Bahasa Inggris')
    ->where('education_level', 'SMK/MAK')
    ->update([
        'subject_type' => 'Wajib',
        'subject_number' => 2,
        'is_required' => true,
        'updated_at' => now()
    ]);

DB::table('subjects')
    ->where('name', 'Matematika')
    ->where('education_level', 'SMK/MAK')
    ->update([
        'subject_type' => 'Wajib',
        'subject_number' => 3,
        'is_required' => true,
        'updated_at' => now()
    ]);

echo "✅ Mata pelajaran wajib diperbarui!\n";

// Cek hasil
$mandatorySubjects = DB::table('subjects')
    ->where('subject_type', 'Wajib')
    ->orderBy('education_level')
    ->orderBy('subject_number')
    ->get();

echo "\n📊 Mata pelajaran wajib setelah update:\n";
foreach ($mandatorySubjects as $subject) {
    echo "- {$subject->name} ({$subject->education_level}) - {$subject->subject_type}\n";
}

echo "\n🎉 Perbaikan selesai!\n";
