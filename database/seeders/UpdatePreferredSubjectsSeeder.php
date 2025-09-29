<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MajorRecommendation;
use Illuminate\Support\Facades\Log;

class UpdatePreferredSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== UPDATING PREFERRED SUBJECTS ACCORDING TO KEPMENDIKDASMEN ===');

        // Data mata pelajaran pilihan sesuai Kepmendikdasmen No. 95/M/2025
        $preferredSubjectsData = [
            // HUMANIORA
            'Seni' => ['Seni Budaya'],
            'Sejarah' => ['Sejarah Indonesia'],
            'Linguistik' => ['Bahasa Indonesia Tingkat Lanjut', 'Bahasa Inggris'],
            'Susastra atau Sastra' => ['Bahasa Indonesia Tingkat Lanjut', 'Bahasa Asing yang Relevan'],
            'Filsafat' => ['Sosiologi', 'Sejarah Indonesia'],
            'Studi Humanitas' => ['Antropologi', 'Sosiologi'],

            // ILMU SOSIAL
            'Sosial' => ['Sosiologi', 'Sejarah Indonesia'],
            'Ekonomi' => ['Ekonomi', 'Matematika'],
            'Pertahanan' => ['Pendidikan Pancasila (PPKn)'],
            'Psikologi' => ['Sosiologi', 'Matematika'],
            'Hukum' => ['Sosiologi', 'Pendidikan Pancasila (PPKn)'],
            'Ilmu atau Sains Militer' => ['Sosiologi'],
            'Urusan Publik' => ['Sosiologi'],

            // ILMU ALAM
            'Kimia' => ['Kimia'],
            'Ilmu atau Sains Kebumian' => ['Fisika', 'Matematika Tingkat Lanjut'],
            'Ilmu atau Sains Kelautan' => ['Biologi'],
            'Biologi' => ['Biologi'],
            'Biofisika' => ['Fisika'],
            'Fisika' => ['Fisika'],
            'Astronomi' => ['Fisika', 'Matematika Tingkat Lanjut'],

            // ILMU FORMAL
            'Komputer' => ['Matematika Tingkat Lanjut'],
            'Logika' => ['Matematika Tingkat Lanjut'],
            'Matematika' => ['Matematika Tingkat Lanjut'],

            // ILMU TERAPAN
            'Ilmu dan Sains Pertanian' => ['Biologi'],
            'Peternakan' => ['Biologi'],
            'Ilmu atau Sains Perikanan' => ['Biologi'],
            'Arsitektur' => ['Matematika', 'Fisika'],
            'Perencanaan Wilayah' => ['Ekonomi', 'Matematika'],
            'Desain' => ['Seni Budaya', 'Matematika'],
            'Ilmu atau Sains Akuntansi' => ['Ekonomi'],
            'Ilmu atau Sains Manajemen' => ['Ekonomi'],
            'Logistik' => ['Ekonomi'],
            'Administrasi Bisnis' => ['Ekonomi'],
            'Bisnis' => ['Ekonomi'],
            'Ilmu atau Sains Komunikasi' => ['Sosiologi', 'Antropologi'],
            'Pendidikan' => ['Maksimal 1 mata pelajaran relevan'],
            'Teknik rekayasa' => ['Fisika/Kimia', 'Matematika Tingkat Lanjut'],
            'Ilmu atau Sains Lingkungan' => ['Biologi'],
            'Kehutanan' => ['Biologi'],
            'Ilmu atau Sains Kedokteran' => ['Biologi', 'Kimia'],
            'Ilmu atau Sains Kedokteran Gigi' => ['Biologi', 'Kimia'],
            'Ilmu atau Sains Veteriner' => ['Biologi', 'Kimia'],
            'Ilmu Farmasi' => ['Biologi', 'Kimia'],
            'Ilmu atau Sains Gizi' => ['Biologi', 'Kimia'],
            'Kesehatan Masyarakat' => ['Biologi'],
            'Kebidanan' => ['Biologi'],
            'Keperawatan' => ['Biologi'],
            'Kesehatan' => ['Biologi'],
            'Ilmu atau Sains Informasi' => ['Matematika Tingkat Lanjut'],
            'Ilmu atau Sains Keolahragaan' => ['PJOK', 'Biologi'],
            'Pariwisata' => ['Ekonomi', 'Bahasa Inggris/Bahasa Asing'],
            'Transportasi' => ['Matematika Tingkat Lanjut'],
            'Bioteknologi, Biokewirausahaan, Bioinformatika' => ['Biologi', 'Matematika'],
            'Geografi, Geografi Lingkungan, Sains Informasi Geografi' => ['Geografi', 'Matematika'],
            'Informatika Medis atau Informatika Kesehatan' => ['Biologi', 'Matematika Tingkat Lanjut'],
            'Konservasi Biologi, Konservasi Hewan Liar, Konservasi Hewan Liar dan Hutan, Konservasi Hutan, Konservasi Sumber Daya Alam' => ['Biologi'],
            'Teknologi Pangan, Teknologi Hasil Pertanian/Peternakan/Perikanan' => ['Kimia', 'Biologi'],
            'Sains Data' => ['Matematika Tingkat Lanjut'],
            'Sains Perkopian' => ['Biologi'],
        ];

        $updatedCount = 0;
        $notFoundCount = 0;

        foreach ($preferredSubjectsData as $majorName => $preferredSubjects) {
            $major = MajorRecommendation::where('major_name', 'LIKE', $majorName . '%')->first();
            
            if ($major) {
                $oldSubjects = $major->preferred_subjects;
                $major->update(['preferred_subjects' => $preferredSubjects]);
                
                $this->command->info("Updated ID {$major->id}: {$major->major_name}");
                $this->command->info("  Old: " . json_encode($oldSubjects));
                $this->command->info("  New: " . json_encode($preferredSubjects));
                $updatedCount++;
            } else {
                $this->command->warn("Not found: {$majorName}");
                $notFoundCount++;
            }
        }

        $this->command->info("\n=== PREFERRED SUBJECTS UPDATE COMPLETED ===");
        $this->command->info("Updated: {$updatedCount} majors");
        $this->command->info("Not found: {$notFoundCount} majors");

        // Verification
        $this->command->info("\n=== VERIFICATION ===");
        $sampleMajors = MajorRecommendation::take(5)->get(['id', 'major_name', 'preferred_subjects']);
        foreach ($sampleMajors as $major) {
            $this->command->info("ID {$major->id} - {$major->major_name}: " . json_encode($major->preferred_subjects));
        }
    }
}
