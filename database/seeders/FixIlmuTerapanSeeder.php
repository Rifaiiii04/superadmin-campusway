<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MajorRecommendation;

class FixIlmuTerapanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "=== FIXING ILMU TERAPAN DATA ===\n";

        // Fix Ilmu Terapan majors
        $ilmuTerapanData = [
            // NO. 20: Ilmu dan Sains Pertanian
            20 => [
                'required_subjects' => ['Biologi'],
                'preferred_subjects' => ['Kimia', 'Matematika'],
                'kurikulum_merdeka_subjects' => ['Biologi'],
                'kurikulum_2013_ipa_subjects' => ['Biologi'],
                'kurikulum_2013_ips_subjects' => ['Biologi'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi'],
            ],
            // NO. 21: Peternakan
            21 => [
                'required_subjects' => ['Biologi'],
                'preferred_subjects' => ['Kimia', 'Matematika'],
                'kurikulum_merdeka_subjects' => ['Biologi'],
                'kurikulum_2013_ipa_subjects' => ['Biologi'],
                'kurikulum_2013_ips_subjects' => ['Biologi'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi'],
            ],
            // NO. 22: Ilmu atau Sains Perikanan
            22 => [
                'required_subjects' => ['Biologi'],
                'preferred_subjects' => ['Kimia', 'Matematika'],
                'kurikulum_merdeka_subjects' => ['Biologi'],
                'kurikulum_2013_ipa_subjects' => ['Biologi'],
                'kurikulum_2013_ips_subjects' => ['Biologi'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi'],
            ],
            // NO. 23: Arsitektur
            23 => [
                'required_subjects' => ['Matematika', 'Fisika'],
                'preferred_subjects' => ['Seni Budaya', 'Kimia'],
                'kurikulum_merdeka_subjects' => ['Matematika', 'Fisika'],
                'kurikulum_2013_ipa_subjects' => ['Matematika', 'Fisika'],
                'kurikulum_2013_ips_subjects' => ['Matematika', 'Fisika'],
                'kurikulum_2013_bahasa_subjects' => ['Matematika', 'Fisika'],
            ],
            // NO. 24: Perencanaan Wilayah
            24 => [
                'required_subjects' => ['Ekonomi', 'Matematika'],
                'preferred_subjects' => ['Geografi', 'Sosiologi'],
                'kurikulum_merdeka_subjects' => ['Ekonomi', 'Matematika'],
                'kurikulum_2013_ipa_subjects' => ['Matematika'],
                'kurikulum_2013_ips_subjects' => ['Ekonomi', 'Matematika'],
                'kurikulum_2013_bahasa_subjects' => ['Matematika'],
            ],
            // NO. 25: Desain
            25 => [
                'required_subjects' => ['Seni Budaya', 'Matematika'],
                'preferred_subjects' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_merdeka_subjects' => ['Seni Budaya', 'Matematika'],
                'kurikulum_2013_ipa_subjects' => ['Seni Budaya', 'Matematika'],
                'kurikulum_2013_ips_subjects' => ['Seni Budaya', 'Matematika'],
                'kurikulum_2013_bahasa_subjects' => ['Seni Budaya', 'Matematika'],
            ],
            // NO. 33: Teknik rekayasa
            33 => [
                'required_subjects' => ['Fisika', 'Kimia', 'Matematika Tingkat Lanjut'],
                'preferred_subjects' => ['Biologi', 'Bahasa Indonesia'],
                'kurikulum_merdeka_subjects' => ['Fisika', 'Kimia', 'Matematika Tingkat Lanjut'],
                'kurikulum_2013_ipa_subjects' => ['Fisika', 'Kimia', 'Matematika'],
                'kurikulum_2013_ips_subjects' => ['Fisika', 'Kimia', 'Matematika'],
                'kurikulum_2013_bahasa_subjects' => ['Fisika', 'Kimia', 'Matematika'],
            ],
            // NO. 34: Ilmu atau Sains Lingkungan
            34 => [
                'required_subjects' => ['Biologi'],
                'preferred_subjects' => ['Kimia', 'Geografi'],
                'kurikulum_merdeka_subjects' => ['Biologi'],
                'kurikulum_2013_ipa_subjects' => ['Biologi'],
                'kurikulum_2013_ips_subjects' => ['Biologi'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi'],
            ],
            // NO. 35: Kehutanan
            35 => [
                'required_subjects' => ['Biologi'],
                'preferred_subjects' => ['Kimia', 'Geografi'],
                'kurikulum_merdeka_subjects' => ['Biologi'],
                'kurikulum_2013_ipa_subjects' => ['Biologi'],
                'kurikulum_2013_ips_subjects' => ['Biologi'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi'],
            ],
            // NO. 36: Ilmu atau Sains Kedokteran (already has data, but let's update)
            36 => [
                'required_subjects' => ['Biologi', 'Kimia'],
                'preferred_subjects' => ['Fisika', 'Matematika'],
                'kurikulum_merdeka_subjects' => ['Biologi', 'Kimia'],
                'kurikulum_2013_ipa_subjects' => ['Biologi', 'Kimia'],
                'kurikulum_2013_ips_subjects' => ['Biologi', 'Kimia'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi', 'Kimia'],
            ],
            // NO. 37: Ilmu atau Sains Kedokteran Gigi
            37 => [
                'required_subjects' => ['Biologi', 'Kimia'],
                'preferred_subjects' => ['Fisika', 'Matematika'],
                'kurikulum_merdeka_subjects' => ['Biologi', 'Kimia'],
                'kurikulum_2013_ipa_subjects' => ['Biologi', 'Kimia'],
                'kurikulum_2013_ips_subjects' => ['Biologi', 'Kimia'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi', 'Kimia'],
            ],
            // NO. 38: Ilmu atau Sains Veteriner
            38 => [
                'required_subjects' => ['Biologi', 'Kimia'],
                'preferred_subjects' => ['Fisika', 'Matematika'],
                'kurikulum_merdeka_subjects' => ['Biologi', 'Kimia'],
                'kurikulum_2013_ipa_subjects' => ['Biologi', 'Kimia'],
                'kurikulum_2013_ips_subjects' => ['Biologi', 'Kimia'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi', 'Kimia'],
            ],
            // NO. 39: Ilmu Farmasi (already has data, but let's update)
            39 => [
                'required_subjects' => ['Biologi', 'Kimia'],
                'preferred_subjects' => ['Matematika', 'Fisika'],
                'kurikulum_merdeka_subjects' => ['Biologi', 'Kimia'],
                'kurikulum_2013_ipa_subjects' => ['Biologi', 'Kimia'],
                'kurikulum_2013_ips_subjects' => ['Biologi', 'Kimia'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi', 'Kimia'],
            ],
            // NO. 40: Ilmu atau Sains Gizi
            40 => [
                'required_subjects' => ['Biologi', 'Kimia'],
                'preferred_subjects' => ['Matematika', 'Fisika'],
                'kurikulum_merdeka_subjects' => ['Biologi', 'Kimia'],
                'kurikulum_2013_ipa_subjects' => ['Biologi', 'Kimia'],
                'kurikulum_2013_ips_subjects' => ['Biologi', 'Kimia'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi', 'Kimia'],
            ],
            // NO. 41: Kesehatan Masyarakat
            41 => [
                'required_subjects' => ['Biologi'],
                'preferred_subjects' => ['Sosiologi', 'Matematika'],
                'kurikulum_merdeka_subjects' => ['Biologi'],
                'kurikulum_2013_ipa_subjects' => ['Biologi'],
                'kurikulum_2013_ips_subjects' => ['Sosiologi', 'Biologi'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi'],
            ],
            // NO. 42: Kebidanan
            42 => [
                'required_subjects' => ['Biologi'],
                'preferred_subjects' => ['Kimia', 'Matematika'],
                'kurikulum_merdeka_subjects' => ['Biologi'],
                'kurikulum_2013_ipa_subjects' => ['Biologi'],
                'kurikulum_2013_ips_subjects' => ['Biologi'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi'],
            ],
            // NO. 43: Keperawatan
            43 => [
                'required_subjects' => ['Biologi'],
                'preferred_subjects' => ['Kimia', 'Matematika'],
                'kurikulum_merdeka_subjects' => ['Biologi'],
                'kurikulum_2013_ipa_subjects' => ['Biologi'],
                'kurikulum_2013_ips_subjects' => ['Biologi'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi'],
            ],
            // NO. 44: Kesehatan
            44 => [
                'required_subjects' => ['Biologi'],
                'preferred_subjects' => ['Kimia', 'Matematika'],
                'kurikulum_merdeka_subjects' => ['Biologi'],
                'kurikulum_2013_ipa_subjects' => ['Biologi'],
                'kurikulum_2013_ips_subjects' => ['Biologi'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi'],
            ],
            // NO. 45: Ilmu atau Sains Informasi
            45 => [
                'required_subjects' => ['Matematika Tingkat Lanjut'],
                'preferred_subjects' => ['Fisika', 'Kimia'],
                'kurikulum_merdeka_subjects' => ['Matematika Tingkat Lanjut'],
                'kurikulum_2013_ipa_subjects' => ['Matematika'],
                'kurikulum_2013_ips_subjects' => ['Matematika'],
                'kurikulum_2013_bahasa_subjects' => ['Matematika'],
            ],
            // NO. 49: Ilmu atau Sains Keolahragaan
            49 => [
                'required_subjects' => ['PJOK', 'Biologi'],
                'preferred_subjects' => ['Matematika', 'Bahasa Indonesia'],
                'kurikulum_merdeka_subjects' => ['PJOK', 'Biologi'],
                'kurikulum_2013_ipa_subjects' => ['PJOK', 'Biologi'],
                'kurikulum_2013_ips_subjects' => ['PJOK'],
                'kurikulum_2013_bahasa_subjects' => ['PJOK'],
            ],
            // NO. 50: Pariwisata
            50 => [
                'required_subjects' => ['Ekonomi', 'Bahasa Inggris Tingkat Lanjut'],
                'preferred_subjects' => ['Bahasa Asing Lainnya', 'Sosiologi'],
                'kurikulum_merdeka_subjects' => ['Ekonomi', 'Bahasa Inggris Tingkat Lanjut', 'Bahasa Asing Lainnya'],
                'kurikulum_2013_ipa_subjects' => ['Bahasa Inggris', 'Ekonomi'],
                'kurikulum_2013_ips_subjects' => ['Ekonomi', 'Bahasa Inggris'],
                'kurikulum_2013_bahasa_subjects' => ['Bahasa Sastra Inggris', 'Bahasa Asing Lainnya'],
            ],
            // NO. 51: Transportasi
            51 => [
                'required_subjects' => ['Matematika Tingkat Lanjut'],
                'preferred_subjects' => ['Fisika', 'Kimia'],
                'kurikulum_merdeka_subjects' => ['Matematika Tingkat Lanjut'],
                'kurikulum_2013_ipa_subjects' => ['Matematika'],
                'kurikulum_2013_ips_subjects' => ['Matematika'],
                'kurikulum_2013_bahasa_subjects' => ['Matematika'],
            ],
            // NO. 52: Bioteknologi, Biokewirausahaan, Bioinformatika
            52 => [
                'required_subjects' => ['Biologi', 'Matematika'],
                'preferred_subjects' => ['Kimia', 'Fisika'],
                'kurikulum_merdeka_subjects' => ['Biologi', 'Matematika'],
                'kurikulum_2013_ipa_subjects' => ['Biologi', 'Matematika'],
                'kurikulum_2013_ips_subjects' => ['Biologi', 'Matematika'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi', 'Matematika'],
            ],
            // NO. 53: Geografi, Geografi Lingkungan, Sains Informasi Geografi
            53 => [
                'required_subjects' => ['Geografi', 'Matematika'],
                'preferred_subjects' => ['Fisika', 'Kimia'],
                'kurikulum_merdeka_subjects' => ['Geografi', 'Matematika'],
                'kurikulum_2013_ipa_subjects' => ['Fisika', 'Matematika'],
                'kurikulum_2013_ips_subjects' => ['Geografi', 'Matematika'],
                'kurikulum_2013_bahasa_subjects' => ['Matematika'],
            ],
            // NO. 54: Informatika Medis atau Informatika Kesehatan
            54 => [
                'required_subjects' => ['Biologi', 'Matematika Tingkat Lanjut'],
                'preferred_subjects' => ['Fisika', 'Kimia'],
                'kurikulum_merdeka_subjects' => ['Biologi', 'Matematika Tingkat Lanjut'],
                'kurikulum_2013_ipa_subjects' => ['Biologi', 'Matematika'],
                'kurikulum_2013_ips_subjects' => ['Biologi', 'Matematika'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi', 'Matematika'],
            ],
            // NO. 55: Konservasi Biologi, Konservasi Hewan Liar, Konservasi Hewan Liar dan Hutan, Konservasi Hutan, Konservasi Sumber Daya Alam
            55 => [
                'required_subjects' => ['Biologi'],
                'preferred_subjects' => ['Kimia', 'Geografi'],
                'kurikulum_merdeka_subjects' => ['Biologi'],
                'kurikulum_2013_ipa_subjects' => ['Biologi'],
                'kurikulum_2013_ips_subjects' => ['Biologi'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi'],
            ],
            // NO. 56: Teknologi Pangan, Teknologi Hasil Pertanian/Peternakan/Perikanan
            56 => [
                'required_subjects' => ['Kimia', 'Biologi'],
                'preferred_subjects' => ['Matematika', 'Fisika'],
                'kurikulum_merdeka_subjects' => ['Kimia', 'Biologi'],
                'kurikulum_2013_ipa_subjects' => ['Kimia', 'Biologi'],
                'kurikulum_2013_ips_subjects' => ['Kimia', 'Biologi'],
                'kurikulum_2013_bahasa_subjects' => ['Kimia', 'Biologi'],
            ],
            // NO. 57: Sains Data
            57 => [
                'required_subjects' => ['Matematika Tingkat Lanjut'],
                'preferred_subjects' => ['Fisika', 'Kimia'],
                'kurikulum_merdeka_subjects' => ['Matematika Tingkat Lanjut'],
                'kurikulum_2013_ipa_subjects' => ['Matematika'],
                'kurikulum_2013_ips_subjects' => ['Matematika'],
                'kurikulum_2013_bahasa_subjects' => ['Matematika'],
            ],
            // NO. 58: Sains Perkopian
            58 => [
                'required_subjects' => ['Biologi'],
                'preferred_subjects' => ['Kimia', 'Matematika'],
                'kurikulum_merdeka_subjects' => ['Biologi'],
                'kurikulum_2013_ipa_subjects' => ['Biologi'],
                'kurikulum_2013_ips_subjects' => ['Biologi'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi'],
            ],
        ];

        foreach ($ilmuTerapanData as $id => $data) {
            $major = MajorRecommendation::find($id);
            if ($major) {
                $major->update([
                    'required_subjects' => json_encode($data['required_subjects']),
                    'preferred_subjects' => json_encode($data['preferred_subjects']),
                    'kurikulum_merdeka_subjects' => json_encode($data['kurikulum_merdeka_subjects']),
                    'kurikulum_2013_ipa_subjects' => json_encode($data['kurikulum_2013_ipa_subjects']),
                    'kurikulum_2013_ips_subjects' => json_encode($data['kurikulum_2013_ips_subjects']),
                    'kurikulum_2013_bahasa_subjects' => json_encode($data['kurikulum_2013_bahasa_subjects']),
                ]);
                echo "Fixed {$major->major_name}\n";
            }
        }

        echo "\n=== ILMU TERAPAN DATA FIXED ===\n";
    }
}
