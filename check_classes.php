<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 CHECKING CLASSES IN DATABASE\n";
echo "==============================\n\n";

try {
    // Check existing classes
    echo "1. Checking existing classes in database...\n";
    
    $classes = \App\Models\Student::select('kelas')->distinct()->get()->pluck('kelas');
    
    echo "   Found " . $classes->count() . " unique classes:\n";
    foreach ($classes as $kelas) {
        echo "   - {$kelas}\n";
    }
    
    // Check if there are any IPA classes (should not exist in SMK)
    $ipaClasses = $classes->filter(function($kelas) {
        return strpos($kelas, 'IPA') !== false;
    });
    
    if ($ipaClasses->count() > 0) {
        echo "\n   ❌ Found IPA classes (not suitable for SMK):\n";
        foreach ($ipaClasses as $kelas) {
            echo "   - {$kelas}\n";
        }
    } else {
        echo "\n   ✅ No IPA classes found (good for SMK)\n";
    }
    
    // Check SMK-appropriate classes
    $smkClasses = $classes->filter(function($kelas) {
        return strpos($kelas, 'TKJ') !== false || 
               strpos($kelas, 'TKRO') !== false || 
               strpos($kelas, 'TKR') !== false ||
               strpos($kelas, 'TBSM') !== false ||
               strpos($kelas, 'RPL') !== false ||
               strpos($kelas, 'MM') !== false;
    });
    
    echo "\n   ✅ Found SMK-appropriate classes:\n";
    foreach ($smkClasses as $kelas) {
        echo "   - {$kelas}\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
