<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MajorRecommendation;

class FixHumanioraDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "=== FIXING HUMANIORA DATA ===\n";

        // Fix Studi Humanitas (ID: 59)
        $studihumanitas = MajorRecommendation::find(59);
        if ($studihumanitas) {
            $studihumanitas->update([
                'required_subjects' => json_encode(['Antropologi', 'Sosiologi']),
                'preferred_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_merdeka_subjects' => json_encode(['Antropologi', 'Sosiologi']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Antropologi', 'Sosiologi']),
                'kurikulum_2013_ips_subjects' => json_encode(['Antropologi', 'Sosiologi']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Antropologi', 'Sosiologi']),
            ]);
            echo "Fixed Studi Humanitas\n";
        }

        // Fix Filsafat (ID: 5) - should have different subjects for different curriculum tracks
        $filsafat = MajorRecommendation::find(5);
        if ($filsafat) {
            $filsafat->update([
                'required_subjects' => json_encode(['Sosiologi']),
                'preferred_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_merdeka_subjects' => json_encode(['Sosiologi']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Sejarah Indonesia']),
                'kurikulum_2013_ips_subjects' => json_encode(['Sosiologi']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Antropologi']),
            ]);
            echo "Fixed Filsafat\n";
        }

        // Fix Susastra atau Sastra (ID: 4) - should have different subjects for different curriculum tracks
        $sastra = MajorRecommendation::find(4);
        if ($sastra) {
            $sastra->update([
                'required_subjects' => json_encode(['Bahasa Indonesia Tingkat Lanjut']),
                'preferred_subjects' => json_encode(['Bahasa Asing yang Relevan']),
                'kurikulum_merdeka_subjects' => json_encode(['Bahasa Indonesia Tingkat Lanjut', 'Bahasa Asing yang Relevan']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Asing yang Relevan']),
                'kurikulum_2013_ips_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Asing yang Relevan']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Asing yang Relevan']),
            ]);
            echo "Fixed Susastra atau Sastra\n";
        }

        // Fix Linguistik (ID: 3) - should have different subjects for different curriculum tracks
        $linguistik = MajorRecommendation::find(3);
        if ($linguistik) {
            $linguistik->update([
                'required_subjects' => json_encode(['Bahasa Indonesia Tingkat Lanjut', 'Bahasa Inggris']),
                'preferred_subjects' => json_encode(['Bahasa Asing yang Relevan']),
                'kurikulum_merdeka_subjects' => json_encode(['Bahasa Indonesia Tingkat Lanjut', 'Bahasa Inggris']),
                'kurikulum_2013_ipa_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_2013_ips_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_2013_bahasa_subjects' => json_encode(['Bahasa Indonesia', 'Bahasa Inggris']),
            ]);
            echo "Fixed Linguistik\n";
        }

        echo "=== HUMANIORA DATA FIXED ===\n";
    }
}
