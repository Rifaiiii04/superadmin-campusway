<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing rumpun_ilmu mapping in major_recommendations...\n\n";

// Correct mapping based on Pusmendik table
$correctMapping = [
    // HUMANIORA (5)
    'Seni' => 'HUMANIORA',
    'Sejarah' => 'HUMANIORA', 
    'Linguistik' => 'HUMANIORA',
    'Sastra' => 'HUMANIORA',
    'Filsafat' => 'HUMANIORA',
    
    // ILMU SOSIAL (4)
    'Sosial' => 'ILMU SOSIAL',
    'Ekonomi' => 'ILMU SOSIAL',
    'Pertahanan' => 'ILMU SOSIAL',
    'Psikologi' => 'ILMU SOSIAL',
    
    // ILMU ALAM (7)
    'Kimia' => 'ILMU ALAM',
    'Ilmu Kebumian' => 'ILMU ALAM',
    'Ilmu Kelautan' => 'ILMU ALAM',
    'Biologi' => 'ILMU ALAM',
    'Biofisika' => 'ILMU ALAM',
    'Fisika' => 'ILMU ALAM',
    'Astronomi' => 'ILMU ALAM',
    
    // ILMU FORMAL (3)
    'Komputer' => 'ILMU FORMAL',
    'Logika' => 'ILMU FORMAL',
    'Matematika' => 'ILMU FORMAL',
    
    // ILMU TERAPAN (40) - all others
    'Ilmu Pertanian' => 'ILMU TERAPAN',
    'Peternakan' => 'ILMU TERAPAN',
    'Ilmu Perikanan' => 'ILMU TERAPAN',
    'Arsitektur' => 'ILMU TERAPAN',
    'Perencanaan Wilayah' => 'ILMU TERAPAN',
    'Desain' => 'ILMU TERAPAN',
    'Ilmu Akuntansi' => 'ILMU TERAPAN',
    'Ilmu Manajemen' => 'ILMU TERAPAN',
    'Logistik' => 'ILMU TERAPAN',
    'Administrasi Bisnis' => 'ILMU TERAPAN',
    'Bisnis' => 'ILMU TERAPAN',
    'Ilmu Komunikasi' => 'ILMU TERAPAN',
    'Pendidikan' => 'ILMU TERAPAN',
    'Teknik Rekayasa' => 'ILMU TERAPAN',
    'Ilmu Lingkungan' => 'ILMU TERAPAN',
    'Kehutanan' => 'ILMU TERAPAN',
    'Ilmu Kedokteran' => 'ILMU TERAPAN',
    'Ilmu Kedokteran Gigi' => 'ILMU TERAPAN',
    'Ilmu Veteriner' => 'ILMU TERAPAN',
    'Ilmu Farmasi' => 'ILMU TERAPAN',
    'Ilmu Gizi' => 'ILMU TERAPAN',
    'Kesehatan Masyarakat' => 'ILMU TERAPAN',
    'Kebidanan' => 'ILMU TERAPAN',
    'Keperawatan' => 'ILMU TERAPAN',
    'Kesehatan' => 'ILMU TERAPAN',
    'Ilmu Informasi' => 'ILMU TERAPAN',
    'Hukum' => 'ILMU TERAPAN',
    'Ilmu Militer' => 'ILMU TERAPAN',
    'Urusan Publik' => 'ILMU TERAPAN',
    'Ilmu Keolahragaan' => 'ILMU TERAPAN',
    'Pariwisata' => 'ILMU TERAPAN',
    'Transportasi' => 'ILMU TERAPAN',
    'Bioteknologi' => 'ILMU TERAPAN',
    'Geografi' => 'ILMU TERAPAN',
    'Informatika Medis' => 'ILMU TERAPAN',
    'Konservasi Biologi' => 'ILMU TERAPAN',
    'Teknologi Pangan' => 'ILMU TERAPAN',
    'Sains Data' => 'ILMU TERAPAN',
    'Sains Perkopian' => 'ILMU TERAPAN',
    'Studi Humanitas' => 'ILMU TERAPAN'
];

// Update all major_recommendations with correct rumpun_ilmu
$updatedCount = 0;
foreach ($correctMapping as $majorName => $correctRumpun) {
    $result = DB::table('major_recommendations')
        ->where('major_name', $majorName)
        ->update(['rumpun_ilmu' => $correctRumpun]);
    
    if ($result > 0) {
        echo "✅ Updated: {$majorName} → {$correctRumpun}\n";
        $updatedCount++;
    }
}

echo "\n📊 Updated {$updatedCount} recommendations\n";

// Final count by rumpun ilmu
$rumpunCounts = DB::table('major_recommendations')
    ->select('rumpun_ilmu', DB::raw('count(*) as count'))
    ->groupBy('rumpun_ilmu')
    ->orderBy('rumpun_ilmu')
    ->get();

echo "\n📚 Final count by Rumpun Ilmu:\n";
foreach ($rumpunCounts as $rumpun) {
    echo "  - {$rumpun->rumpun_ilmu}: {$rumpun->count} recommendations\n";
}

$totalCount = DB::table('major_recommendations')->count();
echo "\n📊 Total recommendations: {$totalCount}\n";

echo "\n📋 Expected final counts:\n";
echo "  - HUMANIORA: 5\n";
echo "  - ILMU SOSIAL: 4\n";
echo "  - ILMU ALAM: 7\n";
echo "  - ILMU FORMAL: 3\n";
echo "  - ILMU TERAPAN: 40\n";
echo "  - Total: 59\n";
