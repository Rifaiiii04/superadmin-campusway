<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateDatabaseTerminologyCommand extends Command
{
    protected $signature = 'subjects:update-terminology';
    protected $description = 'Update database terminology from preferensi to pilihan';

    public function handle()
    {
        $this->info('📚 Memperbarui terminologi database dari preferensi ke pilihan...');

        // Update major_recommendations table
        $this->info('🔄 Memperbarui tabel major_recommendations...');
        
        // Check if preferred_subjects column exists
        $columns = DB::select("
            SELECT COLUMN_NAME 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_NAME = 'major_recommendations' 
            AND COLUMN_NAME = 'preferred_subjects'
        ");

        if (count($columns) > 0) {
            // Add optional_subjects column if it doesn't exist
            DB::statement("
                IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_NAME = 'major_recommendations' AND COLUMN_NAME = 'optional_subjects')
                BEGIN
                    ALTER TABLE major_recommendations ADD optional_subjects TEXT NULL
                END
            ");

            // Copy data from preferred_subjects to optional_subjects
            DB::statement("
                UPDATE major_recommendations 
                SET optional_subjects = preferred_subjects 
                WHERE preferred_subjects IS NOT NULL
            ");

            $this->info('✅ Data preferred_subjects disalin ke optional_subjects');
        } else {
            $this->info('✅ Kolom preferred_subjects tidak ditemukan, mungkin sudah diubah');
        }

        $this->info('✅ Tabel major_recommendations diperbarui');

        // Update subjects table
        $this->info('🔄 Memperbarui tabel subjects...');
        
        // Update subject_type from Pilihan to Pilihan (jika ada yang salah)
        $updatedSubjects = DB::table('subjects')
            ->where('subject_type', 'Pilihan')
            ->update([
                'subject_type' => 'Pilihan',
                'updated_at' => now()
            ]);

        $this->info("✅ {$updatedSubjects} mata pelajaran diperbarui");

        // Update mapping table
        $this->info('🔄 Memperbarui tabel major_subject_mappings...');
        
        $updatedMappings = DB::table('major_subject_mappings')
            ->where('mapping_type', 'pilihan_sesuai_prodi')
            ->update([
                'mapping_type' => 'pilihan_sesuai_prodi',
                'updated_at' => now()
            ]);

        $this->info("✅ {$updatedMappings} mapping diperbarui");

        // Verifikasi hasil
        $this->info('📊 Verifikasi hasil:');
        
        $smaCount = DB::table('subjects')
            ->where('education_level', 'SMA/MA')
            ->where('subject_type', 'Pilihan')
            ->count();

        $smkCount = DB::table('subjects')
            ->where('education_level', 'SMK/MAK')
            ->where('subject_type', 'Pilihan')
            ->count();

        $produkPKKCount = DB::table('subjects')
            ->where('education_level', 'SMK/MAK')
            ->where('subject_type', 'Pilihan_Wajib')
            ->count();

        $this->info("📊 SMA/MA Pilihan: {$smaCount}");
        $this->info("📊 SMK/MAK Pilihan: {$smkCount}");
        $this->info("📊 SMK/MAK Produk/PKK: {$produkPKKCount}");

        $this->info('🎉 Update terminologi selesai!');
    }
}
