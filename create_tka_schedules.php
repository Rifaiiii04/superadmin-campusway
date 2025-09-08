<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📅 Creating TKA schedules...\n\n";

// Sample TKA schedules data
$schedules = [
    [
        'title' => 'Tes Kemampuan Akademik (TKA) - Periode Januari 2025',
        'description' => 'Tes Kemampuan Akademik untuk menentukan rekomendasi jurusan yang sesuai dengan kemampuan dan minat siswa.',
        'start_date' => now()->addDays(3)->format('Y-m-d H:i:s'),
        'end_date' => now()->addDays(3)->addHours(3)->format('Y-m-d H:i:s'),
        'status' => 'scheduled',
        'type' => 'regular',
        'is_active' => true,
        'instructions' => 'Siswa diharapkan hadir 15 menit sebelum jadwal dimulai. Bawa kartu identitas dan alat tulis.',
        'target_schools' => null, // null = semua sekolah
        'created_by' => 'Super Admin',
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'title' => 'Tes Kemampuan Akademik (TKA) - Periode Februari 2025',
        'description' => 'Tes Kemampuan Akademik untuk siswa yang belum mengikuti TKA periode sebelumnya.',
        'start_date' => now()->addDays(10)->format('Y-m-d H:i:s'),
        'end_date' => now()->addDays(10)->addHours(3)->format('Y-m-d H:i:s'),
        'status' => 'scheduled',
        'type' => 'makeup',
        'is_active' => true,
        'instructions' => 'Jadwal makeup untuk siswa yang berhalangan hadir pada jadwal reguler.',
        'target_schools' => null,
        'created_by' => 'Super Admin',
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'title' => 'Tes Kemampuan Akademik (TKA) - Khusus Kelas 12',
        'description' => 'Tes Kemampuan Akademik khusus untuk siswa kelas 12 yang akan lulus tahun ini.',
        'start_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
        'end_date' => now()->addDays(7)->addHours(2)->format('Y-m-d H:i:s'),
        'status' => 'scheduled',
        'type' => 'special',
        'is_active' => true,
        'instructions' => 'Tes khusus untuk kelas 12 dengan durasi 2 jam. Prioritas untuk siswa yang belum menentukan pilihan jurusan.',
        'target_schools' => null,
        'created_by' => 'Super Admin',
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'title' => 'Tes Kemampuan Akademik (TKA) - Periode Maret 2025',
        'description' => 'Tes Kemampuan Akademik reguler untuk periode Maret 2025.',
        'start_date' => now()->addDays(14)->format('Y-m-d H:i:s'),
        'end_date' => now()->addDays(14)->addHours(3)->format('Y-m-d H:i:s'),
        'status' => 'scheduled',
        'type' => 'regular',
        'is_active' => true,
        'instructions' => 'Tes reguler dengan durasi 3 jam. Semua siswa kelas 10-12 dapat mengikuti.',
        'target_schools' => null,
        'created_by' => 'Super Admin',
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'title' => 'Tes Kemampuan Akademik (TKA) - Periode April 2025',
        'description' => 'Tes Kemampuan Akademik untuk periode April 2025.',
        'start_date' => now()->addDays(21)->format('Y-m-d H:i:s'),
        'end_date' => now()->addDays(21)->addHours(3)->format('Y-m-d H:i:s'),
        'status' => 'scheduled',
        'type' => 'regular',
        'is_active' => true,
        'instructions' => 'Tes reguler dengan durasi 3 jam. Semua siswa kelas 10-12 dapat mengikuti.',
        'target_schools' => null,
        'created_by' => 'Super Admin',
        'created_at' => now(),
        'updated_at' => now()
    ]
];

try {
    // Clear existing schedules first
    echo "🗑️ Clearing existing TKA schedules...\n";
    DB::table('tka_schedules')->truncate();
    
    // Insert new schedules
    echo "📝 Inserting new TKA schedules...\n\n";
    
    foreach ($schedules as $index => $scheduleData) {
        $scheduleId = DB::table('tka_schedules')->insertGetId($scheduleData);
        
        echo "✅ TKA Schedule #" . ($index + 1) . " created successfully!\n";
        echo "  - ID: {$scheduleId}\n";
        echo "  - Title: {$scheduleData['title']}\n";
        echo "  - Start Date: {$scheduleData['start_date']}\n";
        echo "  - End Date: {$scheduleData['end_date']}\n";
        echo "  - Status: {$scheduleData['status']}\n";
        echo "  - Type: {$scheduleData['type']}\n";
        echo "  - Is Active: " . ($scheduleData['is_active'] ? 'YES' : 'NO') . "\n\n";
    }
    
    // Verify all schedules
    $totalSchedules = DB::table('tka_schedules')->count();
    $upcomingSchedules = DB::table('tka_schedules')
        ->where('start_date', '>', now())
        ->where('is_active', true)
        ->count();
    
    echo "📊 Summary:\n";
    echo "  - Total Schedules: {$totalSchedules}\n";
    echo "  - Upcoming Schedules: {$upcomingSchedules}\n";
    echo "  - Active Schedules: " . DB::table('tka_schedules')->where('is_active', true)->count() . "\n";
    
    echo "\n🎉 All TKA schedules created successfully!\n";
    
} catch (\Exception $e) {
    echo "❌ Error creating TKA schedules: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
