<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking TKA Schedule ID 4...\n\n";

// Get schedule ID 4
$schedule = DB::table('tka_schedules')->where('id', 4)->first();

if ($schedule) {
    echo "📋 TKA Schedule ID 4:\n";
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
    echo "❌ Schedule ID 4 not found\n";
}

echo "\n" . str_repeat("=", 50) . "\n";

// Test the forSchool scope logic with schedule ID 4
echo "🧪 Testing forSchool scope logic with schedule ID 4...\n";

$schoolId = 4;
echo "  - School ID: {$schoolId}\n";

// Simulate the forSchool scope logic
$query = DB::table('tka_schedules')
    ->where('id', 4)
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
    echo "    - This means the forSchool scope is filtering out this schedule\n";
}
