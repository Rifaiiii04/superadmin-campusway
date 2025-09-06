<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Analyzing and fixing majors according to Pusmendik table...\n\n";

// Valid majors from Pusmendik table
$validMajors = [
    // HUMANIORA
    'Seni', 'Sejarah', 'Linguistik', 'Sastra', 'Filsafat',
    
    // ILMU SOSIAL  
    'Sosial', 'Ekonomi', 'Pertahanan', 'Psikologi',
    
    // ILMU ALAM
    'Kimia', 'Ilmu Kebumian', 'Ilmu Kelautan', 'Biologi', 'Biofisika', 'Fisika', 'Astronomi',
    
    // ILMU FORMAL
    'Komputer', 'Logika', 'Matematika',
    
    // ILMU TERAPAN
    'Ilmu Pertanian', 'Peternakan', 'Ilmu Perikanan', 'Arsitektur', 'Perencanaan Wilayah', 'Desain',
    'Ilmu Akuntansi', 'Ilmu Manajemen', 'Logistik', 'Administrasi Bisnis', 'Bisnis', 'Ilmu Komunikasi',
    'Pendidikan', 'Teknik Rekayasa', 'Ilmu Lingkungan', 'Kehutanan', 'Ilmu Kedokteran', 'Ilmu Kedokteran Gigi',
    'Ilmu Veteriner', 'Ilmu Farmasi', 'Ilmu Gizi', 'Kesehatan Masyarakat', 'Kebidanan', 'Keperawatan',
    'Kesehatan', 'Ilmu Informasi', 'Hukum', 'Ilmu Militer', 'Urusan Publik', 'Ilmu Keolahragaan',
    'Pariwisata', 'Transportasi', 'Bioteknologi', 'Geografi', 'Informatika Medis', 'Konservasi Biologi',
    'Teknologi Pangan', 'Sains Data', 'Sains Perkopian', 'Studi Humanitas'
];

// Get current majors from database
$currentMajors = DB::table('program_studi')->get();

echo "📊 Current majors in database: " . $currentMajors->count() . "\n";
echo "📋 Valid majors from Pusmendik: " . count($validMajors) . "\n\n";

$toDelete = [];
$toUpdate = [];
$valid = [];

foreach ($currentMajors as $major) {
    $name = $major->name;
    
    // Check if exact match exists
    if (in_array($name, $validMajors)) {
        $valid[] = $name;
        echo "✅ Valid: {$name}\n";
    } else {
        // Check for partial matches or similar names
        $found = false;
        $suggested = '';
        
        foreach ($validMajors as $validMajor) {
            if (stripos($name, $validMajor) !== false || stripos($validMajor, $name) !== false) {
                $found = true;
                $suggested = $validMajor;
                break;
            }
        }
        
        if ($found) {
            $toUpdate[] = ['id' => $major->id, 'current' => $name, 'suggested' => $suggested];
            echo "🔄 Update needed: '{$name}' → '{$suggested}'\n";
        } else {
            $toDelete[] = ['id' => $major->id, 'name' => $name];
            echo "❌ Invalid: {$name} (to be deleted)\n";
        }
    }
}

echo "\n📊 Summary:\n";
echo "✅ Valid majors: " . count($valid) . "\n";
echo "🔄 Majors to update: " . count($toUpdate) . "\n";
echo "❌ Majors to delete: " . count($toDelete) . "\n";

// Ask for confirmation
echo "\n🤔 Do you want to proceed with the changes? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim($line) === 'y' || trim($line) === 'Y') {
    echo "\n🚀 Proceeding with changes...\n";
    
    // Delete invalid majors
    if (!empty($toDelete)) {
        echo "\n🗑️ Deleting invalid majors...\n";
        foreach ($toDelete as $major) {
            // First delete related mappings
            DB::table('program_studi_subjects')
                ->where('program_studi_id', $major['id'])
                ->delete();
            
            // Then delete the major
            DB::table('program_studi')
                ->where('id', $major['id'])
                ->delete();
            
            echo "  ❌ Deleted: {$major['name']}\n";
        }
    }
    
    // Update majors with similar names
    if (!empty($toUpdate)) {
        echo "\n🔄 Updating major names...\n";
        foreach ($toUpdate as $major) {
            DB::table('program_studi')
                ->where('id', $major['id'])
                ->update(['name' => $major['suggested']]);
            
            echo "  🔄 Updated: '{$major['current']}' → '{$major['suggested']}'\n";
        }
    }
    
    echo "\n✅ Major cleanup completed!\n";
} else {
    echo "\n❌ Operation cancelled.\n";
}

echo "\n📋 Next steps:\n";
echo "1. Update reference subject system according to new rules\n";
echo "2. Update mappings based on Pusmendik table\n";
echo "3. Test the updated system\n";
