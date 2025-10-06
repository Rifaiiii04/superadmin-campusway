<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking and fixing majors to match Pusmendik table exactly...\n\n";

// Exact list from Pusmendik table (59 majors)
$validMajorsFromPusmendik = [
    // HUMANIORA (5)
    'Seni', 'Sejarah', 'Linguistik', 'Sastra', 'Filsafat',
    
    // ILMU SOSIAL (4)
    'Sosial', 'Ekonomi', 'Pertahanan', 'Psikologi',
    
    // ILMU ALAM (7)
    'Kimia', 'Ilmu Kebumian', 'Ilmu Kelautan', 'Biologi', 'Biofisika', 'Fisika', 'Astronomi',
    
    // ILMU FORMAL (3)
    'Komputer', 'Logika', 'Matematika',
    
    // ILMU TERAPAN (40)
    'Ilmu Pertanian', 'Peternakan', 'Ilmu Perikanan', 'Arsitektur', 'Perencanaan Wilayah', 'Desain',
    'Ilmu Akuntansi', 'Ilmu Manajemen', 'Logistik', 'Administrasi Bisnis', 'Bisnis', 'Ilmu Komunikasi',
    'Pendidikan', 'Teknik Rekayasa', 'Ilmu Lingkungan', 'Kehutanan', 'Ilmu Kedokteran', 'Ilmu Kedokteran Gigi',
    'Ilmu Veteriner', 'Ilmu Farmasi', 'Ilmu Gizi', 'Kesehatan Masyarakat', 'Kebidanan', 'Keperawatan',
    'Kesehatan', 'Ilmu Informasi', 'Hukum', 'Ilmu Militer', 'Urusan Publik', 'Ilmu Keolahragaan',
    'Pariwisata', 'Transportasi', 'Bioteknologi', 'Geografi', 'Informatika Medis', 'Konservasi Biologi',
    'Teknologi Pangan', 'Sains Data', 'Sains Perkopian', 'Studi Humanitas'
];

echo "📋 Valid majors from Pusmendik: " . count($validMajorsFromPusmendik) . "\n";

// Get current majors from database
$currentMajors = DB::table('program_studi')->get();
echo "📊 Current majors in database: " . $currentMajors->count() . "\n\n";

// Check which ones are valid and which are invalid
$validMajors = [];
$invalidMajors = [];
$missingMajors = [];

foreach ($currentMajors as $major) {
    $name = $major->name;
    
    if (in_array($name, $validMajorsFromPusmendik)) {
        $validMajors[] = $name;
        echo "✅ Valid: {$name}\n";
    } else {
        $invalidMajors[] = ['id' => $major->id, 'name' => $name];
        echo "❌ Invalid: {$name}\n";
    }
}

// Check which valid majors are missing
foreach ($validMajorsFromPusmendik as $validMajor) {
    if (!in_array($validMajor, $validMajors)) {
        $missingMajors[] = $validMajor;
        echo "➕ Missing: {$validMajor}\n";
    }
}

echo "\n📊 Summary:\n";
echo "✅ Valid majors: " . count($validMajors) . "\n";
echo "❌ Invalid majors: " . count($invalidMajors) . "\n";
echo "➕ Missing majors: " . count($missingMajors) . "\n";

// Ask for confirmation
echo "\n🤔 Do you want to proceed with the cleanup? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim($line) === 'y' || trim($line) === 'Y') {
    echo "\n🚀 Proceeding with cleanup...\n";
    
    // Delete invalid majors
    if (!empty($invalidMajors)) {
        echo "\n🗑️ Deleting invalid majors...\n";
        foreach ($invalidMajors as $major) {
            // First delete related mappings
            DB::table('program_studi_subjects')
                ->where('program_studi_id', $major['id'])
                ->delete();
            
            // Delete from major_recommendations if exists
            DB::table('major_recommendations')
                ->where('major_name', $major['name'])
                ->delete();
            
            // Then delete the major
            DB::table('program_studi')
                ->where('id', $major['id'])
                ->delete();
            
            echo "  ❌ Deleted: {$major['name']}\n";
        }
    }
    
    // Add missing majors
    if (!empty($missingMajors)) {
        echo "\n➕ Adding missing majors...\n";
        
        // Get rumpun ilmu IDs
        $rumpunIlmu = DB::table('rumpun_ilmu')->pluck('id', 'name');
        
        // Define rumpun ilmu for each major
        $majorRumpunMapping = [
            // HUMANIORA
            'Seni' => 'HUMANIORA', 'Sejarah' => 'HUMANIORA', 'Linguistik' => 'HUMANIORA', 'Sastra' => 'HUMANIORA', 'Filsafat' => 'HUMANIORA',
            
            // ILMU SOSIAL
            'Sosial' => 'ILMU SOSIAL', 'Ekonomi' => 'ILMU SOSIAL', 'Pertahanan' => 'ILMU SOSIAL', 'Psikologi' => 'ILMU SOSIAL',
            
            // ILMU ALAM
            'Kimia' => 'ILMU ALAM', 'Ilmu Kebumian' => 'ILMU ALAM', 'Ilmu Kelautan' => 'ILMU ALAM', 'Biologi' => 'ILMU ALAM', 'Biofisika' => 'ILMU ALAM', 'Fisika' => 'ILMU ALAM', 'Astronomi' => 'ILMU ALAM',
            
            // ILMU FORMAL
            'Komputer' => 'ILMU FORMAL', 'Logika' => 'ILMU FORMAL', 'Matematika' => 'ILMU FORMAL',
            
            // ILMU TERAPAN (all others)
            'Ilmu Pertanian' => 'ILMU TERAPAN', 'Peternakan' => 'ILMU TERAPAN', 'Ilmu Perikanan' => 'ILMU TERAPAN', 'Arsitektur' => 'ILMU TERAPAN', 'Perencanaan Wilayah' => 'ILMU TERAPAN', 'Desain' => 'ILMU TERAPAN',
            'Ilmu Akuntansi' => 'ILMU TERAPAN', 'Ilmu Manajemen' => 'ILMU TERAPAN', 'Logistik' => 'ILMU TERAPAN', 'Administrasi Bisnis' => 'ILMU TERAPAN', 'Bisnis' => 'ILMU TERAPAN', 'Ilmu Komunikasi' => 'ILMU TERAPAN',
            'Pendidikan' => 'ILMU TERAPAN', 'Teknik Rekayasa' => 'ILMU TERAPAN', 'Ilmu Lingkungan' => 'ILMU TERAPAN', 'Kehutanan' => 'ILMU TERAPAN', 'Ilmu Kedokteran' => 'ILMU TERAPAN', 'Ilmu Kedokteran Gigi' => 'ILMU TERAPAN',
            'Ilmu Veteriner' => 'ILMU TERAPAN', 'Ilmu Farmasi' => 'ILMU TERAPAN', 'Ilmu Gizi' => 'ILMU TERAPAN', 'Kesehatan Masyarakat' => 'ILMU TERAPAN', 'Kebidanan' => 'ILMU TERAPAN', 'Keperawatan' => 'ILMU TERAPAN',
            'Kesehatan' => 'ILMU TERAPAN', 'Ilmu Informasi' => 'ILMU TERAPAN', 'Hukum' => 'ILMU TERAPAN', 'Ilmu Militer' => 'ILMU TERAPAN', 'Urusan Publik' => 'ILMU TERAPAN', 'Ilmu Keolahragaan' => 'ILMU TERAPAN',
            'Pariwisata' => 'ILMU TERAPAN', 'Transportasi' => 'ILMU TERAPAN', 'Bioteknologi' => 'ILMU TERAPAN', 'Geografi' => 'ILMU TERAPAN', 'Informatika Medis' => 'ILMU TERAPAN', 'Konservasi Biologi' => 'ILMU TERAPAN',
            'Teknologi Pangan' => 'ILMU TERAPAN', 'Sains Data' => 'ILMU TERAPAN', 'Sains Perkopian' => 'ILMU TERAPAN', 'Studi Humanitas' => 'ILMU TERAPAN'
        ];
        
        foreach ($missingMajors as $majorName) {
            $rumpunName = $majorRumpunMapping[$majorName] ?? 'ILMU TERAPAN';
            $rumpunId = $rumpunIlmu[$rumpunName] ?? null;
            
            if ($rumpunId) {
                DB::table('program_studi')->insert([
                    'name' => $majorName,
                    'description' => "Program studi yang mempelajari {$majorName}",
                    'rumpun_ilmu_id' => $rumpunId,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                echo "  ✅ Added: {$majorName} ({$rumpunName})\n";
            } else {
                echo "  ❌ Could not add: {$majorName} (rumpun not found)\n";
            }
        }
    }
    
    echo "\n✅ Cleanup completed!\n";
    
    // Final count
    $finalCount = DB::table('program_studi')->count();
    echo "📊 Final count: {$finalCount} majors\n";
    
    // Count by rumpun ilmu
    $rumpunCounts = DB::table('program_studi')
        ->join('rumpun_ilmu', 'program_studi.rumpun_ilmu_id', '=', 'rumpun_ilmu.id')
        ->select('rumpun_ilmu.name', DB::raw('count(*) as count'))
        ->groupBy('rumpun_ilmu.name')
        ->get();
    
    echo "\n📚 Final count by Rumpun Ilmu:\n";
    foreach ($rumpunCounts as $rumpun) {
        echo "  - {$rumpun->name}: {$rumpun->count} majors\n";
    }
    
} else {
    echo "\n❌ Operation cancelled.\n";
}

echo "\n📋 Expected final counts:\n";
echo "  - HUMANIORA: 5\n";
echo "  - ILMU SOSIAL: 4\n";
echo "  - ILMU ALAM: 7\n";
echo "  - ILMU FORMAL: 3\n";
echo "  - ILMU TERAPAN: 40\n";
echo "  - Total: 59\n";
