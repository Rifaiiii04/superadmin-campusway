<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VERIFYING SMK FIXES\n";
echo "=====================\n\n";

try {
    // 1. Check current classes in database
    echo "1. Checking current classes in database...\n";
    
    $classes = \App\Models\Student::select('kelas')->distinct()->get()->pluck('kelas');
    
    echo "   Current classes:\n";
    foreach ($classes as $kelas) {
        echo "   - {$kelas}\n";
    }
    
    // 2. Check for IPA classes (should not exist)
    echo "\n2. Checking for IPA classes (should not exist in SMK)...\n";
    
    $ipaClasses = $classes->filter(function($kelas) {
        return strpos($kelas, 'IPA') !== false;
    });
    
    if ($ipaClasses->count() > 0) {
        echo "   ❌ Found IPA classes (not suitable for SMK):\n";
        foreach ($ipaClasses as $kelas) {
            echo "   - {$kelas}\n";
        }
    } else {
        echo "   ✅ No IPA classes found (perfect for SMK!)\n";
    }
    
    // 3. Check for SMK-appropriate classes
    echo "\n3. Checking for SMK-appropriate classes...\n";
    
    $smkClasses = $classes->filter(function($kelas) {
        return strpos($kelas, 'TKJ') !== false || 
               strpos($kelas, 'TKRO') !== false || 
               strpos($kelas, 'TKR') !== false ||
               strpos($kelas, 'TBSM') !== false ||
               strpos($kelas, 'RPL') !== false ||
               strpos($kelas, 'MM') !== false ||
               strpos($kelas, 'AK') !== false;
    });
    
    if ($smkClasses->count() > 0) {
        echo "   ✅ Found SMK-appropriate classes:\n";
        foreach ($smkClasses as $kelas) {
            echo "   - {$kelas}\n";
        }
    } else {
        echo "   ⚠️  No SMK-appropriate classes found\n";
    }
    
    // 4. Check student distribution by class
    echo "\n4. Checking student distribution by class...\n";
    
    $classStats = \App\Models\Student::select('kelas')
        ->selectRaw('COUNT(*) as student_count')
        ->groupBy('kelas')
        ->orderBy('kelas')
        ->get();
    
    foreach ($classStats as $stat) {
        echo "   - {$stat->kelas}: {$stat->student_count} students\n";
    }
    
    // 5. Check if all classes are SMK-appropriate
    echo "\n5. Verifying all classes are SMK-appropriate...\n";
    
    $allSmkAppropriate = true;
    foreach ($classes as $kelas) {
        $isSmkAppropriate = strpos($kelas, 'TKJ') !== false || 
                           strpos($kelas, 'TKRO') !== false || 
                           strpos($kelas, 'TKR') !== false ||
                           strpos($kelas, 'TBSM') !== false ||
                           strpos($kelas, 'RPL') !== false ||
                           strpos($kelas, 'MM') !== false ||
                           strpos($kelas, 'AK') !== false;
        
        if (!$isSmkAppropriate) {
            echo "   ❌ {$kelas} is not SMK-appropriate\n";
            $allSmkAppropriate = false;
        }
    }
    
    if ($allSmkAppropriate) {
        echo "   ✅ All classes are SMK-appropriate!\n";
    } else {
        echo "   ❌ Some classes are not SMK-appropriate\n";
    }
    
    // 6. Summary
    echo "\n6. Summary:\n";
    echo "   Total classes: " . $classes->count() . "\n";
    echo "   IPA classes: " . $ipaClasses->count() . " (should be 0)\n";
    echo "   SMK-appropriate classes: " . $smkClasses->count() . "\n";
    echo "   Total students: " . \App\Models\Student::count() . "\n";

    echo "\n🎉 SMK FIXES VERIFICATION COMPLETED!\n";
    echo "====================================\n";
    
    if ($ipaClasses->count() === 0 && $allSmkAppropriate) {
        echo "✅ All classes are SMK-appropriate\n";
        echo "✅ No IPA classes found\n";
        echo "✅ Database is ready for SMK school\n";
        echo "\n🎉 SMK FIXES SUCCESSFUL!\n";
    } else {
        echo "❌ Some issues found - please check above\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
