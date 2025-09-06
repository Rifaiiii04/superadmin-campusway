<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking TKA Schedule details...\n\n";

// Get schedule ID 3
$schedule = DB::table('tka_schedules')->where('id', 3)->first();

if ($schedule) {
    echo "📋 TKA Schedule ID 3:\n";
    echo "  - ID: {$schedule->id}\n";
    echo "  - Title: {$schedule->title}\n";
    echo "  - Start Date: {$schedule->start_date}\n";
    echo "  - End Date: {$schedule->end_date}\n";
    echo "  - Status: {$schedule->status}\n";
    echo "  - Type: {$schedule->type}\n";
    echo "  - Is Active: " . ($schedule->is_active ? 'YES' : 'NO') . "\n";
    echo "  - Target Schools: {$schedule->target_schools}\n";
    echo "  - Created By: {$schedule->created_by}\n";
    
    // Check if target_schools is null or empty array
    $targetSchools = json_decode($schedule->target_schools, true);
    echo "  - Target Schools (decoded): " . json_encode($targetSchools) . "\n";
    echo "  - Target Schools is null: " . (is_null($targetSchools) ? 'YES' : 'NO') . "\n";
    echo "  - Target Schools is empty: " . (empty($targetSchools) ? 'YES' : 'NO') . "\n";
    
} else {
    echo "❌ Schedule ID 3 not found\n";
}

echo "\n" . str_repeat("=", 50) . "\n";

// Test the forSchool scope logic
echo "🧪 Testing forSchool scope logic...\n";

$schoolId = 4;
echo "  - School ID: {$schoolId}\n";

// Simulate the forSchool scope logic
$query = DB::table('tka_schedules')
    ->where('is_active', true)
    ->where('start_date', '>', now())
    ->where(function($q) use ($schoolId) {
        $q->whereNull('target_schools')
          ->orWhereJsonContains('target_schools', $schoolId);
    });

$result = $query->get();

echo "  - Query result count: " . $result->count() . "\n";

if ($result->count() > 0) {
    foreach ($result as $schedule) {
        echo "    - Schedule ID: {$schedule->id}, Title: {$schedule->title}\n";
    }
} else {
    echo "    - No schedules found for school ID {$schoolId}\n";
}

echo "\n" . str_repeat("=", 50) . "\n";

// Test without school filter
echo "🧪 Testing without school filter...\n";

$query2 = DB::table('tka_schedules')
    ->where('is_active', true)
    ->where('start_date', '>', now());

$result2 = $query2->get();

echo "  - Query result count: " . $result2->count() . "\n";

if ($result2->count() > 0) {
    foreach ($result2 as $schedule) {
        echo "    - Schedule ID: {$schedule->id}, Title: {$schedule->title}\n";
        echo "      Target Schools: {$schedule->target_schools}\n";
    }
}
