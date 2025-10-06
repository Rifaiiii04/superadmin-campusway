<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MajorRecommendationsCompleteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama
        DB::table('major_recommendations')->truncate();

        // Insert Major Recommendations lengkap (59 program studi)
        $majorRecommendations = [
            // HUMANIORA (1-5)
            [
                'major_name' => 'Seni',
                'category' => 'Humaniora',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Sejarah',
                'category' => 'Humaniora',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Linguistik',
                'category' => 'Humaniora',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Susastra atau Sastra',
                'category' => 'Humaniora',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Filsafat',
                'category' => 'Humaniora',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // ILMU SOSIAL (6-9)
            [
                'major_name' => 'Sosial',
                'category' => 'Ilmu Sosial',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Ekonomi',
                'category' => 'Ilmu Sosial',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Pertahanan',
                'category' => 'Ilmu Sosial',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Psikologi',
                'category' => 'Ilmu Sosial',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // ILMU ALAM (10-16)
            [
                'major_name' => 'Kimia',
                'category' => 'Ilmu Alam',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Ilmu atau Sains Kebumian',
                'category' => 'Ilmu Alam',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Ilmu atau Sains Kelautan',
                'category' => 'Ilmu Alam',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Biologi',
                'category' => 'Ilmu Alam',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Biofisika',
                'category' => 'Ilmu Alam',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Fisika',
                'category' => 'Ilmu Alam',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Astronomi',
                'category' => 'Ilmu Alam',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // ILMU FORMAL (17-19)
            [
                'major_name' => 'Komputer',
                'category' => 'Ilmu Formal',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Logika',
                'category' => 'Ilmu Formal',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Matematika',
                'category' => 'Ilmu Formal',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // ILMU TERAPAN (20-25)
            [
                'major_name' => 'Ilmu dan Sains Pertanian',
                'category' => 'Ilmu Terapan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Peternakan',
                'category' => 'Ilmu Terapan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Ilmu atau Sains Perikanan',
                'category' => 'Ilmu Terapan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Arsitektur',
                'category' => 'Ilmu Terapan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Perencanaan Wilayah',
                'category' => 'Ilmu Terapan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Desain',
                'category' => 'Ilmu Terapan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // ILMU TERAPAN (26-32)
            [
                'major_name' => 'Ilmu atau Sains Akuntansi',
                'category' => 'Ilmu Terapan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Ilmu atau Sains Manajemen',
                'category' => 'Ilmu Terapan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Logistik',
                'category' => 'Ilmu Terapan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Administrasi Bisnis',
                'category' => 'Ilmu Terapan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Bisnis',
                'category' => 'Ilmu Terapan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Ilmu atau Sains Komunikasi',
                'category' => 'Ilmu Terapan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Pendidikan',
                'category' => 'Ilmu Terapan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // ILMU TERAPAN (33-40)
            [
                'major_name' => 'Teknik rekayasa',
                'category' => 'Ilmu Terapan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Ilmu atau Sains Lingkungan',
                'category' => 'Ilmu Lingkungan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Kehutanan',
                'category' => 'Ilmu Lingkungan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Ilmu atau Sains Kedokteran',
                'category' => 'Ilmu Kesehatan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Ilmu atau Sains Kedokteran Gigi',
                'category' => 'Ilmu Kesehatan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Ilmu atau Sains Veteriner',
                'category' => 'Ilmu Kesehatan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Ilmu Farmasi',
                'category' => 'Ilmu Kesehatan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Ilmu atau Sains Gizi',
                'category' => 'Ilmu Kesehatan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // ILMU KESEHATAN (41-49)
            [
                'major_name' => 'Kesehatan Masyarakat',
                'category' => 'Ilmu Kesehatan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Kebidanan',
                'category' => 'Ilmu Kesehatan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Keperawatan',
                'category' => 'Ilmu Kesehatan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Kesehatan',
                'category' => 'Ilmu Kesehatan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Ilmu atau Sains Informasi',
                'category' => 'Ilmu Teknologi',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Hukum',
                'category' => 'Ilmu Sosial',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Ilmu atau Sains Militer',
                'category' => 'Ilmu Sosial',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Urusan Publik',
                'category' => 'Ilmu Sosial',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Ilmu atau Sains Keolahragaan',
                'category' => 'Ilmu Kesehatan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // ILMU LINGKUNGAN/TERAPAN (50-59)
            [
                'major_name' => 'Pariwisata',
                'category' => 'Ilmu Lingkungan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Transportasi',
                'category' => 'Ilmu Teknologi',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Bioteknologi, Biokewirausahaan, Bioinformatika',
                'category' => 'Ilmu Lingkungan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Geografi, Geografi Lingkungan, Sains Informasi Geografi',
                'category' => 'Ilmu Lingkungan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Informatika Medis atau Informatika Kesehatan',
                'category' => 'Ilmu Teknologi',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Konservasi Biologi, Konservasi Hewan Liar, Konservasi Hewan Liar dan Hutan, Konservasi Hutan, Konservasi Sumber Daya Alam',
                'category' => 'Ilmu Lingkungan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Teknologi Pangan, Teknologi Hasil Pertanian/Peternakan/Perikanan',
                'category' => 'Ilmu Lingkungan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Sains Data',
                'category' => 'Ilmu Teknologi',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Sains Perkopian',
                'category' => 'Ilmu Lingkungan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'major_name' => 'Studi Humanitas',
                'category' => 'Humaniora',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($majorRecommendations as $major) {
            DB::table('major_recommendations')->insert($major);
        }
    }
}
