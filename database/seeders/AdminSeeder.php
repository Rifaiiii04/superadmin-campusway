<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin already exists
        $existingAdmin = DB::table('admins')->where('username', 'campusway_superadmin')->first();
        
        if (!$existingAdmin) {
            DB::table('admins')->insert([
                'username' => 'campusway_superadmin',
                'password' => Hash::make('campusway321@'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->command->info('Admin user created successfully!');
            $this->command->info('Username: campusway_superadmin');
            $this->command->info('Password: campusway321@');
        } else {
            $this->command->info('Admin user already exists!');
        }
    }
}
