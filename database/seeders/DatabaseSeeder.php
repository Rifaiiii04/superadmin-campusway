<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RumpunIlmuSeeder::class,
            ProgramStudiSeeder::class,
            SubjectsSeeder::class,
            MajorRecommendationsSeeder::class,
        ]);
    }
}