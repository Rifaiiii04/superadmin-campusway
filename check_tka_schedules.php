<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking TKA Schedules...\n\n";

// Check if table exists
$tableExists = DB::getSchemaBuilder()->hasTable('tka_schedules');
echo "📊 Table 'tka_schedules' exists: " . ($tableExists ? 'YES' : 'NO') . "\n\n";

if ($tableExists) {
    // Get all schedules
    $schedules = DB::table('tka_schedules')->get();
    echo "📊 Total TKA Schedules: " . $schedules->count() . "\n\n";
    
    if ($schedules->count() > 0) {
        echo "📋 TKA Schedules:\n";
        foreach ($schedules as $schedule) {
            echo "  - ID: {$schedule->id}\n";
            echo "    Title: {$schedule->title}\n";
            echo "    Status: {$schedule->status}\n";
            echo "    Is Active: " . ($schedule->is_active ? 'YES' : 'NO') . "\n";
            echo "    Start Date: {$schedule->start_date}\n";
            echo "    End Date: {$schedule->end_date}\n";
            echo "    Created: {$schedule->created_at}\n";
            echo "    Updated: {$schedule->updated_at}\n";
            echo "    ---\n";
        }
    } else {
        echo "❌ No TKA schedules found in database\n";
    }
    
    // Check upcoming schedules (active and future dates)
    $upcomingSchedules = DB::table('tka_schedules')
        ->where('is_active', true)
        ->where('status', '!=', 'cancelled')
        ->where('start_date', '>=', now())
        ->get();
    
    echo "\n📅 Upcoming Schedules: " . $upcomingSchedules->count() . "\n";
    
    if ($upcomingSchedules->count() > 0) {
        foreach ($upcomingSchedules as $schedule) {
            echo "  - {$schedule->title} (Start: {$schedule->start_date})\n";
        }
    } else {
        echo "❌ No upcoming schedules found\n";
    }
} else {
    echo "❌ Table 'tka_schedules' does not exist\n";
}
