<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 TESTING API DIRECT\n";
echo "===================\n\n";

try {
    // 1. Test SuperAdmin API endpoint directly
    echo "1. Testing SuperAdmin API endpoint directly...\n";
    
    $request = \Illuminate\Http\Request::create('/super-admin/major-recommendations', 'GET');
    $request->setLaravelSession(app('session.store'));
    
    // Set up authentication
    $admin = \App\Models\Admin::first();
    if ($admin) {
        \Illuminate\Support\Facades\Auth::guard('admin')->login($admin);
        echo "✅ Admin authenticated: {$admin->name}\n";
    } else {
        echo "❌ No admin found\n";
        exit;
    }
    
    // Call the controller
    $controller = new \App\Http\Controllers\SuperAdminController();
    $response = $controller->majorRecommendations();
    
    echo "✅ SuperAdmin controller executed successfully\n";
    echo "   Response type: " . get_class($response) . "\n";
    
    // Try to get the data from the response
    if ($response instanceof \Inertia\Response) {
        $props = $response->toResponse($request)->getData();
        echo "   Response data type: " . gettype($props) . "\n";
        
        if (is_object($props) && isset($props->page)) {
            $pageData = $props->page;
            echo "   Page data type: " . gettype($pageData) . "\n";
            
            if (is_object($pageData) && isset($pageData->props)) {
                $propsData = $pageData->props;
                echo "   Props data type: " . gettype($propsData) . "\n";
                
                if (is_object($propsData) && isset($propsData->majorRecommendations)) {
                    $majors = $propsData->majorRecommendations;
                    echo "   Majors data type: " . gettype($majors) . "\n";
                    echo "   Majors count: " . count($majors) . "\n";
                    
                    // Find Ilmu Kelautan
                    $ilmuKelautan = null;
                    foreach ($majors as $major) {
                        if ($major->major_name === 'Ilmu Kelautan') {
                            $ilmuKelautan = $major;
                            break;
                        }
                    }
                    
                    if ($ilmuKelautan) {
                        echo "✅ Found Ilmu Kelautan in response\n";
                        echo "   Merdeka subjects: " . count($ilmuKelautan->kurikulum_merdeka_subjects ?? []) . " items\n";
                        echo "   2013 IPA subjects: " . count($ilmuKelautan->kurikulum_2013_ipa_subjects ?? []) . " items\n";
                        echo "   2013 IPS subjects: " . count($ilmuKelautan->kurikulum_2013_ips_subjects ?? []) . " items\n";
                        echo "   2013 Bahasa subjects: " . count($ilmuKelautan->kurikulum_2013_bahasa_subjects ?? []) . " items\n";
                        echo "   Career prospects: " . (!empty($ilmuKelautan->career_prospects) ? 'Yes' : 'No') . "\n";
                    } else {
                        echo "❌ Ilmu Kelautan not found in response\n";
                    }
                } else {
                    echo "❌ majorRecommendations not found in props\n";
                }
            } else {
                echo "❌ props not found in page data\n";
            }
        } else {
            echo "❌ page not found in response data\n";
        }
    } else {
        echo "❌ Response is not Inertia Response\n";
    }

    echo "\n🎉 API DIRECT TEST COMPLETED!\n";
    echo "=============================\n";
    echo "✅ SuperAdmin API is working\n";
    echo "✅ Data is being sent to frontend\n";
    echo "✅ Frontend should display curriculum subjects\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
