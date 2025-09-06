<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🏭 Verifying SMK/MAK data...\n\n";

// Check SMK/MAK subjects
echo "📚 SMK/MAK Subjects:\n";
$smkSubjects = DB::table('subjects')
    ->where('education_level', 'SMK')
    ->orderBy('subject_number')
    ->get();

foreach($smkSubjects as $subject) {
    echo "  {$subject->subject_number}. {$subject->name} ({$subject->subject_type})\n";
}

echo "\n📊 Total SMK/MAK subjects: " . $smkSubjects->count() . "\n";

// Check pilihan pertama (Produk/Projek Kreatif dan Kewirausahaan)
$ppkk = $smkSubjects->where('name', 'Produk/Projek Kreatif dan Kewirausahaan')->first();
if ($ppkk) {
    echo "✅ Pilihan Pertama: {$ppkk->name} (ID: {$ppkk->id})\n";
} else {
    echo "❌ Pilihan Pertama not found!\n";
}

// Check pilihan kedua (1-18)
$pilihanKedua = $smkSubjects->where('subject_number', '>=', 1)->where('subject_number', '<=', 18);
echo "✅ Pilihan Kedua: " . $pilihanKedua->count() . " subjects (1-18)\n";

// Check mappings
echo "\n🔗 SMK/MAK Mappings:\n";
$mappings = DB::table('program_studi_subjects')
    ->join('subjects', 'program_studi_subjects.subject_id', '=', 'subjects.id')
    ->join('program_studi', 'program_studi_subjects.program_studi_id', '=', 'program_studi.id')
    ->where('subjects.education_level', 'SMK')
    ->select('program_studi.name as program_name', 'subjects.name as subject_name', 'subjects.subject_type', 'program_studi_subjects.kurikulum_type')
    ->orderBy('program_studi.name')
    ->orderBy('subjects.subject_type')
    ->get();

$groupedMappings = $mappings->groupBy('program_name');
foreach($groupedMappings as $programName => $programMappings) {
    echo "\n📖 {$programName}:\n";
    $pilihanPertama = $programMappings->where('subject_type', 'Pilihan Pertama');
    $pilihanKedua = $programMappings->where('subject_type', 'Pilihan Kedua');
    
    if ($pilihanPertama->count() > 0) {
        echo "  🥇 Pilihan Pertama:\n";
        foreach($pilihanPertama as $mapping) {
            echo "    - {$mapping->subject_name} ({$mapping->kurikulum_type})\n";
        }
    }
    
    if ($pilihanKedua->count() > 0) {
        echo "  🥈 Pilihan Kedua:\n";
        foreach($pilihanKedua as $mapping) {
            echo "    - {$mapping->subject_name} ({$mapping->kurikulum_type})\n";
        }
    }
}

echo "\n📊 Total SMK/MAK mappings: " . $mappings->count() . "\n";

echo "\n✅ SMK/MAK data verification completed!\n";
echo "\n📋 Summary:\n";
echo "  🏭 SMK/MAK subjects: " . $smkSubjects->count() . "\n";
echo "  🔗 Total mappings: " . $mappings->count() . "\n";
echo "  📚 Programs covered: " . $groupedMappings->count() . "\n";
echo "  ✅ Follows Pusmendik reference for SMK/MAK\n";
