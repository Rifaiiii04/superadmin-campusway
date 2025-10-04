<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\MajorRecommendation;
use App\Models\Subject;
use App\Models\MajorSubjectMapping;
use App\Helpers\SMKSubjectHelper;

class FixMappingBasedOnReferenceCommand extends Command
{
    protected $signature = 'mapping:fix-based-on-reference';
    protected $description = 'Fix major-subject mapping based on reference table from regulation';

    public function handle()
    {
        $this->info('🔧 Memperbaiki mapping berdasarkan referensi tabel yang benar...');

        try {
            // 1. Hapus semua mapping yang ada
            $this->info('🗑️ Menghapus mapping lama...');
            MajorSubjectMapping::truncate();

            // 2. Dapatkan semua jurusan
            $majors = MajorRecommendation::where('is_active', true)->get();
            $this->info("📊 Ditemukan {$majors->count()} jurusan aktif");

            // 3. Mapping berdasarkan referensi tabel
            $referenceMapping = $this->getReferenceMapping();
            
            $mappingCount = 0;
            foreach ($majors as $major) {
                $this->line("🔧 Memproses jurusan: {$major->major_name}");

                // Tentukan education level berdasarkan rumpun ilmu
                $educationLevel = $this->determineEducationLevel($major->rumpun_ilmu);
                
                // Dapatkan mata pelajaran berdasarkan referensi
                $subjects = $this->getSubjectsForMajor($major, $referenceMapping, $educationLevel);
                
                if (empty($subjects)) {
                    $this->warn("   ⚠️ Tidak ada referensi untuk {$major->major_name}");
                    continue;
                }

                // Simpan mapping
                foreach ($subjects as $index => $subjectName) {
                    $subject = Subject::where('name', $subjectName)
                        ->where('education_level', $educationLevel)
                        ->first();
                    
                    if ($subject) {
                        MajorSubjectMapping::create([
                            'major_id' => $major->id,
                            'subject_id' => $subject->id,
                            'subject_type' => 'pilihan', // Force to pilihan for mapping
                            'education_level' => $educationLevel,
                            'mapping_type' => 'optional',
                            'priority' => $index + 1
                        ]);
                        $mappingCount++;
                    }
                }

                $this->line("   ✅ Mapping: " . implode(', ', $subjects));
            }

            $this->info("🎉 Mapping selesai! Total mapping: {$mappingCount}");

            // 4. Verifikasi hasil
            $this->info('📊 Verifikasi hasil...');
            
            $smaMappings = MajorSubjectMapping::where('education_level', 'SMA/MA')->count();
            $smkMappings = MajorSubjectMapping::where('education_level', 'SMK/MAK')->count();
            
            $this->info("📈 Hasil mapping:");
            $this->line("   - SMA/MA: {$smaMappings} mapping");
            $this->line("   - SMK/MAK: {$smkMappings} mapping");

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }

    private function determineEducationLevel($rumpunIlmu)
    {
        // Tentukan education level berdasarkan rumpun ilmu
        // Berdasarkan referensi tabel, sebagian besar adalah SMA/MA
        $smaRumpun = ['ILMU ALAM', 'ILMU SOSIAL', 'ILMU BUDAYA', 'ILMU TERAPAN', 'ILMU FORMAL'];
        
        // Khusus untuk HUMANIORA, periksa nama jurusan
        if ($rumpunIlmu === 'HUMANIORA') {
            // Jurusan tertentu di HUMANIORA adalah SMA/MA, bukan SMK
            $smaHumanioraMajors = ['Seni', 'Linguistik', 'Filsafat', 'Sejarah', 'Sastra'];
            // Ini akan dicek di getSubjectsForMajor
            return 'SMA/MA'; // Default untuk HUMANIORA adalah SMA/MA
        }
        
        if (in_array($rumpunIlmu, $smaRumpun)) {
            return 'SMA/MA';
        } else {
            return 'SMK/MAK';
        }
    }

    private function getReferenceMapping()
    {
        return [
            // HUMANIORA
            'Seni' => ['Seni Budaya'],
            'Sejarah' => ['Sejarah'],
            'Linguistik' => ['Bahasa Indonesia Lanjutan', 'Bahasa Inggris Lanjutan'],
            'Susastra atau Sastra' => ['Bahasa Indonesia Lanjutan'],
            'Filsafat' => ['Sosiologi'],
            
            // ILMU SOSIAL
            'Sosial' => ['Sosiologi'],
            'Ekonomi' => ['Ekonomi', 'Matematika'],
            'Pertahanan' => ['PPKn/Pendidikan Pancasila'],
            'Psikologi' => ['Sosiologi', 'Matematika'],
            
            // ILMU ALAM
            'Kimia' => ['Kimia'],
            'Ilmu atau Sains Kebumian' => ['Fisika', 'Matematika Lanjutan'],
            'Ilmu atau Sains Kelautan' => ['Biologi'],
            'Biologi' => ['Biologi'],
            'Biofisika' => ['Fisika'],
            'Fisika' => ['Fisika'],
            'Astronomi' => ['Fisika', 'Matematika Lanjutan'],
            
            // ILMU FORMAL
            'Komputer' => ['Matematika Lanjutan'],
            'Logika' => ['Matematika Lanjutan'],
            'Matematika' => ['Matematika Lanjutan'],
            
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
            'Pendidikan' => ['Sosiologi'], // Paling relevan 1 mata pelajaran
            'Teknik Rekayasa' => ['Fisika', 'Kimia', 'Matematika Lanjutan'],
            'Ilmu atau Sains Lingkungan' => ['Biologi'],
            'Ilmu atau Sains Kedokteran' => ['Biologi', 'Kimia'],
            'Ilmu atau Sains Kedokteran Gigi' => ['Biologi', 'Kimia'],
            'Ilmu atau Sains Veteriner' => ['Biologi', 'Kimia'],
            'Ilmu atau Sains Farmasi' => ['Biologi', 'Kimia'],
            'Ilmu atau Sains Gizi' => ['Biologi', 'Kimia'],
            'Ilmu Gizi' => ['Biologi', 'Kimia'],
            'Kesehatan Masyarakat' => ['Biologi'],
            'Kebidanan' => ['Biologi'],
            'Keperawatan' => ['Biologi'],
            'Kesehatan' => ['Biologi'],
            'Ilmu atau Sains Informasi' => ['Matematika Lanjutan'],
            'Hukum' => ['Sosiologi', 'PPKn/Pendidikan Pancasila'],
            'Ilmu atau Sains Militer' => ['PPKn/Pendidikan Pancasila'],
            'Urusan Publik' => ['PPKn/Pendidikan Pancasila'],
            'Ilmu atau Sains Keolahragaan' => ['PJOK', 'Biologi'],
            'Pariwisata' => ['Ekonomi', 'Bahasa Inggris Lanjutan'],
            'Transportasi' => ['Matematika Lanjutan'],
            'Bioteknologi' => ['Biologi', 'Matematika'],
            'Geografi' => ['Geografi', 'Matematika'],
            'Informatika Medis' => ['Biologi', 'Matematika Lanjutan'],
            'Konservasi Biologi' => ['Biologi'],
            'Teknologi Pangan' => ['Kimia', 'Biologi'],
            'Sains Data' => ['Matematika Lanjutan'],
            'Sains Perkopian' => ['Biologi'],
            'Studi Humanitas' => ['Antropologi', 'Sosiologi'],
            // SMA/MA specific mappings (not SMK)
            'Seni' => ['Seni Budaya', 'Matematika'],
            'Linguistik' => ['Bahasa Indonesia Lanjutan', 'Bahasa Inggris Lanjutan'],
            'Filsafat' => ['Sosiologi', 'Matematika'],
            'Sejarah' => ['Sejarah', 'Matematika'],
            'Sastra' => ['Bahasa Indonesia Lanjutan', 'Bahasa Inggris Lanjutan'],
        ];
    }

    private function getSubjectsForMajor($major, $referenceMapping, $educationLevel)
    {
        $majorName = $major->major_name;
        
        // Khusus untuk HUMANIORA, periksa apakah ini SMK atau SMA
        if ($major->rumpun_ilmu === 'HUMANIORA') {
            $smaHumanioraMajors = ['Seni', 'Linguistik', 'Filsafat', 'Sejarah', 'Sastra'];
            $isSMK = !in_array($majorName, $smaHumanioraMajors);
            
            if ($isSMK) {
                // Untuk SMK HUMANIORA, gunakan helper SMK
                return SMKSubjectHelper::getSubjectsForMajor($majorName);
            }
            // Untuk SMA HUMANIORA, lanjut ke logika SMA
        } else if ($educationLevel === 'SMK/MAK') {
            // Untuk SMK non-HUMANIORA, gunakan helper SMK
            return SMKSubjectHelper::getSubjectsForMajor($majorName);
        }
        
        // Untuk SMA/MA, gunakan referensi tabel
        foreach ($referenceMapping as $key => $subjects) {
            if (stripos($majorName, $key) !== false) {
                // Ambil 2 mata pelajaran yang paling relevan (selalu 2)
                $selectedSubjects = array_slice($subjects, 0, 2);
                
                // Pastikan selalu ada 2 mata pelajaran
                while (count($selectedSubjects) < 2) {
                    // Jika kurang dari 2, tambahkan mata pelajaran default
                    $defaultSubjects = ['Matematika', 'Fisika', 'Biologi', 'Kimia', 'Ekonomi', 'Sosiologi'];
                    foreach ($defaultSubjects as $defaultSubject) {
                        if (!in_array($defaultSubject, $selectedSubjects)) {
                            $selectedSubjects[] = $defaultSubject;
                            break; // Tambahkan satu per satu
                        }
                    }
                }
                
                // Untuk SMA/MA, pastikan TIDAK ada Produk/PKK
                $selectedSubjects = array_filter($selectedSubjects, function($subject) {
                    return $subject !== 'Produk/Projek Kreatif dan Kewirausahaan';
                });
                $selectedSubjects = array_values($selectedSubjects); // Re-index array
                
                // Jika setelah filter kurang dari 2, tambahkan lagi
                while (count($selectedSubjects) < 2) {
                    $defaultSubjects = ['Matematika', 'Fisika', 'Biologi', 'Kimia', 'Ekonomi', 'Sosiologi'];
                    foreach ($defaultSubjects as $defaultSubject) {
                        if (!in_array($defaultSubject, $selectedSubjects)) {
                            $selectedSubjects[] = $defaultSubject;
                            break;
                        }
                    }
                }
                
                return $selectedSubjects;
            }
        }
        
        // Jika tidak ditemukan mapping yang cocok, gunakan default untuk SMA
        return ['Matematika', 'Fisika']; // Default untuk SMA
    }
}
