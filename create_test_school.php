<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\School;

try {
    // Cek apakah sekolah test sudah ada
    $existingSchool = School::where('npsn', '12345678')->first();
    
    if ($existingSchool) {
        echo "Test school already exists with NPSN: 12345678\n";
        echo "School name: " . $existingSchool->name . "\n";
    } else {
        // Buat sekolah test
        $school = new School();
        $school->npsn = '12345678';
        $school->name = 'Sekolah Test ArahPotensi';
        $school->password = Hash::make('password123');
        $school->email = 'test@arahpotensi.com';
        $school->phone = '081234567890';
        $school->address = 'Jl. Test No. 123';
        $school->city = 'Jakarta';
        $school->province = 'DKI Jakarta';
        $school->postal_code = '12345';
        $school->school_type = 'SMA';
        $school->save();
        
        echo "Test school created successfully!\n";
        echo "NPSN: 12345678\n";
        echo "Password: password123\n";
        echo "School name: " . $school->name . "\n";
    }
    
    // Tampilkan semua sekolah
    echo "\nAll schools in database:\n";
    $schools = School::all();
    foreach ($schools as $school) {
        echo "- NPSN: " . $school->npsn . ", Name: " . $school->name . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
