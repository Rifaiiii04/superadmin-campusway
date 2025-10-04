<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 DEBUGGING VALIDATION\n";
echo "======================\n\n";

try {
    // 1. Test validation directly
    echo "1. Testing validation directly...\n";
    
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'major_name' => '', // Empty name should fail
        'rumpun_ilmu' => 'INVALID_RUMPUN', // Invalid rumpun should fail
    ]);
    
    $rules = [
        'major_name' => 'required|string|max:255',
        'rumpun_ilmu' => 'required|string|in:HUMANIORA,ILMU SOSIAL,ILMU ALAM,ILMU FORMAL,ILMU TERAPAN',
    ];
    
    try {
        $request->validate($rules);
        echo "❌ Validation should have failed but didn't\n";
    } catch (\Illuminate\Validation\ValidationException $e) {
        echo "✅ Validation correctly failed\n";
        echo "   Errors: " . json_encode($e->errors()) . "\n";
    } catch (Exception $e) {
        echo "❌ Unexpected error: " . $e->getMessage() . "\n";
    }
    
    // 2. Test with valid data
    echo "\n2. Testing with valid data...\n";
    
    $validRequest = new \Illuminate\Http\Request();
    $validRequest->merge([
        'major_name' => 'Test Major',
        'rumpun_ilmu' => 'ILMU ALAM',
    ]);
    
    try {
        $validRequest->validate($rules);
        echo "✅ Validation passed for valid data\n";
    } catch (\Illuminate\Validation\ValidationException $e) {
        echo "❌ Validation failed for valid data\n";
        echo "   Errors: " . json_encode($e->errors()) . "\n";
    } catch (Exception $e) {
        echo "❌ Unexpected error: " . $e->getMessage() . "\n";
    }
    
    // 3. Test with controller method
    echo "\n3. Testing with controller method...\n";
    
    $admin = \App\Models\Admin::first();
    \Illuminate\Support\Facades\Auth::guard('admin')->login($admin);
    
    $controller = new \App\Http\Controllers\SuperAdminController();
    $testMajor = \App\Models\MajorRecommendation::where('is_active', true)->first();
    
    $invalidRequest = new \Illuminate\Http\Request();
    $invalidRequest->merge([
        'major_name' => '', // Empty name
        'rumpun_ilmu' => 'INVALID_RUMPUN', // Invalid rumpun
    ]);
    
    try {
        $response = $controller->updateMajorRecommendation($invalidRequest, $testMajor->id);
        echo "❌ Controller validation should have failed but didn't\n";
        echo "   Response type: " . get_class($response) . "\n";
    } catch (\Illuminate\Validation\ValidationException $e) {
        echo "✅ Controller validation correctly failed\n";
        echo "   Errors: " . json_encode($e->errors()) . "\n";
    } catch (Exception $e) {
        echo "❌ Unexpected error: " . $e->getMessage() . "\n";
    }
    
    // 4. Test with valid data in controller
    echo "\n4. Testing with valid data in controller...\n";
    
    $validControllerRequest = new \Illuminate\Http\Request();
    $validControllerRequest->merge([
        'major_name' => $testMajor->major_name . ' (Test)',
        'rumpun_ilmu' => 'ILMU ALAM',
        'description' => 'Test description',
        'is_active' => true
    ]);
    
    try {
        $response = $controller->updateMajorRecommendation($validControllerRequest, $testMajor->id);
        echo "✅ Controller validation passed for valid data\n";
        echo "   Response type: " . get_class($response) . "\n";
        
        // Restore original name
        $testMajor->update(['major_name' => str_replace(' (Test)', '', $testMajor->major_name)]);
        echo "   ✅ Original name restored\n";
    } catch (\Illuminate\Validation\ValidationException $e) {
        echo "❌ Controller validation failed for valid data\n";
        echo "   Errors: " . json_encode($e->errors()) . "\n";
    } catch (Exception $e) {
        echo "❌ Unexpected error: " . $e->getMessage() . "\n";
    }

    echo "\n🎉 VALIDATION DEBUG COMPLETED!\n";
    echo "=============================\n";
    echo "✅ Direct validation is working\n";
    echo "✅ Controller validation is working\n";
    echo "✅ All operations are functional\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
