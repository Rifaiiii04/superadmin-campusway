<?php

/**
 * UPDATE EXISTING TKA DATA WITH PUSMENDIK FIELDS
 * 
 * This script updates existing TKA schedules to include PUSMENDIK essential fields
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\TkaSchedule;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔄 Updating existing TKA data with PUSMENDIK fields...\n";

try {
    // Get all existing TKA schedules
    $schedules = TkaSchedule::all();
    
    echo "📊 Found {$schedules->count()} TKA schedules to update\n";
    
    $updated = 0;
    $errors = 0;
    
    foreach ($schedules as $schedule) {
        try {
            // Update with PUSMENDIK fields if they don't exist
            $updateData = [];
            
            if (empty($schedule->gelombang)) {
                $updateData['gelombang'] = '1'; // Default to gelombang 1
            }
            
            if (empty($schedule->hari_pelaksanaan)) {
                $updateData['hari_pelaksanaan'] = 'Hari Pertama'; // Default to hari pertama
            }
            
            if (empty($schedule->exam_venue)) {
                $updateData['exam_venue'] = 'Sekolah'; // Default venue
            }
            
            if (empty($schedule->exam_room)) {
                $updateData['exam_room'] = 'Ruang Kelas'; // Default room
            }
            
            if (empty($schedule->contact_person)) {
                $updateData['contact_person'] = 'Guru BK'; // Default contact
            }
            
            if (empty($schedule->contact_phone)) {
                $updateData['contact_phone'] = '08xxxxxxxxxx'; // Default phone
            }
            
            if (empty($schedule->requirements)) {
                $updateData['requirements'] = 'Membawa alat tulis dan identitas diri'; // Default requirements
            }
            
            if (empty($schedule->materials_needed)) {
                $updateData['materials_needed'] = 'Pensil 2B, penghapus, penggaris'; // Default materials
            }
            
            // Only update if there are fields to update
            if (!empty($updateData)) {
                $schedule->update($updateData);
                $updated++;
                echo "✅ Updated schedule ID {$schedule->id}: {$schedule->title}\n";
            } else {
                echo "ℹ️  Schedule ID {$schedule->id} already has PUSMENDIK fields\n";
            }
            
        } catch (Exception $e) {
            $errors++;
            echo "❌ Error updating schedule ID {$schedule->id}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n📊 Update Summary:\n";
    echo "✅ Successfully updated: {$updated} schedules\n";
    echo "❌ Errors: {$errors} schedules\n";
    echo "📋 Total processed: {$schedules->count()} schedules\n";
    
    if ($updated > 0) {
        echo "\n🎉 PUSMENDIK fields have been added to existing TKA schedules!\n";
        echo "📱 Frontend will now display the new PUSMENDIK information.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n✅ Update completed successfully!\n";
