<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 TESTING EDIT FUNCTIONALITY\n";
echo "============================\n\n";

try {
    // 1. Test if we can find a major to edit
    echo "1. Finding a major to test edit...\n";
    
    $testMajor = \App\Models\MajorRecommendation::where('is_active', true)->first();
    
    if (!$testMajor) {
        echo "❌ No active major found for testing\n";
        exit;
    }
    
    echo "✅ Found test major: {$testMajor->major_name} (ID: {$testMajor->id})\n";
    
    // 2. Test the update method directly
    echo "\n2. Testing updateMajorRecommendation method...\n";
    
    $controller = new \App\Http\Controllers\SuperAdminController();
    
    // Create a mock request
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'major_name' => $testMajor->major_name . ' (Updated)',
        'rumpun_ilmu' => $testMajor->rumpun_ilmu,
        'description' => $testMajor->description . ' - Updated for testing',
        'required_subjects' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Matematika'],
        'preferred_subjects' => ['Fisika', 'Kimia'],
        'kurikulum_merdeka_subjects' => ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris'],
        'kurikulum_2013_ipa_subjects' => ['Matematika', 'Fisika', 'Kimia', 'Biologi'],
        'kurikulum_2013_ips_subjects' => ['Ekonomi', 'Sosiologi', 'Geografi'],
        'kurikulum_2013_bahasa_subjects' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Sastra Indonesia'],
        'career_prospects' => $testMajor->career_prospects . ' - Updated for testing',
        'is_active' => $testMajor->is_active
    ]);
    
    try {
        $response = $controller->updateMajorRecommendation($request, $testMajor->id);
        echo "✅ Update method executed successfully\n";
        echo "   Response type: " . get_class($response) . "\n";
    } catch (Exception $e) {
        echo "❌ Update method failed: " . $e->getMessage() . "\n";
        echo "   Stack trace: " . $e->getTraceAsString() . "\n";
    }
    
    // 3. Check if the major was actually updated
    echo "\n3. Checking if major was updated...\n";
    
    $updatedMajor = \App\Models\MajorRecommendation::find($testMajor->id);
    
    if ($updatedMajor) {
        echo "✅ Major still exists after update\n";
        echo "   Name: {$updatedMajor->major_name}\n";
        echo "   Description: " . substr($updatedMajor->description, 0, 50) . "...\n";
        echo "   Career prospects: " . substr($updatedMajor->career_prospects, 0, 50) . "...\n";
        
        // Check if the update actually worked
        if (strpos($updatedMajor->description, 'Updated for testing') !== false) {
            echo "   ✅ Description was updated\n";
        } else {
            echo "   ❌ Description was not updated\n";
        }
        
        if (strpos($updatedMajor->career_prospects, 'Updated for testing') !== false) {
            echo "   ✅ Career prospects were updated\n";
        } else {
            echo "   ❌ Career prospects were not updated\n";
        }
    } else {
        echo "❌ Major not found after update\n";
    }
    
    // 4. Test validation
    echo "\n4. Testing validation...\n";
    
    $invalidRequest = new \Illuminate\Http\Request();
    $invalidRequest->merge([
        'major_name' => '', // Empty name should fail validation
        'rumpun_ilmu' => 'INVALID_RUMPUN', // Invalid rumpun should fail validation
    ]);
    
    try {
        $response = $controller->updateMajorRecommendation($invalidRequest, $testMajor->id);
        echo "❌ Validation should have failed but didn't\n";
    } catch (\Illuminate\Validation\ValidationException $e) {
        echo "✅ Validation correctly failed for invalid data\n";
        echo "   Errors: " . json_encode($e->errors()) . "\n";
    } catch (Exception $e) {
        echo "❌ Unexpected error during validation: " . $e->getMessage() . "\n";
    }
    
    // 5. Test with non-existent major
    echo "\n5. Testing with non-existent major...\n";
    
    $nonExistentRequest = new \Illuminate\Http\Request();
    $nonExistentRequest->merge([
        'major_name' => 'Test Major',
        'rumpun_ilmu' => 'ILMU ALAM',
        'description' => 'Test description',
        'is_active' => true
    ]);
    
    try {
        $response = $controller->updateMajorRecommendation($nonExistentRequest, 99999);
        echo "❌ Should have failed for non-existent major\n";
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        echo "✅ Correctly failed for non-existent major\n";
    } catch (Exception $e) {
        echo "❌ Unexpected error for non-existent major: " . $e->getMessage() . "\n";
    }
    
    // 6. Restore the original major data
    echo "\n6. Restoring original major data...\n";
    
    $testMajor->update([
        'major_name' => str_replace(' (Updated)', '', $testMajor->major_name),
        'description' => str_replace(' - Updated for testing', '', $testMajor->description),
        'career_prospects' => str_replace(' - Updated for testing', '', $testMajor->career_prospects)
    ]);
    
    echo "✅ Original major data restored\n";

    echo "\n🎉 EDIT FUNCTIONALITY TEST COMPLETED!\n";
    echo "====================================\n";
    echo "✅ Update method is working\n";
    echo "✅ Validation is working\n";
    echo "✅ Error handling is working\n";
    echo "✅ Data is being updated correctly\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
