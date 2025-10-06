<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RumpunIlmuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rumpunIlmu = [
            [
                'name' => 'Humaniora',
                'description' => 'Ilmu tentang manusia, budaya, dan nilai kemanusiaan',
                'is_active' => true,
            ],
            [
                'name' => 'Ilmu Sosial',
                'description' => 'Ilmu tentang masyarakat dan interaksi manusia',
                'is_active' => true,
            ],
            [
                'name' => 'Ilmu Alam',
                'description' => 'Ilmu tentang fenomena alam dan hukum alam',
                'is_active' => true,
            ],
            [
                'name' => 'Ilmu Formal',
                'description' => 'Ilmu tentang sistem formal, logika, dan matematika',
                'is_active' => true,
            ],
            [
                'name' => 'Ilmu Terapan',
                'description' => 'Ilmu yang menerapkan pengetahuan untuk masalah praktis',
                'is_active' => true,
            ],
            [
                'name' => 'Ilmu Kesehatan',
                'description' => 'Ilmu tentang kesehatan dan kedokteran',
                'is_active' => true,
            ],
            [
                'name' => 'Ilmu Lingkungan',
                'description' => 'Ilmu tentang lingkungan hidup dan konservasi',
                'is_active' => true,
            ],
            [
                'name' => 'Ilmu Teknologi',
                'description' => 'Ilmu tentang teknologi dan rekayasa',
                'is_active' => true,
            ],
        ];

        foreach ($rumpunIlmu as $rumpun) {
            DB::table('rumpun_ilmu')->updateOrInsert(
                ['name' => $rumpun['name']],
                $rumpun
            );
        }
    }
}