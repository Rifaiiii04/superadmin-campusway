<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MajorRecommendation;

class FixIlmuFormalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "=== FIXING ILMU FORMAL DATA ===\n";

        // Fix Ilmu Formal majors
        $ilmuFormalData = [
            // NO. 17: Komputer
            17 => [
                'required_subjects' => ['Matematika Tingkat Lanjut'],
                'preferred_subjects' => ['Fisika', 'Kimia'],
                'kurikulum_merdeka_subjects' => ['Matematika Tingkat Lanjut'],
                'kurikulum_2013_ipa_subjects' => ['Matematika'],
                'kurikulum_2013_ips_subjects' => ['Matematika'],
                'kurikulum_2013_bahasa_subjects' => ['Matematika'],
            ],
            // NO. 18: Logika
            18 => [
                'required_subjects' => ['Matematika Tingkat Lanjut'],
                'preferred_subjects' => ['Fisika', 'Kimia'],
                'kurikulum_merdeka_subjects' => ['Matematika Tingkat Lanjut'],
                'kurikulum_2013_ipa_subjects' => ['Matematika'],
                'kurikulum_2013_ips_subjects' => ['Matematika'],
                'kurikulum_2013_bahasa_subjects' => ['Matematika'],
            ],
            // NO. 19: Matematika
            19 => [
                'required_subjects' => ['Matematika Tingkat Lanjut'],
                'preferred_subjects' => ['Fisika', 'Kimia'],
                'kurikulum_merdeka_subjects' => ['Matematika Tingkat Lanjut'],
                'kurikulum_2013_ipa_subjects' => ['Matematika'],
                'kurikulum_2013_ips_subjects' => ['Matematika'],
                'kurikulum_2013_bahasa_subjects' => ['Matematika'],
            ],
        ];

        foreach ($ilmuFormalData as $id => $data) {
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

        echo "\n=== ILMU FORMAL DATA FIXED ===\n";
    }
}
