<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\MajorRecommendation;
use App\Models\Subject;
use App\Models\MajorSubjectMapping;
use App\Helpers\SMKSubjectHelper;

class UpdateSMKMappingCommand extends Command
{
    protected $signature = 'mapping:update-smk';
    protected $description = 'Update SMK mapping using JSON configuration';

    public function handle()
    {
        $this->info('🔧 Mengupdate mapping SMK menggunakan konfigurasi JSON...');

        try {
            // 1. Dapatkan semua jurusan SMK
            $smkMajors = MajorRecommendation::where('is_active', true)
                ->whereIn('rumpun_ilmu', ['HUMANIORA']) // SMK rumpun
                ->get();
            
            $this->info("📊 Ditemukan {$smkMajors->count()} jurusan SMK");

            // 2. Hapus mapping lama untuk SMK
            foreach ($smkMajors as $major) {
                MajorSubjectMapping::where('major_id', $major->id)
                    ->where('education_level', 'SMK/MAK')
                    ->delete();
            }
            $this->info('🗑️ Mapping SMK lama dihapus');

            // 3. Buat mapping baru berdasarkan JSON
            $mappingCount = 0;
            foreach ($smkMajors as $major) {
                $this->line("🔧 Memproses jurusan SMK: {$major->major_name}");

                // Dapatkan mata pelajaran dari helper SMK
                $subjects = SMKSubjectHelper::getSubjectsForMajor($major->major_name);
                
                if (empty($subjects)) {
                    $this->warn("   ⚠️ Tidak ada konfigurasi untuk {$major->major_name}");
                    continue;
                }

                // Simpan mapping
                foreach ($subjects as $index => $subjectName) {
                    $subject = Subject::where('name', $subjectName)
                        ->where('education_level', 'SMK/MAK')
                        ->first();
                    
                    if ($subject) {
                        MajorSubjectMapping::create([
                            'major_id' => $major->id,
                            'subject_id' => $subject->id,
                            'subject_type' => 'pilihan',
                            'education_level' => 'SMK/MAK',
                            'mapping_type' => 'optional',
                            'priority' => $index + 1
                        ]);
                        $mappingCount++;
                    } else {
                        $this->warn("   ⚠️ Subject tidak ditemukan: {$subjectName}");
                    }
                }

                $this->line("   ✅ Mapping: " . implode(', ', $subjects));
            }

            $this->info("🎉 Mapping SMK selesai! Total mapping: {$mappingCount}");

            // 4. Verifikasi hasil
            $this->info('📊 Verifikasi hasil...');
            
            $smkMappings = MajorSubjectMapping::where('education_level', 'SMK/MAK')->count();
            $this->info("📈 SMK Mappings: {$smkMappings}");

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}
