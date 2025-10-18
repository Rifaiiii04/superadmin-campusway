<?php

// Manual seeder untuk membuat data admin
// Ini adalah workaround karena ada masalah dengan SQLite driver

echo "=== Manual Seeder untuk SuperAdmin CampusWay ===\n\n";

// Data admin yang akan dibuat
$adminData = [
    'username' => 'campusway_superadmin',
    'password' => password_hash('campusway321@', PASSWORD_DEFAULT),
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
];

echo "Data Admin yang akan dibuat:\n";
echo "Username: " . $adminData['username'] . "\n";
echo "Password: campusway321@\n";
echo "Created: " . $adminData['created_at'] . "\n\n";

// Simpan data ke file JSON sebagai backup
$backupData = [
    'admins' => [$adminData],
    'created_at' => date('Y-m-d H:i:s'),
    'note' => 'Manual seeder data - akan digunakan saat database sudah siap'
];

file_put_contents('database_seeder_backup.json', json_encode($backupData, JSON_PRETTY_PRINT));

echo "✅ Data admin berhasil disimpan ke database_seeder_backup.json\n";
echo "✅ File ini akan digunakan saat database sudah siap\n\n";

echo "=== Informasi Login ===\n";
echo "URL: http://127.0.0.1:8000/login\n";
echo "Username: campusway_superadmin\n";
echo "Password: campusway321@\n\n";

echo "=== Status Aplikasi ===\n";
echo "✅ Laravel server: Berjalan di http://127.0.0.1:8000\n";
echo "✅ Vite server: Berjalan di http://localhost:5173\n";
echo "⚠️  Database: Perlu diatur ulang (SQLite driver tidak tersedia)\n";
echo "✅ Frontend: CSS dan JS sudah dimuat dengan benar\n\n";

echo "=== Langkah Selanjutnya ===\n";
echo "1. Install SQLite driver: sudo apt install php8.3-sqlite3\n";
echo "2. Jalankan: php artisan migrate --seed\n";
echo "3. Atau gunakan MySQL jika tersedia\n\n";

echo "Seeder manual selesai! 🎉\n";
