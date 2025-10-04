<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CorrectRumpunIlmuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update categories sesuai dengan Kepmendikdasmen No. 95/M/2025
        // Hanya ada 5 rumpun ilmu: Humaniora, Ilmu Sosial, Ilmu Alam, Ilmu Formal, Ilmu Terapan
        
        // HUMANIORA (1-5)
        $humaniora = [
            'Seni', 'Sejarah', 'Linguistik', 'Susastra atau Sastra', 'Filsafat', 'Studi Humanitas'
        ];
        DB::table('major_recommendations')->whereIn('major_name', $humaniora)->update(['category' => 'Humaniora']);
        
        // ILMU SOSIAL (6-9, 26-32, 46-48)
        $ilmuSosial = [
            'Sosial', 'Ekonomi', 'Pertahanan', 'Psikologi',
            'Ilmu atau Sains Akuntansi', 'Ilmu atau Sains Manajemen', 'Logistik', 'Administrasi Bisnis', 'Bisnis', 'Ilmu atau Sains Komunikasi', 'Pendidikan',
            'Hukum', 'Ilmu atau Sains Militer', 'Urusan Publik'
        ];
        DB::table('major_recommendations')->whereIn('major_name', $ilmuSosial)->update(['category' => 'Ilmu Sosial']);
        
        // ILMU ALAM (10-16)
        $ilmuAlam = [
            'Kimia', 'Ilmu atau Sains Kebumian', 'Ilmu atau Sains Kelautan', 'Biologi', 'Biofisika', 'Fisika', 'Astronomi'
        ];
        DB::table('major_recommendations')->whereIn('major_name', $ilmuAlam)->update(['category' => 'Ilmu Alam']);
        
        // ILMU FORMAL (17-19)
        $ilmuFormal = [
            'Komputer', 'Logika', 'Matematika'
        ];
        DB::table('major_recommendations')->whereIn('major_name', $ilmuFormal)->update(['category' => 'Ilmu Formal']);
        
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
        DB::table('major_recommendations')->whereIn('major_name', $ilmuTerapan)->update(['category' => 'Ilmu Terapan']);
        
        echo "Rumpun Ilmu corrected to 5 categories only!\n";
        echo "Humaniora, Ilmu Sosial, Ilmu Alam, Ilmu Formal, Ilmu Terapan\n";
    }
}
