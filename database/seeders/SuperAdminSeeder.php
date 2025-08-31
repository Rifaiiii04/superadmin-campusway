<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\School;
use App\Models\Question;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        Admin::create([
            'username' => 'superadmin',
            'password' => Hash::make('password123'),
        ]);

        // Create sample schools
        School::create([
            'npsn' => '12345678',
            'name' => 'SMA Negeri 1 Jakarta',
            'password_hash' => Hash::make('password123'),
        ]);

        School::create([
            'npsn' => '87654321',
            'name' => 'SMA Negeri 2 Bandung',
            'password_hash' => Hash::make('password123'),
        ]);

        // Create sample questions
        Question::create([
            'subject' => 'Matematika',
            'type' => 'Pilihan Ganda',
            'content' => 'Berapakah hasil dari 15 + 27?',
            'media_url' => null,
        ]);

        Question::create([
            'subject' => 'Bahasa Indonesia',
            'type' => 'Essay',
            'content' => 'Jelaskan pengertian puisi dan berikan contohnya!',
            'media_url' => null,
        ]);

        Question::create([
            'subject' => 'Fisika',
            'type' => 'Pilihan Ganda',
            'content' => 'Apa satuan SI untuk gaya?',
            'media_url' => null,
        ]);
    }
}
