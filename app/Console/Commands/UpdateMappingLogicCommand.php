<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateMappingLogicCommand extends Command
{
    protected $signature = 'subjects:update-mapping';
    protected $description = 'Update mapping logic for max 2 optional subjects';

    public function handle()
    {
        $this->info('📚 Memperbarui logika mapping untuk maksimal 2 mata pelajaran pilihan...');

        // Hapus semua mapping lama
        DB::table('major_subject_mappings')->truncate();
        $this->info('🧹 Mapping lama dihapus');

        // Dapatkan semua prodi aktif
        $majors = DB::table('major_recommendations')
            ->where('is_active', true)
            ->get();

        $this->info("📊 Ditemukan {$majors->count()} prodi aktif");

        // Dapatkan mata pelajaran pilihan SMA/MA (maksimal 2)
        $smaSubjects = DB::table('subjects')
            ->where('education_level', 'SMA/MA')
            ->where('subject_type', 'Pilihan')
            ->where('is_active', true)
            ->orderBy('subject_number')
            ->limit(2)
            ->get();

        // Dapatkan mata pelajaran pilihan SMK/MAK (maksimal 1)
        $smkSubjects = DB::table('subjects')
            ->where('education_level', 'SMK/MAK')
            ->where('subject_type', 'Pilihan')
            ->where('is_active', true)
            ->orderBy('subject_number')
            ->limit(1)
            ->get();

        // Dapatkan Produk/PKK untuk SMK
        $produkPKK = DB::table('subjects')
            ->where('education_level', 'SMK/MAK')
            ->where('subject_type', 'Pilihan_Wajib')
            ->where('is_active', true)
            ->first();

        $mappingCount = 0;

        foreach ($majors as $major) {
            // Mapping untuk SMA/MA - 2 mata pelajaran pilihan
            if ($smaSubjects->count() >= 2) {
                foreach ($smaSubjects as $index => $subject) {
                    DB::table('major_subject_mappings')->insert([
                        'major_id' => $major->id,
                        'subject_id' => $subject->id,
                        'education_level' => 'SMA/MA',
                        'mapping_type' => 'pilihan_sesuai_prodi',
                        'priority' => $index + 1,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $mappingCount++;
                }
            }

            // Mapping untuk SMK/MAK - 1 mata pelajaran pilihan + Produk/PKK
            if ($smkSubjects->count() >= 1) {
                // Mata pelajaran pilihan
                $selectedSmkSubject = $smkSubjects->first();
                
                DB::table('major_subject_mappings')->insert([
                    'major_id' => $major->id,
                    'subject_id' => $selectedSmkSubject->id,
                    'education_level' => 'SMK/MAK',
                    'mapping_type' => 'pilihan_sesuai_prodi',
                    'priority' => 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $mappingCount++;

                // Produk/PKK
                if ($produkPKK) {
                    DB::table('major_subject_mappings')->insert([
                        'major_id' => $major->id,
                        'subject_id' => $produkPKK->id,
                        'education_level' => 'SMK/MAK',
                        'mapping_type' => 'pilihan_wajib_smk',
                        'priority' => 2,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $mappingCount++;
                }
            }
        }

        $this->info('✅ Mapping SMA/MA: 2 mata pelajaran pilihan per prodi');
        $this->info('✅ Mapping SMK/MAK: 1 mata pelajaran pilihan + Produk/PKK per prodi');
        $this->info("📊 Total mapping dibuat: {$mappingCount}");

        // Cek hasil
        $smaMappingCount = DB::table('major_subject_mappings')
            ->where('education_level', 'SMA/MA')
            ->count();

        $smkMappingCount = DB::table('major_subject_mappings')
            ->where('education_level', 'SMK/MAK')
            ->count();

        $this->info("📊 Total mapping SMA/MA: {$smaMappingCount}");
        $this->info("📊 Total mapping SMK/MAK: {$smkMappingCount}");

        $this->info('🎉 Update mapping selesai!');
    }
}
