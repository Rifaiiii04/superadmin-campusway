<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update categories sesuai dengan Kepmendikdasmen No. 95/M/2025
        
        // HUMANIORA (1-5)
        $humaniora = [
            'Seni', 'Sejarah', 'Linguistik', 'Susastra atau Sastra', 'Filsafat', 'Studi Humanitas'
        ];
        DB::table('major_recommendations')->whereIn('major_name', $humaniora)->update(['category' => 'Humaniora']);
        
        // ILMU SOSIAL (6-9, 46-48)
        $ilmuSosial = [
            'Sosial', 'Ekonomi', 'Pertahanan', 'Psikologi', 'Hukum', 'Ilmu atau Sains Militer', 'Urusan Publik'
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
        
        // ILMU TERAPAN (20-33)
        $ilmuTerapan = [
            'Ilmu dan Sains Pertanian', 'Peternakan', 'Ilmu atau Sains Perikanan', 'Arsitektur', 
            'Perencanaan Wilayah', 'Desain', 'Ilmu atau Sains Akuntansi', 'Ilmu atau Sains Manajemen', 
            'Logistik', 'Administrasi Bisnis', 'Bisnis', 'Ilmu atau Sains Komunikasi', 'Pendidikan', 'Teknik rekayasa'
        ];
        DB::table('major_recommendations')->whereIn('major_name', $ilmuTerapan)->update(['category' => 'Ilmu Terapan']);
        
        // ILMU KESEHATAN (36-40, 41-44, 49)
        $ilmuKesehatan = [
            'Ilmu atau Sains Kedokteran', 'Ilmu atau Sains Kedokteran Gigi', 'Ilmu atau Sains Veteriner', 
            'Ilmu Farmasi', 'Ilmu atau Sains Gizi', 'Kesehatan Masyarakat', 'Kebidanan', 'Keperawatan', 
            'Kesehatan', 'Ilmu atau Sains Keolahragaan'
        ];
        DB::table('major_recommendations')->whereIn('major_name', $ilmuKesehatan)->update(['category' => 'Ilmu Kesehatan']);
        
        // ILMU LINGKUNGAN (34-35, 50, 52-56, 58)
        $ilmuLingkungan = [
            'Ilmu atau Sains Lingkungan', 'Kehutanan', 'Pariwisata', 
            'Bioteknologi, Biokewirausahaan, Bioinformatika', 
            'Geografi, Geografi Lingkungan, Sains Informasi Geografi',
            'Konservasi Biologi, Konservasi Hewan Liar, Konservasi Hewan Liar dan Hutan, Konservasi Hutan, Konservasi Sumber Daya Alam',
            'Teknologi Pangan, Teknologi Hasil Pertanian/Peternakan/Perikanan', 'Sains Perkopian'
        ];
        DB::table('major_recommendations')->whereIn('major_name', $ilmuLingkungan)->update(['category' => 'Ilmu Lingkungan']);
        
        // ILMU TEKNOLOGI (45, 51, 54, 57)
        $ilmuTeknologi = [
            'Ilmu atau Sains Informasi', 'Transportasi', 
            'Informatika Medis atau Informatika Kesehatan', 'Sains Data'
        ];
        DB::table('major_recommendations')->whereIn('major_name', $ilmuTeknologi)->update(['category' => 'Ilmu Teknologi']);
        
        echo "Categories updated successfully!\n";
    }
}
