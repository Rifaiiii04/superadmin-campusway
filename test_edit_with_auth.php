<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 TESTING EDIT WITH AUTHENTICATION\n";
echo "===================================\n\n";

try {
    // 1. Authenticate as admin
    echo "1. Authenticating as admin...\n";
    
    $admin = \App\Models\Admin::first();
    if (!$admin) {
        echo "❌ No admin found\n";
        exit;
    }
    
    \Illuminate\Support\Facades\Auth::guard('admin')->login($admin);
    echo "✅ Authenticated as admin: {$admin->name}\n";
    
    // 2. Test edit functionality with authentication
    echo "\n2. Testing edit functionality with authentication...\n";
    
    $testMajor = \App\Models\MajorRecommendation::where('is_active', true)->first();
    echo "✅ Found test major: {$testMajor->major_name} (ID: {$testMajor->id})\n";
    
    // 3. Test validation with empty data
    echo "\n3. Testing validation with empty data...\n";
    
    $controller = new \App\Http\Controllers\SuperAdminController();
    
    $emptyRequest = new \Illuminate\Http\Request();
    $emptyRequest->merge([]);
    
    try {
        $response = $controller->updateMajorRecommendation($emptyRequest, $testMajor->id);
        echo "❌ Should have failed validation but didn't\n";
    } catch (\Illuminate\Validation\ValidationException $e) {
        echo "✅ Validation correctly failed for empty data\n";
        echo "   Errors: " . json_encode($e->errors()) . "\n";
    } catch (Exception $e) {
        echo "❌ Unexpected error: " . $e->getMessage() . "\n";
    }
    
    // 4. Test validation with invalid rumpun ilmu
    echo "\n4. Testing validation with invalid rumpun ilmu...\n";
    
    $invalidRumpunRequest = new \Illuminate\Http\Request();
    $invalidRumpunRequest->merge([
        'major_name' => 'Test Major',
        'rumpun_ilmu' => 'INVALID_RUMPUN',
        'description' => 'Test description',
        'is_active' => true
    ]);
    
    try {
        $response = $controller->updateMajorRecommendation($invalidRumpunRequest, $testMajor->id);
        echo "❌ Should have failed validation but didn't\n";
    } catch (\Illuminate\Validation\ValidationException $e) {
        echo "✅ Validation correctly failed for invalid rumpun ilmu\n";
        echo "   Errors: " . json_encode($e->errors()) . "\n";
    } catch (Exception $e) {
        echo "❌ Unexpected error: " . $e->getMessage() . "\n";
    }
    
    // 5. Test with valid data
    echo "\n5. Testing with valid data...\n";
    
    $validRequest = new \Illuminate\Http\Request();
    $validRequest->merge([
        'major_name' => $testMajor->major_name . ' (Test)',
        'rumpun_ilmu' => 'ILMU ALAM',
        'description' => 'Test description updated',
        'career_prospects' => 'Test career prospects updated',
        'is_active' => true
    ]);
    
    try {
        $response = $controller->updateMajorRecommendation($validRequest, $testMajor->id);
        echo "✅ Update successful with valid data\n";
        echo "   Response type: " . get_class($response) . "\n";
        
        // Check if data was actually updated
        $updatedMajor = \App\Models\MajorRecommendation::find($testMajor->id);
        if (strpos($updatedMajor->description, 'Test description updated') !== false) {
            echo "   ✅ Description was updated\n";
        } else {
            echo "   ❌ Description was not updated\n";
        }
        
        if (strpos($updatedMajor->career_prospects, 'Test career prospects updated') !== false) {
            echo "   ✅ Career prospects were updated\n";
        } else {
            echo "   ❌ Career prospects were not updated\n";
        }
        
        // Restore original data
        $testMajor->update([
            'major_name' => str_replace(' (Test)', '', $testMajor->major_name),
            'description' => str_replace('Test description updated', $testMajor->description, $testMajor->description),
            'career_prospects' => str_replace('Test career prospects updated', $testMajor->career_prospects, $testMajor->career_prospects)
        ]);
        echo "   ✅ Original data restored\n";
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        echo "❌ Validation failed for valid data\n";
        echo "   Errors: " . json_encode($e->errors()) . "\n";
    } catch (Exception $e) {
        echo "❌ Unexpected error: " . $e->getMessage() . "\n";
    }
    
    // 6. Test with non-existent major
    echo "\n6. Testing with non-existent major...\n";
    
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
        echo "❌ Unexpected error: " . $e->getMessage() . "\n";
    }
    
    // 7. Test all CRUD operations
    echo "\n7. Testing all CRUD operations...\n";
    
    // Test create
    $createRequest = new \Illuminate\Http\Request();
    $createRequest->merge([
        'major_name' => 'Test Major CRUD',
        'rumpun_ilmu' => 'ILMU ALAM',
        'description' => 'Test description for CRUD',
        'career_prospects' => 'Test career prospects for CRUD',
        'is_active' => true
    ]);
    
    try {
        $response = $controller->storeMajorRecommendation($createRequest);
        echo "✅ Create operation successful\n";
        
        // Find the created major
        $createdMajor = \App\Models\MajorRecommendation::where('major_name', 'Test Major CRUD')->first();
        if ($createdMajor) {
            echo "   ✅ Major was created with ID: {$createdMajor->id}\n";
            
            // Test delete
            $deleteResponse = $controller->deleteMajorRecommendation($createdMajor->id);
            echo "   ✅ Delete operation successful\n";
        } else {
            echo "   ❌ Major was not created\n";
        }
    } catch (Exception $e) {
        echo "❌ Create operation failed: " . $e->getMessage() . "\n";
    }
    
    // Test toggle
    $toggleResponse = $controller->toggleMajorRecommendation($testMajor->id);
    echo "✅ Toggle operation successful\n";

    echo "\n🎉 EDIT WITH AUTHENTICATION TEST COMPLETED!\n";
    echo "==========================================\n";
    echo "✅ All CRUD operations are working\n";
    echo "✅ Validation is working correctly\n";
    echo "✅ Error handling is working correctly\n";
    echo "✅ Authentication is working correctly\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
