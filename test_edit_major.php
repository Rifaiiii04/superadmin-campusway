<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing Edit Major Recommendation...\n";

try {
    // Cari major Filsafat
    $filsafat = DB::table('major_recommendations')
        ->where('major_name', 'LIKE', '%Filsafat%')
        ->first();
    
    if ($filsafat) {
        echo "✅ Found Filsafat major:\n";
        echo "  - ID: " . $filsafat->id . "\n";
        echo "  - Name: " . $filsafat->major_name . "\n";
        echo "  - Current Rumpun Ilmu: " . $filsafat->rumpun_ilmu . "\n";
        
        // Update rumpun ilmu ke HUMANIORA
        $updated = DB::table('major_recommendations')
            ->where('id', $filsafat->id)
            ->update(['rumpun_ilmu' => 'HUMANIORA']);
        
        if ($updated) {
            echo "✅ Successfully updated rumpun_ilmu to HUMANIORA\n";
            
            // Verify update
            $updatedMajor = DB::table('major_recommendations')
                ->where('id', $filsafat->id)
                ->first();
            
            echo "✅ Verification - New rumpun_ilmu: " . $updatedMajor->rumpun_ilmu . "\n";
        } else {
            echo "❌ Failed to update\n";
        }
    } else {
        echo "❌ Filsafat major not found\n";
    }
} catch(Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n✅ Test completed!\n";
