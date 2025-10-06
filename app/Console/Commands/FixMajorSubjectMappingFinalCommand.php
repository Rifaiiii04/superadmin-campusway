<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\MajorRecommendation;
use App\Models\Subject;
use App\Models\MajorSubjectMapping;

class FixMajorSubjectMappingFinalCommand extends Command
{
    protected $signature = 'mapping:fix-major-subject-final';
    protected $description = 'Fix major subject mapping according to SMA/SMK rules';

    public function handle()
    {
        $this->info('🔧 Memperbaiki mapping jurusan ke mata pelajaran sesuai aturan SMA/SMK...');

        try {
            // 1. Hapus semua mapping yang ada
            $this->info('🗑️ Menghapus mapping lama...');
            MajorSubjectMapping::truncate();

            // 2. Dapatkan semua jurusan
            $majors = MajorRecommendation::where('is_active', true)->get();
            $this->info("📊 Ditemukan {$majors->count()} jurusan aktif");

            // 3. Dapatkan mata pelajaran pilihan yang tersedia
            $optionalSubjects = Subject::where('subject_type', 'pilihan')
                ->whereIn('education_level', ['SMA/MA', 'SMK/MAK'])
                ->get()
                ->groupBy('education_level');

            $this->info("📚 Mata pelajaran pilihan tersedia:");
            $this->line("   - SMA/MA: " . $optionalSubjects->get('SMA/MA', collect())->count());
            $this->line("   - SMK/MAK: " . $optionalSubjects->get('SMK/MAK', collect())->count());

            // 4. Buat mapping untuk setiap jurusan
            $mappingCount = 0;
            foreach ($majors as $major) {
                $this->line("🔧 Memproses jurusan: {$major->major_name}");

                // Tentukan education level berdasarkan rumpun ilmu
                $educationLevel = $this->determineEducationLevel($major->rumpun_ilmu);
                
                // Dapatkan mata pelajaran pilihan untuk level pendidikan ini
                $availableSubjects = $optionalSubjects->get($educationLevel, collect());
                
                if ($availableSubjects->isEmpty()) {
                    $this->warn("   ⚠️ Tidak ada mata pelajaran pilihan untuk {$educationLevel}");
                    continue;
                }

                // Untuk SMA: 2 mata pelajaran pilihan
                // Untuk SMK: 1 Produk/PKK + 1 mata pelajaran pilihan sesuai prodi
                if ($educationLevel === 'SMA/MA') {
                    // SMA: 2 mata pelajaran pilihan
                    $selectedSubjects = $availableSubjects->random(2);
                } else {
                    // SMK: 1 Produk/PKK + 1 mata pelajaran pilihan
                    $pkkSubject = Subject::where('name', 'Produk/Projek Kreatif dan Kewirausahaan')
                        ->where('education_level', 'SMK/MAK')
                        ->first();
                    
                    $otherSubject = $availableSubjects->random(1);
                    $selectedSubjects = collect([$pkkSubject])->merge($otherSubject);
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
                            'priority' => $index + 1 // Tambahkan priority
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
        // Untuk sementara, kita asumsikan semua jurusan bisa SMA atau SMK
        // Tapi dalam implementasi nyata, ini harus berdasarkan data sekolah
        
        // Contoh mapping sederhana
        $smaRumpun = ['ILMU ALAM', 'ILMU SOSIAL', 'ILMU BUDAYA', 'ILMU TERAPAN'];
        
        if (in_array($rumpunIlmu, $smaRumpun)) {
            return 'SMA/MA';
        } else {
            return 'SMK/MAK';
        }
    }
}
