<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class RumpunIlmuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create rumpun_ilmu table if not exists
        if (!Schema::hasTable('rumpun_ilmu')) {
            Schema::create('rumpun_ilmu', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Insert rumpun ilmu data
        $rumpunIlmu = [
            [
                'name' => 'HUMANIORA',
                'description' => 'Rumpun ilmu yang mempelajari manusia, budaya, dan ekspresi manusia',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ILMU SOSIAL',
                'description' => 'Rumpun ilmu yang mempelajari perilaku manusia dalam masyarakat',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ILMU ALAM',
                'description' => 'Rumpun ilmu yang mempelajari fenomena alam dan hukum-hukum alam',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ILMU FORMAL',
                'description' => 'Rumpun ilmu yang mempelajari sistem formal dan abstrak',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ILMU TERAPAN',
                'description' => 'Rumpun ilmu yang menerapkan pengetahuan untuk memecahkan masalah praktis',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('rumpun_ilmu')->insert($rumpunIlmu);

        // Create program_studi table if not exists
        if (!Schema::hasTable('program_studi')) {
            Schema::create('program_studi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rumpun_ilmu_id')->constrained('rumpun_ilmu');
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Insert program studi data according to the business process
        $programStudi = [
            // HUMANIORA
            ['rumpun_ilmu_id' => 1, 'name' => 'Seni', 'description' => 'Program studi yang mempelajari seni dan budaya'],
            ['rumpun_ilmu_id' => 1, 'name' => 'Sejarah', 'description' => 'Program studi yang mempelajari peristiwa masa lalu'],
            ['rumpun_ilmu_id' => 1, 'name' => 'Linguistik', 'description' => 'Program studi yang mempelajari bahasa dan struktur bahasa'],
            ['rumpun_ilmu_id' => 1, 'name' => 'Sastra', 'description' => 'Program studi yang mempelajari karya sastra'],
            ['rumpun_ilmu_id' => 1, 'name' => 'Filsafat', 'description' => 'Program studi yang mempelajari pemikiran mendalam tentang kehidupan'],

            // ILMU SOSIAL
            ['rumpun_ilmu_id' => 2, 'name' => 'Sosial', 'description' => 'Program studi yang mempelajari interaksi sosial'],
            ['rumpun_ilmu_id' => 2, 'name' => 'Ekonomi', 'description' => 'Program studi yang mempelajari produksi, distribusi, dan konsumsi'],
            ['rumpun_ilmu_id' => 2, 'name' => 'Pertahanan', 'description' => 'Program studi yang mempelajari keamanan dan pertahanan'],
            ['rumpun_ilmu_id' => 2, 'name' => 'Psikologi', 'description' => 'Program studi yang mempelajari perilaku dan mental manusia'],

            // ILMU ALAM
            ['rumpun_ilmu_id' => 3, 'name' => 'Kimia', 'description' => 'Program studi yang mempelajari sifat dan perubahan zat'],
            ['rumpun_ilmu_id' => 3, 'name' => 'Ilmu Kebumian', 'description' => 'Program studi yang mempelajari bumi dan fenomena geologi'],
            ['rumpun_ilmu_id' => 3, 'name' => 'Ilmu Kelautan', 'description' => 'Program studi yang mempelajari laut dan ekosistem laut'],
            ['rumpun_ilmu_id' => 3, 'name' => 'Biologi', 'description' => 'Program studi yang mempelajari makhluk hidup'],
            ['rumpun_ilmu_id' => 3, 'name' => 'Biofisika', 'description' => 'Program studi yang mempelajari aspek fisik dalam biologi'],
            ['rumpun_ilmu_id' => 3, 'name' => 'Fisika', 'description' => 'Program studi yang mempelajari materi dan energi'],
            ['rumpun_ilmu_id' => 3, 'name' => 'Astronomi', 'description' => 'Program studi yang mempelajari benda langit dan alam semesta'],

            // ILMU FORMAL
            ['rumpun_ilmu_id' => 4, 'name' => 'Matematika', 'description' => 'Program studi yang mempelajari struktur abstrak dan logika'],
            ['rumpun_ilmu_id' => 4, 'name' => 'Statistika', 'description' => 'Program studi yang mempelajari pengumpulan dan analisis data'],
            ['rumpun_ilmu_id' => 4, 'name' => 'Logika', 'description' => 'Program studi yang mempelajari penalaran yang benar'],

            // ILMU TERAPAN
            ['rumpun_ilmu_id' => 5, 'name' => 'Teknik', 'description' => 'Program studi yang menerapkan ilmu untuk memecahkan masalah praktis'],
            ['rumpun_ilmu_id' => 5, 'name' => 'Kedokteran', 'description' => 'Program studi yang mempelajari kesehatan dan pengobatan'],
            ['rumpun_ilmu_id' => 5, 'name' => 'Pertanian', 'description' => 'Program studi yang mempelajari budidaya tanaman dan hewan'],
            ['rumpun_ilmu_id' => 5, 'name' => 'Teknologi Informasi', 'description' => 'Program studi yang mempelajari sistem informasi dan komputer'],
        ];

        foreach ($programStudi as $program) {
            DB::table('program_studi')->insert([
                'rumpun_ilmu_id' => $program['rumpun_ilmu_id'],
                'name' => $program['name'],
                'description' => $program['description'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
