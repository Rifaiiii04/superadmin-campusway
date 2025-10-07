<?php
echo "<h1>Application Comparison</h1>";

// Check current application
echo "<h2>Current Application Info</h2>";
echo "Directory: " . base_path() . "<br>";
echo "URL: " . url('/') . "<br>";
echo "Application Name: " . config('app.name', 'Not set') . "<br>";

// Test routes
$routes = [
    '/' => 'Root',
    '/super-admin/login' => 'Super Admin Login',
    '/landing' => 'Landing Page',
    '/test-route' => 'Test Route'
];

echo "<h2>Route Testing</h2>";
foreach ($routes as $route => $description) {
    $url = "http://103.23.198.101{$route}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    
    echo "<strong>{$description} ({$route}):</strong> HTTP {$httpCode}";
    if ($effectiveUrl != $url) {
        echo " → Redirects to: {$effectiveUrl}";
    }
    echo "<br>";
}

// Check if this is Super Admin or Landing
echo "<h2>Application Identification</h2>";
$superAdminIndicators = [
    'base_path_contains_superadmin' => str_contains(base_path(), 'superadmin'),
    'has_super_admin_routes' => Route::has('super-admin.login'),
    'test_route_works' => false
];

// Test our test route
$testUrl = "http://103.23.198.101/test-route";
$ch = curl_init($testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$superAdminIndicators['test_route_works'] = ($httpCode == 200 && str_contains($response, 'Super Admin'));

foreach ($superAdminIndicators as $indicator => $value) {
    echo "{$indicator}: " . ($value ? "✅ YES" : "❌ NO") . "<br>";
}

if ($superAdminIndicators['base_path_contains_superadmin'] && $superAdminIndicators['has_super_admin_routes']) {
    echo "<h3 style='color: green;'>✅ This is the SUPER ADMIN application</h3>";
} else {
    echo "<h3 style='color: red;'>❌ This is NOT the Super Admin application</h3>";
}
?>
