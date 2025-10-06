<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MajorRecommendation;

class FixAllCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "=== FIXING ALL CATEGORIES ACCORDING TO KEPMENDIKDASMEN ===\n";

        // Mapping berdasarkan Kepmendikdasmen No. 95/M/2025
        $categoryMappings = [
            // HUMANIORA (6 majors)
            1 => 'Humaniora',   // Seni
            2 => 'Humaniora',   // Sejarah
            3 => 'Humaniora',   // Linguistik
            4 => 'Humaniora',   // Susastra atau Sastra
            5 => 'Humaniora',   // Filsafat
            59 => 'Humaniora',  // Studi Humanitas

            // ILMU SOSIAL (14 majors)
            6 => 'Ilmu Sosial',    // Sosial
            7 => 'Ilmu Sosial',    // Ekonomi
            8 => 'Ilmu Sosial',    // Pertahanan
            9 => 'Ilmu Sosial',    // Psikologi
            26 => 'Ilmu Sosial',   // Ilmu atau Sains Akuntansi
            27 => 'Ilmu Sosial',   // Ilmu atau Sains Manajemen
            28 => 'Ilmu Sosial',   // Logistik
            29 => 'Ilmu Sosial',   // Administrasi Bisnis
            30 => 'Ilmu Sosial',   // Bisnis
            31 => 'Ilmu Sosial',   // Ilmu atau Sains Komunikasi
            32 => 'Ilmu Sosial',   // Pendidikan
            46 => 'Ilmu Sosial',   // Hukum
            47 => 'Ilmu Sosial',   // Ilmu atau Sains Militer
            48 => 'Ilmu Sosial',   // Urusan Publik

            // ILMU ALAM (7 majors)
            10 => 'Ilmu Alam',     // Kimia
            11 => 'Ilmu Alam',     // Ilmu atau Sains Kebumian
            12 => 'Ilmu Alam',     // Ilmu atau Sains Kelautan
            13 => 'Ilmu Alam',     // Biologi
            14 => 'Ilmu Alam',     // Biofisika
            15 => 'Ilmu Alam',     // Fisika
            16 => 'Ilmu Alam',     // Astronomi

            // ILMU FORMAL (3 majors)
            17 => 'Ilmu Formal',   // Komputer
            18 => 'Ilmu Formal',   // Logika
            19 => 'Ilmu Formal',   // Matematika

            // ILMU TERAPAN (29 majors)
            20 => 'Ilmu Terapan',  // Ilmu dan Sains Pertanian
            21 => 'Ilmu Terapan',  // Peternakan
            22 => 'Ilmu Terapan',  // Ilmu atau Sains Perikanan
            23 => 'Ilmu Terapan',  // Arsitektur
            24 => 'Ilmu Terapan',  // Perencanaan Wilayah
            25 => 'Ilmu Terapan',  // Desain
            33 => 'Ilmu Terapan',  // Teknik rekayasa
            34 => 'Ilmu Terapan',  // Ilmu atau Sains Lingkungan
            35 => 'Ilmu Terapan',  // Kehutanan
            36 => 'Ilmu Terapan',  // Ilmu atau Sains Kedokteran
            37 => 'Ilmu Terapan',  // Ilmu atau Sains Kedokteran Gigi
            38 => 'Ilmu Terapan',  // Ilmu atau Sains Veteriner
            39 => 'Ilmu Terapan',  // Ilmu Farmasi
            40 => 'Ilmu Terapan',  // Ilmu atau Sains Gizi
            41 => 'Ilmu Terapan',  // Kesehatan Masyarakat
            42 => 'Ilmu Terapan',  // Kebidanan
            43 => 'Ilmu Terapan',  // Keperawatan
            44 => 'Ilmu Terapan',  // Kesehatan
            45 => 'Ilmu Terapan',  // Ilmu atau Sains Informasi
            49 => 'Ilmu Terapan',  // Ilmu atau Sains Keolahragaan
            50 => 'Ilmu Terapan',  // Pariwisata
            51 => 'Ilmu Terapan',  // Transportasi
            52 => 'Ilmu Terapan',  // Bioteknologi, Biokewirausahaan, Bioinformatika
            53 => 'Ilmu Terapan',  // Geografi, Geografi Lingkungan, Sains Informasi Geografi
            54 => 'Ilmu Terapan',  // Informatika Medis atau Informatika Kesehatan
            55 => 'Ilmu Terapan',  // Konservasi Biologi, Konservasi Hewan Liar, Konservasi Hewan Liar dan Hutan, Konservasi Hutan, Konservasi Sumber Daya Alam
            56 => 'Ilmu Terapan',  // Teknologi Pangan, Teknologi Hasil Pertanian/Peternakan/Perikanan
            57 => 'Ilmu Terapan',  // Sains Data
            58 => 'Ilmu Terapan',  // Sains Perkopian
        ];

        $updatedCount = 0;
        foreach ($categoryMappings as $id => $correctCategory) {
            $major = MajorRecommendation::find($id);
            if ($major && $major->category !== $correctCategory) {
                $oldCategory = $major->category;
                $major->update(['category' => $correctCategory]);
                echo "Fixed ID {$id}: {$major->major_name} - {$oldCategory} → {$correctCategory}\n";
                $updatedCount++;
            }
        }

        echo "\n=== CATEGORY FIX COMPLETED ===\n";
        echo "Total updated: {$updatedCount} majors\n";

        // Show final summary
        echo "\n=== FINAL CATEGORY SUMMARY ===\n";
        $categories = MajorRecommendation::select('category')->distinct()->get();
        foreach($categories as $cat) {
            $count = MajorRecommendation::where('category', $cat->category)->count();
            echo "{$cat->category}: {$count} majors\n";
        }
    }
}
