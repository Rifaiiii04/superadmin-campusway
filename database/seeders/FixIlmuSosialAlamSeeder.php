<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MajorRecommendation;

class FixIlmuSosialAlamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "=== FIXING ILMU SOSIAL DATA ===\n";

        // Fix Ilmu Sosial majors
        $ilmuSosialData = [
            // NO. 6: Sosial
            6 => [
                'required_subjects' => ['Sosiologi'],
                'preferred_subjects' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_merdeka_subjects' => ['Sosiologi'],
                'kurikulum_2013_ipa_subjects' => ['Sejarah Indonesia'],
                'kurikulum_2013_ips_subjects' => ['Sosiologi'],
                'kurikulum_2013_bahasa_subjects' => ['Antropologi'],
            ],
            // NO. 7: Ekonomi
            7 => [
                'required_subjects' => ['Ekonomi', 'Matematika'],
                'preferred_subjects' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_merdeka_subjects' => ['Ekonomi', 'Matematika'],
                'kurikulum_2013_ipa_subjects' => ['Matematika'],
                'kurikulum_2013_ips_subjects' => ['Ekonomi', 'Matematika'],
                'kurikulum_2013_bahasa_subjects' => ['Matematika'],
            ],
            // NO. 8: Pertahanan
            8 => [
                'required_subjects' => ['Pendidikan Pancasila'],
                'preferred_subjects' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_merdeka_subjects' => ['Pendidikan Pancasila'],
                'kurikulum_2013_ipa_subjects' => ['PPKn'],
                'kurikulum_2013_ips_subjects' => ['PPKn'],
                'kurikulum_2013_bahasa_subjects' => ['PPKn'],
            ],
            // NO. 9: Psikologi
            9 => [
                'required_subjects' => ['Sosiologi', 'Matematika'],
                'preferred_subjects' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_merdeka_subjects' => ['Sosiologi', 'Matematika'],
                'kurikulum_2013_ipa_subjects' => ['Matematika'],
                'kurikulum_2013_ips_subjects' => ['Sosiologi'],
                'kurikulum_2013_bahasa_subjects' => ['Matematika'],
            ],
            // NO. 26: Ilmu atau Sains Akuntansi
            26 => [
                'required_subjects' => ['Ekonomi'],
                'preferred_subjects' => ['Matematika', 'Bahasa Indonesia'],
                'kurikulum_merdeka_subjects' => ['Ekonomi'],
                'kurikulum_2013_ipa_subjects' => ['Fisika', 'Kimia', 'Matematika'],
                'kurikulum_2013_ips_subjects' => ['Fisika', 'Kimia'],
                'kurikulum_2013_bahasa_subjects' => ['Fisika', 'Kimia', 'Matematika'],
            ],
            // NO. 27: Ilmu atau Sains Manajemen
            27 => [
                'required_subjects' => ['Ekonomi'],
                'preferred_subjects' => ['Matematika', 'Bahasa Indonesia'],
                'kurikulum_merdeka_subjects' => ['Ekonomi'],
                'kurikulum_2013_ipa_subjects' => ['Fisika', 'Kimia', 'Matematika'],
                'kurikulum_2013_ips_subjects' => ['Fisika', 'Kimia'],
                'kurikulum_2013_bahasa_subjects' => ['Fisika', 'Kimia', 'Matematika'],
            ],
            // NO. 28: Logistik
            28 => [
                'required_subjects' => ['Ekonomi'],
                'preferred_subjects' => ['Matematika', 'Bahasa Indonesia'],
                'kurikulum_merdeka_subjects' => ['Ekonomi'],
                'kurikulum_2013_ipa_subjects' => ['Fisika', 'Kimia', 'Matematika'],
                'kurikulum_2013_ips_subjects' => ['Fisika', 'Kimia'],
                'kurikulum_2013_bahasa_subjects' => ['Fisika', 'Kimia', 'Matematika'],
            ],
            // NO. 29: Administrasi Bisnis
            29 => [
                'required_subjects' => ['Ekonomi'],
                'preferred_subjects' => ['Matematika', 'Bahasa Indonesia'],
                'kurikulum_merdeka_subjects' => ['Ekonomi'],
                'kurikulum_2013_ipa_subjects' => ['Fisika', 'Kimia', 'Matematika'],
                'kurikulum_2013_ips_subjects' => ['Fisika', 'Kimia'],
                'kurikulum_2013_bahasa_subjects' => ['Fisika', 'Kimia', 'Matematika'],
            ],
            // NO. 30: Bisnis
            30 => [
                'required_subjects' => ['Ekonomi'],
                'preferred_subjects' => ['Matematika', 'Bahasa Indonesia'],
                'kurikulum_merdeka_subjects' => ['Ekonomi'],
                'kurikulum_2013_ipa_subjects' => ['Fisika', 'Kimia', 'Matematika'],
                'kurikulum_2013_ips_subjects' => ['Fisika', 'Kimia'],
                'kurikulum_2013_bahasa_subjects' => ['Fisika', 'Kimia', 'Matematika'],
            ],
            // NO. 31: Ilmu atau Sains Komunikasi
            31 => [
                'required_subjects' => ['Sosiologi', 'Antropologi'],
                'preferred_subjects' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_merdeka_subjects' => ['Sosiologi', 'Antropologi'],
                'kurikulum_2013_ipa_subjects' => ['Fisika', 'Kimia', 'Matematika'],
                'kurikulum_2013_ips_subjects' => ['Fisika', 'Kimia'],
                'kurikulum_2013_bahasa_subjects' => ['Fisika', 'Kimia', 'Matematika'],
            ],
            // NO. 32: Pendidikan
            32 => [
                'required_subjects' => ['Sosiologi', 'Antropologi'],
                'preferred_subjects' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_merdeka_subjects' => ['Sosiologi', 'Antropologi'],
                'kurikulum_2013_ipa_subjects' => ['Fisika', 'Kimia', 'Matematika'],
                'kurikulum_2013_ips_subjects' => ['Fisika', 'Kimia'],
                'kurikulum_2013_bahasa_subjects' => ['Fisika', 'Kimia', 'Matematika'],
            ],
            // NO. 46: Hukum
            46 => [
                'required_subjects' => ['Sosiologi', 'Pendidikan Pancasila'],
                'preferred_subjects' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_merdeka_subjects' => ['Sosiologi', 'Pendidikan Pancasila'],
                'kurikulum_2013_ipa_subjects' => ['PPKn'],
                'kurikulum_2013_ips_subjects' => ['PPKn', 'Sosiologi'],
                'kurikulum_2013_bahasa_subjects' => ['PPKn', 'Antropologi'],
            ],
            // NO. 47: Ilmu atau Sains Militer
            47 => [
                'required_subjects' => ['Sosiologi'],
                'preferred_subjects' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_merdeka_subjects' => ['Sosiologi'],
                'kurikulum_2013_ipa_subjects' => ['PPKn'],
                'kurikulum_2013_ips_subjects' => ['Sosiologi'],
                'kurikulum_2013_bahasa_subjects' => ['Sosiologi'],
            ],
            // NO. 48: Urusan Publik
            48 => [
                'required_subjects' => ['Sosiologi'],
                'preferred_subjects' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                'kurikulum_merdeka_subjects' => ['Sosiologi'],
                'kurikulum_2013_ipa_subjects' => ['PPKn'],
                'kurikulum_2013_ips_subjects' => ['Sosiologi'],
                'kurikulum_2013_bahasa_subjects' => ['Sosiologi'],
            ],
        ];

        foreach ($ilmuSosialData as $id => $data) {
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

        echo "\n=== FIXING ILMU ALAM DATA ===\n";

        // Fix Ilmu Alam majors
        $ilmuAlamData = [
            // NO. 10: Kimia
            10 => [
                'required_subjects' => ['Kimia'],
                'preferred_subjects' => ['Matematika', 'Fisika'],
                'kurikulum_merdeka_subjects' => ['Kimia'],
                'kurikulum_2013_ipa_subjects' => ['Kimia'],
                'kurikulum_2013_ips_subjects' => ['Kimia'],
                'kurikulum_2013_bahasa_subjects' => ['Kimia'],
            ],
            // NO. 11: Ilmu atau Sains Kebumian
            11 => [
                'required_subjects' => ['Fisika', 'Matematika Tingkat Lanjut'],
                'preferred_subjects' => ['Geografi', 'Kimia'],
                'kurikulum_merdeka_subjects' => ['Fisika', 'Matematika Tingkat Lanjut'],
                'kurikulum_2013_ipa_subjects' => ['Fisika', 'Matematika'],
                'kurikulum_2013_ips_subjects' => ['Fisika', 'Geografi'],
                'kurikulum_2013_bahasa_subjects' => ['Fisika', 'Matematika'],
            ],
            // NO. 12: Ilmu atau Sains Kelautan
            12 => [
                'required_subjects' => ['Biologi'],
                'preferred_subjects' => ['Kimia', 'Fisika'],
                'kurikulum_merdeka_subjects' => ['Biologi'],
                'kurikulum_2013_ipa_subjects' => ['Biologi'],
                'kurikulum_2013_ips_subjects' => ['Biologi', 'Geografi'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi'],
            ],
            // NO. 13: Biologi
            13 => [
                'required_subjects' => ['Biologi'],
                'preferred_subjects' => ['Kimia', 'Fisika'],
                'kurikulum_merdeka_subjects' => ['Biologi'],
                'kurikulum_2013_ipa_subjects' => ['Biologi'],
                'kurikulum_2013_ips_subjects' => ['Biologi'],
                'kurikulum_2013_bahasa_subjects' => ['Biologi'],
            ],
            // NO. 14: Biofisika
            14 => [
                'required_subjects' => ['Fisika'],
                'preferred_subjects' => ['Biologi', 'Matematika'],
                'kurikulum_merdeka_subjects' => ['Fisika'],
                'kurikulum_2013_ipa_subjects' => ['Fisika'],
                'kurikulum_2013_ips_subjects' => ['Fisika'],
                'kurikulum_2013_bahasa_subjects' => ['Fisika'],
            ],
            // NO. 15: Fisika
            15 => [
                'required_subjects' => ['Fisika'],
                'preferred_subjects' => ['Matematika', 'Kimia'],
                'kurikulum_merdeka_subjects' => ['Fisika'],
                'kurikulum_2013_ipa_subjects' => ['Fisika'],
                'kurikulum_2013_ips_subjects' => ['Fisika'],
                'kurikulum_2013_bahasa_subjects' => ['Fisika'],
            ],
            // NO. 16: Astronomi
            16 => [
                'required_subjects' => ['Fisika', 'Matematika Tingkat Lanjut'],
                'preferred_subjects' => ['Kimia', 'Biologi'],
                'kurikulum_merdeka_subjects' => ['Fisika', 'Matematika Tingkat Lanjut'],
                'kurikulum_2013_ipa_subjects' => ['Fisika', 'Matematika'],
                'kurikulum_2013_ips_subjects' => ['Fisika', 'Matematika'],
                'kurikulum_2013_bahasa_subjects' => ['Fisika', 'Matematika'],
            ],
        ];

        foreach ($ilmuAlamData as $id => $data) {
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

        echo "\n=== ILMU SOSIAL & ALAM DATA FIXED ===\n";
    }
}
