<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\MajorRecommendation;
use App\Models\Subject;
use App\Models\MajorSubjectMapping;

class FixMappingRegulationCommand extends Command
{
    protected $signature = 'mapping:fix-regulation';
    protected $description = 'Fix major-subject mapping according to regulation (SMA: 2 pilihan, SMK: 1 PKK + 1 pilihan)';

    public function handle()
    {
        $this->info('🔧 Memperbaiki mapping jurusan ke mata pelajaran sesuai regulasi...');

        try {
            // 1. Hapus semua mapping yang ada
            $this->info('🗑️ Menghapus mapping lama...');
            MajorSubjectMapping::truncate();

            // 2. Dapatkan semua jurusan
            $majors = MajorRecommendation::where('is_active', true)->get();
            $this->info("📊 Ditemukan {$majors->count()} jurusan aktif");

            // 3. Dapatkan mata pelajaran pilihan yang tersedia
            $smaOptionalSubjects = Subject::where('subject_type', 'pilihan')
                ->where('education_level', 'SMA/MA')
                ->get();
            
            $smkOptionalSubjects = Subject::where('subject_type', 'pilihan')
                ->where('education_level', 'SMK/MAK')
                ->get();
            
            $smkPKKSubject = Subject::where('name', 'Produk/Projek Kreatif dan Kewirausahaan')
                ->where('education_level', 'SMK/MAK')
                ->first();

            $this->info("📚 Mata pelajaran pilihan tersedia:");
            $this->line("   - SMA/MA: " . $smaOptionalSubjects->count());
            $this->line("   - SMK/MAK: " . $smkOptionalSubjects->count());
            $this->line("   - SMK/MAK PKK: " . ($smkPKKSubject ? 'Ada' : 'Tidak ada'));

            // 4. Buat mapping untuk setiap jurusan
            $mappingCount = 0;
            foreach ($majors as $major) {
                $this->line("🔧 Memproses jurusan: {$major->major_name}");

                // Tentukan education level berdasarkan rumpun ilmu
                $educationLevel = $this->determineEducationLevel($major->rumpun_ilmu);
                
                if ($educationLevel === 'SMA/MA') {
                    // SMA/MA: 2 mata pelajaran pilihan sesuai prodi
                    $selectedSubjects = $this->selectSubjectsForMajor($major, $smaOptionalSubjects, 2);
                } else {
                    // SMK/MAK: 1 Produk/PKK + 1 mata pelajaran pilihan sesuai prodi
                    $selectedSubjects = collect([$smkPKKSubject]);
                    $otherSubject = $this->selectSubjectsForMajor($major, $smkOptionalSubjects, 1);
                    $selectedSubjects = $selectedSubjects->merge($otherSubject);
                }

                // Simpan mapping
                foreach ($selectedSubjects as $index => $subject) {
                    if ($subject) {
                        MajorSubjectMapping::create([
                            'major_id' => $major->id,
                            'subject_id' => $subject->id,
                            'subject_type' => $subject->subject_type,
                            'education_level' => $educationLevel,
                            'mapping_type' => 'optional',
                            'priority' => $index + 1
                        ]);
                        $mappingCount++;
                    }
                }

                $this->line("   ✅ Mapping: {$selectedSubjects->count()} mata pelajaran");
            }

            $this->info("🎉 Mapping selesai! Total mapping: {$mappingCount}");

            // 5. Verifikasi hasil
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
        $smaRumpun = ['ILMU ALAM', 'ILMU SOSIAL', 'ILMU BUDAYA', 'ILMU TERAPAN'];
        
        if (in_array($rumpunIlmu, $smaRumpun)) {
            return 'SMA/MA';
        } else {
            return 'SMK/MAK';
        }
    }

    private function selectSubjectsForMajor($major, $availableSubjects, $count)
    {
        // Logic untuk memilih mata pelajaran sesuai prodi
        // Untuk sementara, pilih secara random
        // Dalam implementasi nyata, ini harus berdasarkan mapping prodi ke mata pelajaran
        
        $selectedSubjects = collect();
        
        // Mapping sederhana berdasarkan nama jurusan
        $majorName = strtolower($major->major_name);
        
        // Pilih mata pelajaran yang relevan dengan jurusan
        $relevantSubjects = $availableSubjects->filter(function($subject) use ($majorName) {
            $subjectName = strtolower($subject->name);
            
            // Mapping sederhana
            if (strpos($majorName, 'matematika') !== false && strpos($subjectName, 'matematika') !== false) {
                return true;
            }
            if (strpos($majorName, 'fisika') !== false && strpos($subjectName, 'fisika') !== false) {
                return true;
            }
            if (strpos($majorName, 'kimia') !== false && strpos($subjectName, 'kimia') !== false) {
                return true;
            }
            if (strpos($majorName, 'biologi') !== false && strpos($subjectName, 'biologi') !== false) {
                return true;
            }
            if (strpos($majorName, 'ekonomi') !== false && strpos($subjectName, 'ekonomi') !== false) {
                return true;
            }
            if (strpos($majorName, 'sosiologi') !== false && strpos($subjectName, 'sosiologi') !== false) {
                return true;
            }
            if (strpos($majorName, 'geografi') !== false && strpos($subjectName, 'geografi') !== false) {
                return true;
            }
            if (strpos($majorName, 'sejarah') !== false && strpos($subjectName, 'sejarah') !== false) {
                return true;
            }
            if (strpos($majorName, 'bahasa') !== false && (strpos($subjectName, 'bahasa') !== false || strpos($subjectName, 'indonesia') !== false || strpos($subjectName, 'inggris') !== false)) {
                return true;
            }
            
            return false;
        });
        
        // Jika tidak ada yang relevan, pilih random
        if ($relevantSubjects->count() < $count) {
            $selectedSubjects = $availableSubjects->random($count);
        } else {
            $selectedSubjects = $relevantSubjects->random($count);
        }
        
        return $selectedSubjects;
    }
}
