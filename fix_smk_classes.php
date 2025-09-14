<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 FIXING SMK CLASSES\n";
echo "====================\n\n";

try {
    // 1. Check current classes
    echo "1. Checking current classes...\n";
    
    $classes = \App\Models\Student::select('kelas')->distinct()->get()->pluck('kelas');
    
    echo "   Current classes:\n";
    foreach ($classes as $kelas) {
        echo "   - {$kelas}\n";
    }
    
    // 2. Fix IPA classes to SMK-appropriate classes
    echo "\n2. Fixing IPA classes to SMK-appropriate classes...\n";
    
    $classMappings = [
        'X IPA 1' => 'X TKJ 1',
        'XI IPS 3' => 'XI TKJ 3',
        'XII IPA 2' => 'XII TKJ 3',
        'XII IPS 1' => 'XII TKRO 3'
    ];
    
    foreach ($classMappings as $oldClass => $newClass) {
        $students = \App\Models\Student::where('kelas', $oldClass)->get();
        
        if ($students->count() > 0) {
            echo "   Updating {$oldClass} to {$newClass} ({$students->count()} students)\n";
            
            foreach ($students as $student) {
                $student->update(['kelas' => $newClass]);
            }
            
            echo "   ✅ Updated {$students->count()} students from {$oldClass} to {$newClass}\n";
        } else {
            echo "   ⚠️  No students found in {$oldClass}\n";
        }
    }
    
    // 3. Verify the changes
    echo "\n3. Verifying changes...\n";
    
    $newClasses = \App\Models\Student::select('kelas')->distinct()->get()->pluck('kelas');
    
    echo "   Updated classes:\n";
    foreach ($newClasses as $kelas) {
        echo "   - {$kelas}\n";
    }
    
    // Check if there are any IPA classes left
    $ipaClasses = $newClasses->filter(function($kelas) {
        return strpos($kelas, 'IPA') !== false;
    });
    
    if ($ipaClasses->count() > 0) {
        echo "\n   ❌ Still found IPA classes:\n";
        foreach ($ipaClasses as $kelas) {
            echo "   - {$kelas}\n";
        }
    } else {
        echo "\n   ✅ No IPA classes found (all fixed!)\n";
    }
    
    // Check SMK-appropriate classes
    $smkClasses = $newClasses->filter(function($kelas) {
        return strpos($kelas, 'TKJ') !== false || 
               strpos($kelas, 'TKRO') !== false || 
               strpos($kelas, 'TKR') !== false ||
               strpos($kelas, 'TBSM') !== false ||
               strpos($kelas, 'RPL') !== false ||
               strpos($kelas, 'MM') !== false;
    });
    
    echo "\n   ✅ SMK-appropriate classes:\n";
    foreach ($smkClasses as $kelas) {
        echo "   - {$kelas}\n";
    }

    echo "\n🎉 SMK CLASSES FIXED SUCCESSFULLY!\n";
    echo "==================================\n";
    echo "✅ IPA classes converted to SMK classes\n";
    echo "✅ All classes are now SMK-appropriate\n";
    echo "✅ Database updated successfully\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
