<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📅 Creating upcoming TKA schedule...\n\n";

// Create upcoming schedule
$startDate = now()->addDays(7)->format('Y-m-d H:i:s');
$endDate = now()->addDays(14)->format('Y-m-d H:i:s');

$scheduleData = [
    'title' => 'Tes Kemampuan Akademik (TKA) - Periode ' . now()->format('M Y'),
    'description' => 'Tes Kemampuan Akademik untuk menentukan rekomendasi jurusan yang sesuai dengan kemampuan dan minat siswa.',
    'start_date' => $startDate,
    'end_date' => $endDate,
    'status' => 'scheduled',
    'is_active' => true,
    'max_participants' => 100,
    'location' => 'Laboratorium Komputer SMA',
    'instructions' => 'Siswa diharapkan hadir 15 menit sebelum jadwal dimulai. Bawa kartu identitas dan alat tulis.',
    'created_at' => now(),
    'updated_at' => now()
];

try {
    $scheduleId = DB::table('tka_schedules')->insertGetId($scheduleData);
    
    echo "✅ TKA Schedule created successfully!\n";
    echo "  - ID: {$scheduleId}\n";
    echo "  - Title: {$scheduleData['title']}\n";
    echo "  - Start Date: {$scheduleData['start_date']}\n";
    echo "  - End Date: {$scheduleData['end_date']}\n";
    echo "  - Status: {$scheduleData['status']}\n";
    echo "  - Is Active: " . ($scheduleData['is_active'] ? 'YES' : 'NO') . "\n";
    
    // Verify the schedule
    $createdSchedule = DB::table('tka_schedules')->where('id', $scheduleId)->first();
    echo "\n📋 Created schedule details:\n";
    echo "  - ID: {$createdSchedule->id}\n";
    echo "  - Title: {$createdSchedule->title}\n";
    echo "  - Start Date: {$createdSchedule->start_date}\n";
    echo "  - End Date: {$createdSchedule->end_date}\n";
    echo "  - Status: {$createdSchedule->status}\n";
    echo "  - Is Active: " . ($createdSchedule->is_active ? 'YES' : 'NO') . "\n";
    
} catch (\Exception $e) {
    echo "❌ Error creating TKA schedule: " . $e->getMessage() . "\n";
}

echo "\n📊 Total TKA Schedules now: " . DB::table('tka_schedules')->count() . "\n";
