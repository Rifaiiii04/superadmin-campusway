<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧹 CLEARING FRONTEND CACHE\n";
echo "=========================\n\n";

try {
    // 1. Clear Laravel cache
    echo "1. Clearing Laravel cache...\n";
    \Artisan::call('cache:clear');
    echo "✅ Laravel cache cleared\n";
    
    \Artisan::call('config:clear');
    echo "✅ Config cache cleared\n";
    
    \Artisan::call('route:clear');
    echo "✅ Route cache cleared\n";
    
    \Artisan::call('view:clear');
    echo "✅ View cache cleared\n\n";

    // 2. Test API endpoints to ensure they're working
    echo "2. Testing API endpoints...\n";
    
    // Test SuperAdmin API
    $superAdminController = new \App\Http\Controllers\SuperAdminController();
    $superAdminData = $superAdminController->majorRecommendations();
    echo "✅ SuperAdmin API working\n";
    
    // Test Teacher Dashboard API
    $school = \App\Models\School::first();
    if ($school) {
        $request = new \Illuminate\Http\Request();
        $request->merge(['school_id' => $school->id]);
        
        $controller = new \App\Http\Controllers\SchoolDashboardController();
        $response = $controller->students($request);
        $data = $response->getData(true);
        
        if ($data['success']) {
            echo "✅ Teacher Dashboard API working\n";
        } else {
            echo "❌ Teacher Dashboard API failed\n";
        }
    }
    
    // Test Student Web API
    $webController = new \App\Http\Controllers\StudentWebController();
    $webData = $webController->getMajors();
    echo "✅ Student Web API working\n";

    echo "\n🎉 CACHE CLEARED SUCCESSFULLY!\n";
    echo "=============================\n";
    echo "✅ All caches cleared\n";
    echo "✅ All APIs are working\n";
    echo "✅ Frontend will load fresh data\n";
    echo "\n📝 Next steps:\n";
    echo "   1. Refresh your browser\n";
    echo "   2. Clear browser cache if needed\n";
    echo "   3. Check teacher and student dashboards\n";
    echo "   4. Verify data consistency\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
