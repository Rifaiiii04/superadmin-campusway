<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentParentPhoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate nomor telepon orang tua untuk siswa yang belum memiliki
        $students = Student::whereNull('parent_phone')->orWhere('parent_phone', '')->get();
        
        $phonePrefixes = ['0812', '0813', '0814', '0815', '0816', '0817', '0818', '0819', '0852', '0853', '0855', '0856', '0857', '0858', '0859'];
        
        foreach ($students as $student) {
            // Generate nomor telepon random dengan format Indonesia
            $prefix = $phonePrefixes[array_rand($phonePrefixes)];
            $suffix = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $parentPhone = $prefix . $suffix;
            
            $student->update(['parent_phone' => $parentPhone]);
        }
        
        $this->command->info('Parent phone numbers updated for ' . $students->count() . ' students!');
    }
}
