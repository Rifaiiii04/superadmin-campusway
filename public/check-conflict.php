<?php
echo "<h1>Super Admin vs Landing Conflict Check</h1>";

$routes = [
    '/' => 'Root',
    '/_super_admin' => 'Bypass Super Admin',
    '/_admin' => 'Bypass Admin', 
    '/super-admin-system/login' => 'Super Admin System',
    '/admin-system/login' => 'Admin System',
    '/sys-admin/login' => 'Sys Admin',
    '/landing/' => 'Landing Page'
];

foreach ($routes as $route => $desc) {
    $url = "http://103.23.198.101{$route}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    
    $isSuperAdmin = strpos($response, 'Super Admin') !== false;
    $isLanding = strpos($response, 'landing') !== false || strpos($response, 'Next.js') !== false;
    
    echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ccc;'>";
    echo "<strong>{$desc} ({$route}):</strong> HTTP {$httpCode}";
    
    if ($isSuperAdmin) {
        echo " - <span style='color: green;'>✅ SUPER ADMIN</span>";
    } elseif ($isLanding) {
        echo " - <span style='color: red;'>❌ LANDING PAGE</span>";
    } else {
        echo " - <span style='color: orange;'>⚠️ UNKNOWN</span>";
    }
    echo "</div>";
}

echo "<h2>Conclusion</h2>";
echo "<p>If Super Admin routes show Landing content, the server routing needs configuration.</p>";
?>
