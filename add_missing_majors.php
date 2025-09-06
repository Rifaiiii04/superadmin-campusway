<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "➕ Adding missing majors from Pusmendik table...\n\n";

// Get HUMANIORA rumpun_ilmu ID
$humanioraRumpun = DB::table('rumpun_ilmu')->where('name', 'HUMANIORA')->first();
$sosialRumpun = DB::table('rumpun_ilmu')->where('name', 'ILMU SOSIAL')->first();
$alamRumpun = DB::table('rumpun_ilmu')->where('name', 'ILMU ALAM')->first();
$formalRumpun = DB::table('rumpun_ilmu')->where('name', 'ILMU FORMAL')->first();
$terapanRumpun = DB::table('rumpun_ilmu')->where('name', 'ILMU TERAPAN')->first();

if (!$humanioraRumpun || !$sosialRumpun || !$alamRumpun || !$formalRumpun || !$terapanRumpun) {
    echo "❌ Rumpun ilmu not found!\n";
    return;
}

// Missing majors from Pusmendik table
$missingMajors = [
    // ILMU TERAPAN - Major missing ones
    ['name' => 'Peternakan', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari peternakan dan hewan ternak'],
    ['name' => 'Ilmu Perikanan', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari ilmu perikanan dan kelautan'],
    ['name' => 'Arsitektur', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari arsitektur dan desain bangunan'],
    ['name' => 'Perencanaan Wilayah', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari perencanaan wilayah dan tata kota'],
    ['name' => 'Desain', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari desain dan seni terapan'],
    ['name' => 'Ilmu Akuntansi', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari akuntansi dan keuangan'],
    ['name' => 'Ilmu Manajemen', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari manajemen dan administrasi'],
    ['name' => 'Logistik', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari logistik dan supply chain'],
    ['name' => 'Administrasi Bisnis', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari administrasi bisnis'],
    ['name' => 'Bisnis', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari bisnis dan kewirausahaan'],
    ['name' => 'Ilmu Komunikasi', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari komunikasi dan media'],
    ['name' => 'Pendidikan', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari pendidikan dan pengajaran'],
    ['name' => 'Ilmu Lingkungan', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari ilmu lingkungan dan konservasi'],
    ['name' => 'Kehutanan', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari kehutanan dan konservasi hutan'],
    ['name' => 'Ilmu Kedokteran Gigi', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari kedokteran gigi'],
    ['name' => 'Ilmu Veteriner', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari kedokteran hewan'],
    ['name' => 'Ilmu Farmasi', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari farmasi dan obat-obatan'],
    ['name' => 'Ilmu Gizi', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari gizi dan nutrisi'],
    ['name' => 'Kesehatan Masyarakat', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari kesehatan masyarakat'],
    ['name' => 'Kebidanan', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari kebidanan'],
    ['name' => 'Keperawatan', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari keperawatan'],
    ['name' => 'Kesehatan', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari kesehatan umum'],
    ['name' => 'Ilmu Informasi', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari ilmu informasi'],
    ['name' => 'Hukum', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari hukum dan perundang-undangan'],
    ['name' => 'Ilmu Militer', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari ilmu militer'],
    ['name' => 'Urusan Publik', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari urusan publik dan administrasi negara'],
    ['name' => 'Ilmu Keolahragaan', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari ilmu keolahragaan'],
    ['name' => 'Pariwisata', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari pariwisata'],
    ['name' => 'Transportasi', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari transportasi'],
    ['name' => 'Bioteknologi', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari bioteknologi'],
    ['name' => 'Geografi', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari geografi'],
    ['name' => 'Informatika Medis', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari informatika medis'],
    ['name' => 'Konservasi Biologi', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari konservasi biologi'],
    ['name' => 'Teknologi Pangan', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari teknologi pangan'],
    ['name' => 'Sains Data', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari sains data'],
    ['name' => 'Sains Perkopian', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari sains perkopian'],
    ['name' => 'Studi Humanitas', 'rumpun_ilmu_id' => $terapanRumpun->id, 'description' => 'Program studi yang mempelajari studi humanitas'],
    
    // ILMU FORMAL - Missing ones
    ['name' => 'Komputer', 'rumpun_ilmu_id' => $formalRumpun->id, 'description' => 'Program studi yang mempelajari ilmu komputer'],
];

$addedCount = 0;
$skippedCount = 0;

foreach ($missingMajors as $majorData) {
    // Check if major already exists
    $existing = DB::table('program_studi')
        ->where('name', $majorData['name'])
        ->first();

    if (!$existing) {
        DB::table('program_studi')->insert(array_merge($majorData, [
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]));
        
        echo "✅ Added: {$majorData['name']}\n";
        $addedCount++;
    } else {
        echo "⚠️ Already exists: {$majorData['name']}\n";
        $skippedCount++;
    }
}

echo "\n🎉 Missing majors addition completed!\n";
echo "📊 Majors added: {$addedCount}\n";
echo "📊 Majors skipped: {$skippedCount}\n";
echo "📋 All majors follow Pusmendik table reference\n";
