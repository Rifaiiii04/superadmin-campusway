<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\School;
use Illuminate\Support\Facades\Hash;

echo "=== School Account Creator ===" . PHP_EOL;
echo "Script untuk membuat akun sekolah baru" . PHP_EOL . PHP_EOL;

// Input dari user
echo "Masukkan NPSN (8 digit): ";
$npsn = trim(fgets(STDIN));

echo "Masukkan Nama Sekolah: ";
$name = trim(fgets(STDIN));

echo "Masukkan Password: ";
$password = trim(fgets(STDIN));

// Validasi input
if (strlen($npsn) !== 8 || !is_numeric($npsn)) {
    echo "Error: NPSN harus 8 digit angka!" . PHP_EOL;
    exit(1);
}

if (strlen($password) < 6) {
    echo "Error: Password minimal 6 karakter!" . PHP_EOL;
    exit(1);
}

if (empty($name)) {
    echo "Error: Nama sekolah tidak boleh kosong!" . PHP_EOL;
    exit(1);
}

// Cek apakah NPSN sudah ada
$existingSchool = School::where('npsn', $npsn)->first();
if ($existingSchool) {
    echo "Error: NPSN {$npsn} sudah terdaftar untuk sekolah: {$existingSchool->name}" . PHP_EOL;
    exit(1);
}

try {
    // Buat akun sekolah baru
    $school = School::create([
        'npsn' => $npsn,
        'name' => $name,
        'password_hash' => Hash::make($password),
    ]);

    echo PHP_EOL . "✅ Akun sekolah berhasil dibuat!" . PHP_EOL;
    echo "NPSN: {$school->npsn}" . PHP_EOL;
    echo "Nama: {$school->name}" . PHP_EOL;
    echo "Password: {$password}" . PHP_EOL;
    echo "ID: {$school->id}" . PHP_EOL . PHP_EOL;
    
    echo "Sekarang sekolah ini bisa login menggunakan:" . PHP_EOL;
    echo "NPSN: {$school->npsn}" . PHP_EOL;
    echo "Password: {$password}" . PHP_EOL;

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
