<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Starting rumpun ilmu category fix...\n";
    
    // Update categories sesuai dengan Kepmendikdasmen No. 95/M/2025
    // Hanya ada 5 rumpun ilmu: Humaniora, Ilmu Sosial, Ilmu Alam, Ilmu Formal, Ilmu Terapan
    
    // HUMANIORA (1-5)
    $humaniora = [
        'Seni', 'Sejarah', 'Linguistik', 'Susastra atau Sastra', 'Filsafat', 'Studi Humanitas'
    ];
    $updated = DB::table('major_recommendations')->whereIn('major_name', $humaniora)->update(['category' => 'Humaniora']);
    echo "Updated Humaniora: {$updated} records\n";
    
    // ILMU SOSIAL (6-9, 26-32, 46-48)
    $ilmuSosial = [
        'Sosial', 'Ekonomi', 'Pertahanan', 'Psikologi',
        'Ilmu atau Sains Akuntansi', 'Ilmu atau Sains Manajemen', 'Logistik', 'Administrasi Bisnis', 'Bisnis', 'Ilmu atau Sains Komunikasi', 'Pendidikan',
        'Hukum', 'Ilmu atau Sains Militer', 'Urusan Publik'
    ];
    $updated = DB::table('major_recommendations')->whereIn('major_name', $ilmuSosial)->update(['category' => 'Ilmu Sosial']);
    echo "Updated Ilmu Sosial: {$updated} records\n";
    
    // ILMU ALAM (10-16)
    $ilmuAlam = [
        'Kimia', 'Ilmu atau Sains Kebumian', 'Ilmu atau Sains Kelautan', 'Biologi', 'Biofisika', 'Fisika', 'Astronomi'
    ];
    $updated = DB::table('major_recommendations')->whereIn('major_name', $ilmuAlam)->update(['category' => 'Ilmu Alam']);
    echo "Updated Ilmu Alam: {$updated} records\n";
    
    // ILMU FORMAL (17-19)
    $ilmuFormal = [
        'Komputer', 'Logika', 'Matematika'
    ];
    $updated = DB::table('major_recommendations')->whereIn('major_name', $ilmuFormal)->update(['category' => 'Ilmu Formal']);
    echo "Updated Ilmu Formal: {$updated} records\n";
    
    // ILMU TERAPAN (20-25, 33-45, 49-59) - Semua sisanya masuk ke Ilmu Terapan
    $ilmuTerapan = [
        // Ilmu Terapan (20-25)
        'Ilmu dan Sains Pertanian', 'Peternakan', 'Ilmu atau Sains Perikanan', 'Arsitektur', 'Perencanaan Wilayah', 'Desain',
        // Teknik rekayasa (33-40)
        'Teknik rekayasa', 'Ilmu atau Sains Lingkungan', 'Kehutanan', 'Ilmu atau Sains Kedokteran', 'Ilmu atau Sains Kedokteran Gigi', 'Ilmu atau Sains Veteriner', 'Ilmu Farmasi', 'Ilmu atau Sains Gizi',
        // Kesehatan (41-45)
        'Kesehatan Masyarakat', 'Kebidanan', 'Keperawatan', 'Kesehatan', 'Ilmu atau Sains Informasi',
        // Keolahragaan (49)
        'Ilmu atau Sains Keolahragaan',
        // Lingkungan & Teknologi (50-58)
        'Pariwisata', 'Transportasi', 'Bioteknologi, Biokewirausahaan, Bioinformatika', 'Geografi, Geografi Lingkungan, Sains Informasi Geografi', 'Informatika Medis atau Informatika Kesehatan', 'Konservasi Biologi, Konservasi Hewan Liar, Konservasi Hewan Liar dan Hutan, Konservasi Hutan, Konservasi Sumber Daya Alam', 'Teknologi Pangan, Teknologi Hasil Pertanian/Peternakan/Perikanan', 'Sains Data', 'Sains Perkopian'
    ];
    $updated = DB::table('major_recommendations')->whereIn('major_name', $ilmuTerapan)->update(['category' => 'Ilmu Terapan']);
    echo "Updated Ilmu Terapan: {$updated} records\n";
    
    // Update remaining records that might still have old categories
    $updated = DB::table('major_recommendations')
        ->where('category', 'Soshum')
        ->update(['category' => 'Ilmu Sosial']);
    echo "Updated remaining Soshum to Ilmu Sosial: {$updated} records\n";
    
    $updated = DB::table('major_recommendations')
        ->where('category', 'Saintek')
        ->update(['category' => 'Ilmu Terapan']);
    echo "Updated remaining Saintek to Ilmu Terapan: {$updated} records\n";
    
    // Show final categories
    $categories = DB::table('major_recommendations')
        ->select('category', DB::raw('count(*) as count'))
        ->groupBy('category')
        ->get();
    
    echo "\nFinal categories in database:\n";
    foreach ($categories as $category) {
        echo "- {$category->category}: {$category->count} records\n";
    }
    
    echo "\nRumpun Ilmu corrected to 5 categories only!\n";
    echo "Humaniora, Ilmu Sosial, Ilmu Alam, Ilmu Formal, Ilmu Terapan\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
