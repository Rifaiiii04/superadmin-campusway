<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use Illuminate\Support\Facades\Hash;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat akun sekolah untuk testing
        $schools = [
            [
                'npsn' => '12345678',
                'name' => 'SMA Negeri 1 Jakarta',
                'password' => Hash::make('password123'),
            ],
            [
                'npsn' => '87654321',
                'name' => 'SMA Negeri 2 Bandung',
                'password' => Hash::make('password123'),
            ],
            [
                'npsn' => '11223344',
                'name' => 'SMA Negeri 3 Surabaya',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($schools as $schoolData) {
            School::updateOrCreate(
                ['npsn' => $schoolData['npsn']],
                $schoolData
            );
        }

        $this->command->info('School accounts created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('NPSN: 12345678, Password: password123');
        $this->command->info('NPSN: 87654321, Password: password123');
        $this->command->info('NPSN: 11223344, Password: password123');
    }
}
