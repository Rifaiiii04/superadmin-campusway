<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMajorSubjectMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Membuat mapping prodi ke mata pelajaran pilihan sesuai peraturan:
     * - SMA: 2 mata pelajaran pilihan sesuai prodi
     * - SMK: 1 mata pelajaran pilihan sesuai prodi + Produk/PKK
     */
    public function run(): void
    {
        echo "📚 Membuat mapping prodi ke mata pelajaran pilihan...\n";

        // Check if tables exist
        if (!Schema::hasTable('major_recommendations')) {
            echo "❌ Table major_recommendations not found!\n";
            return;
        }

        if (!Schema::hasTable('subjects')) {
            echo "❌ Table subjects not found!\n";
            return;
        }

        // Buat tabel mapping jika belum ada
        if (!Schema::hasTable('major_subject_mappings')) {
            Schema::create('major_subject_mappings', function ($table) {
                $table->id();
                $table->unsignedBigInteger('major_id');
                $table->unsignedBigInteger('subject_id');
                $table->string('education_level'); // SMA/MA atau SMK/MAK
                $table->string('mapping_type'); // 'pilihan_sesuai_prodi' atau 'pilihan_wajib_smk'
                $table->integer('priority'); // 1 untuk pilihan utama, 2 untuk pilihan kedua
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('major_id')->references('id')->on('major_recommendations')->onDelete('cascade');
                $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
                $table->unique(['major_id', 'subject_id', 'education_level']);
            });
            echo "✅ Tabel major_subject_mappings dibuat\n";
        }

        // Hapus mapping lama
        DB::table('major_subject_mappings')->truncate();
        echo "🧹 Mapping lama dihapus\n";

        // Dapatkan semua prodi aktif
        $majors = DB::table('major_recommendations')
            ->where('is_active', true)
            ->get();

        echo "📊 Ditemukan {$majors->count()} prodi aktif\n";

        // Dapatkan mata pelajaran pilihan SMA/MA
        $smaSubjects = DB::table('subjects')
            ->where('education_level', 'SMA/MA')
            ->where('subject_type', 'Pilihan')
            ->where('is_active', true)
            ->get();

        // Dapatkan mata pelajaran pilihan SMK/MAK
        $smkSubjects = DB::table('subjects')
            ->where('education_level', 'SMK/MAK')
            ->where('subject_type', 'Pilihan')
            ->where('is_active', true)
            ->get();

        // Dapatkan Produk/PKK untuk SMK
        $produkPKK = DB::table('subjects')
            ->where('education_level', 'SMK/MAK')
            ->where('subject_type', 'Pilihan_Wajib')
            ->where('name', 'Produk/Projek Kreatif dan Kewirausahaan')
            ->first();

        $mappingCount = 0;

        foreach ($majors as $major) {
            // Mapping untuk SMA/MA - 2 mata pelajaran pilihan sesuai prodi
            if ($smaSubjects->count() >= 2) {
                // Ambil 2 mata pelajaran pilihan pertama untuk SMA
                $selectedSmaSubjects = $smaSubjects->take(2);
                
                foreach ($selectedSmaSubjects as $index => $subject) {
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
                // Ambil 1 mata pelajaran pilihan untuk SMK
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

                // Tambahkan Produk/PKK untuk SMK
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

        echo "✅ Mapping SMA/MA: 2 mata pelajaran pilihan per prodi\n";
        echo "✅ Mapping SMK/MAK: 1 mata pelajaran pilihan + Produk/PKK per prodi\n";
        echo "📊 Total mapping dibuat: {$mappingCount}\n";
        echo "\n🎉 Mapping prodi ke mata pelajaran selesai!\n";
    }
}
