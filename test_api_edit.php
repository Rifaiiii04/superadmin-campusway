<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing API Edit Major Recommendation...\n";

try {
    // Cari major Filsafat
    $filsafat = DB::table('major_recommendations')
        ->where('major_name', 'LIKE', '%Filsafat%')
        ->first();
    
    if ($filsafat) {
        echo "✅ Found Filsafat major (ID: {$filsafat->id})\n";
        echo "  - Current Rumpun Ilmu: {$filsafat->rumpun_ilmu}\n";
        
        // Simulate API request data
        $requestData = [
            'major_name' => $filsafat->major_name,
            'rumpun_ilmu' => 'HUMANIORA',
            'description' => $filsafat->description,
            'required_subjects' => json_decode($filsafat->required_subjects, true),
            'preferred_subjects' => json_decode($filsafat->preferred_subjects, true),
            'kurikulum_merdeka_subjects' => json_decode($filsafat->kurikulum_merdeka_subjects, true),
            'kurikulum_2013_ipa_subjects' => json_decode($filsafat->kurikulum_2013_ipa_subjects, true),
            'kurikulum_2013_ips_subjects' => json_decode($filsafat->kurikulum_2013_ips_subjects, true),
            'kurikulum_2013_bahasa_subjects' => json_decode($filsafat->kurikulum_2013_bahasa_subjects, true),
            'career_prospects' => $filsafat->career_prospects,
            'is_active' => $filsafat->is_active
        ];
        
        echo "📝 Request data prepared:\n";
        echo "  - Rumpun Ilmu: {$requestData['rumpun_ilmu']}\n";
        echo "  - Required Subjects: " . implode(', ', $requestData['required_subjects']) . "\n";
        
        // Update via database (simulating controller logic)
        $updated = DB::table('major_recommendations')
            ->where('id', $filsafat->id)
            ->update([
                'rumpun_ilmu' => $requestData['rumpun_ilmu'],
                'updated_at' => now()
            ]);
        
        if ($updated) {
            echo "✅ Successfully updated via database\n";
            
            // Verify update
            $updatedMajor = DB::table('major_recommendations')
                ->where('id', $filsafat->id)
                ->first();
            
            echo "✅ Verification - New rumpun_ilmu: {$updatedMajor->rumpun_ilmu}\n";
        } else {
            echo "❌ Failed to update\n";
        }
    } else {
        echo "❌ Filsafat major not found\n";
    }
} catch(Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n✅ API test completed!\n";
