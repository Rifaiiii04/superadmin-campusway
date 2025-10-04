<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MajorRecommendation;
use Illuminate\Support\Facades\Log;

class UpdatePreferredSubjectsFinalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== UPDATING PREFERRED SUBJECTS FINAL - 100% ACCURATE ===');

        // Data mata pelajaran pilihan FINAL sesuai list yang diberikan
        $preferredSubjectsData = [
            // HUMANIORA
            'Seni' => ['Seni Budaya', 'Sejarah Indonesia'],
            'Sejarah' => ['Sejarah Indonesia', 'Sosiologi'],
            'Linguistik' => ['Bahasa Indonesia Tingkat Lanjut', 'Bahasa Inggris'],
            'Susastra atau Sastra' => ['Bahasa Indonesia Tingkat Lanjut', 'Bahasa Asing yang Relevan'],
            'Filsafat' => ['Sosiologi', 'Sejarah Indonesia'],
            'Studi Humanitas' => ['Antropologi', 'Sosiologi'],

            // ILMU SOSIAL
            'Sosial' => ['Sosiologi', 'Antropologi'],
            'Ekonomi' => ['Ekonomi', 'Matematika'],
            'Pertahanan' => ['PPKn', 'Sejarah Indonesia'],
            'Psikologi' => ['Sosiologi', 'Matematika'],
            'Hukum' => ['Sosiologi', 'PPKn'],
            'Ilmu atau Sains Militer' => ['Sosiologi', 'PPKn'],
            'Urusan Publik' => ['Sosiologi', 'PPKn'],

            // ILMU ALAM
            'Kimia' => ['Kimia', 'Matematika'],
            'Ilmu atau Sains Kebumian' => ['Fisika', 'Matematika Tingkat Lanjut'],
            'Ilmu atau Sains Kelautan' => ['Biologi', 'Geografi'],
            'Biologi' => ['Biologi', 'Kimia'],
            'Biofisika' => ['Fisika', 'Matematika'],
            'Fisika' => ['Fisika', 'Matematika'],
            'Astronomi' => ['Fisika', 'Matematika Tingkat Lanjut'],

            // ILMU FORMAL
            'Komputer' => ['Matematika Tingkat Lanjut', 'Fisika'],
            'Logika' => ['Matematika Tingkat Lanjut', 'Bahasa Inggris'],
            'Matematika' => ['Matematika Tingkat Lanjut', 'Fisika'],

            // ILMU TERAPAN
            'Ilmu dan Sains Pertanian' => ['Biologi', 'Kimia'],
            'Peternakan' => ['Biologi', 'Kimia'],
            'Ilmu atau Sains Perikanan' => ['Biologi', 'Kimia'],
            'Arsitektur' => ['Matematika', 'Fisika'],
            'Perencanaan Wilayah' => ['Ekonomi', 'Matematika'],
            'Desain' => ['Seni Budaya', 'Matematika'],
            'Ilmu atau Sains Akuntansi' => ['Ekonomi', 'Matematika'],
            'Ilmu atau Sains Manajemen' => ['Ekonomi', 'Matematika'],
            'Logistik' => ['Ekonomi', 'Matematika'],
            'Administrasi Bisnis' => ['Ekonomi', 'Matematika'],
            'Bisnis' => ['Ekonomi', 'Matematika'],
            'Ilmu atau Sains Komunikasi' => ['Sosiologi', 'Antropologi'],
            'Pendidikan' => ['Bahasa Indonesia', 'Matematika'],
            'Teknik rekayasa' => ['Fisika', 'Matematika Tingkat Lanjut'],
            'Ilmu atau Sains Lingkungan' => ['Biologi', 'Kimia'],
            'Kehutanan' => ['Biologi', 'Geografi'],
            'Ilmu atau Sains Kedokteran' => ['Biologi', 'Kimia'],
            'Ilmu atau Sains Kedokteran Gigi' => ['Biologi', 'Kimia'],
            'Ilmu atau Sains Veteriner' => ['Biologi', 'Kimia'],
            'Ilmu Farmasi' => ['Biologi', 'Kimia'],
            'Ilmu atau Sains Gizi' => ['Biologi', 'Kimia'],
            'Kesehatan Masyarakat' => ['Biologi', 'Sosiologi'],
            'Kebidanan' => ['Biologi', 'Kimia'],
            'Keperawatan' => ['Biologi', 'Kimia'],
            'Kesehatan' => ['Biologi', 'Kimia'],
            'Ilmu atau Sains Informasi' => ['Matematika Tingkat Lanjut', 'Fisika'],
            'Hukum' => ['Sosiologi', 'PPKn'],
            'Ilmu atau Sains Militer' => ['Sosiologi', 'PPKn'],
            'Urusan Publik' => ['Sosiologi', 'PPKn'],
            'Ilmu atau Sains Keolahragaan' => ['PJOK', 'Biologi'],
            'Pariwisata' => ['Ekonomi', 'Bahasa Inggris'],
            'Transportasi' => ['Matematika Tingkat Lanjut', 'Fisika'],
            'Bioteknologi, Biokewirausahaan, Bioinformatika' => ['Biologi', 'Matematika'],
            'Geografi, Geografi Lingkungan, Sains Informasi Geografi' => ['Geografi', 'Matematika'],
            'Informatika Medis atau Informatika Kesehatan' => ['Biologi', 'Matematika Tingkat Lanjut'],
            'Konservasi Biologi, Konservasi Hewan Liar, Konservasi Hewan Liar dan Hutan, Konservasi Hutan, Konservasi Sumber Daya Alam' => ['Biologi', 'Geografi'],
            'Teknologi Pangan, Teknologi Hasil Pertanian/Peternakan/Perikanan' => ['Kimia', 'Biologi'],
            'Sains Data' => ['Matematika Tingkat Lanjut', 'Komputer'],
            'Sains Perkopian' => ['Biologi', 'Kimia'],
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
                $this->command->info("  Count: " . count($preferredSubjects) . " subjects");
                $updatedCount++;
            } else {
                $this->command->warn("Not found: {$majorName}");
                $notFoundCount++;
            }
        }

        $this->command->info("\n=== PREFERRED SUBJECTS FINAL UPDATE COMPLETED ===");
        $this->command->info("Updated: {$updatedCount} majors");
        $this->command->info("Not found: {$notFoundCount} majors");

        // Verification - check that all have exactly 2 subjects
        $this->command->info("\n=== VERIFICATION - CHECKING 2 SUBJECTS REQUIREMENT ===");
        $majors = MajorRecommendation::all();
        $correctCount = 0;
        $incorrectCount = 0;
        
        foreach ($majors as $major) {
            $subjectCount = is_array($major->preferred_subjects) ? count($major->preferred_subjects) : 0;
            if ($subjectCount === 2) {
                $correctCount++;
            } else {
                $incorrectCount++;
                $this->command->warn("ID {$major->id} - {$major->major_name}: {$subjectCount} subjects (should be 2)");
            }
        }
        
        $this->command->info("Correct (2 subjects): {$correctCount}");
        $this->command->info("Incorrect (not 2 subjects): {$incorrectCount}");

        // Show sample verification
        $this->command->info("\n=== SAMPLE VERIFICATION ===");
        $sampleMajors = ['Seni', 'Kimia', 'Komputer', 'Administrasi Bisnis', 'Sains Data'];
        foreach($sampleMajors as $majorName) {
            $major = MajorRecommendation::where('major_name', 'LIKE', $majorName . '%')->first();
            if ($major) {
                $this->command->info("{$major->major_name}: " . json_encode($major->preferred_subjects));
            }
        }
    }
}
