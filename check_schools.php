<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking schools in database...\n";

try {
    $schoolsCount = App\Models\School::count();
    echo "Total schools: $schoolsCount\n";
    
    if ($schoolsCount > 0) {
        $firstSchool = App\Models\School::first();
        echo "First school ID: " . $firstSchool->id . "\n";
        echo "First school NPSN: " . $firstSchool->npsn . "\n";
        echo "First school Name: " . $firstSchool->name . "\n";
        
        // Test with first school's NPSN
        $npsn = $firstSchool->npsn;
        $token = base64_encode($firstSchool->id . '|' . time() . '|' . $npsn);
        echo "Test token for school $npsn: " . substr($token, 0, 50) . "...\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Done.\n";
