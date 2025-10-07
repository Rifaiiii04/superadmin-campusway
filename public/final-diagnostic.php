<?php
echo "<h1>FINAL DIAGNOSTIC - SUPER ADMIN vs LANDING</h1>";

// Test semua endpoint
$endpoints = [
    '/' => 'Root',
    '/super-admin-system/login' => 'Super Admin System Login',
    '/admin-system/login' => 'Admin System Login', 
    '/super-admin-test' => 'Super Admin Test',
    '/health-check' => 'Health Check',
    '/landing/' => 'Landing Page',
    '/super-admin-direct.php' => 'Direct Access Page'
];

echo "<h2>Endpoint Testing</h2>";
foreach ($endpoints as $endpoint => $description) {
    $url = "http://103.23.198.101{$endpoint}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    
    echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ccc;'>";
    echo "<strong>{$description} ({$endpoint}):</strong> HTTP {$httpCode}";
    echo "<br>Content-Type: {$contentType}";
    
    // Analyze response
    if (strpos($response, 'Super Admin') !== false) {
        echo "<br>✅ <strong>SUPER ADMIN CONTENT DETECTED</strong>";
    } elseif (strpos($response, 'Landing') !== false || strpos($response, 'Next.js') !== false) {
        echo "<br>❌ <strong>LANDING PAGE CONTENT DETECTED</strong>";
    }
    
    if (strpos($response, '{') === 0) {
        echo "<br>📊 JSON Response";
        echo "<br><pre>" . htmlspecialchars(substr($response, 0, 200)) . "...</pre>";
    }
    
    echo "</div>";
}

echo "<h2>Conclusion</h2>";
echo "<p>If Super Admin routes return Landing page content, then the server is configured to route all requests to the Landing application.</p>";
echo "<p><strong>Solution needed:</strong> Server configuration change to separate Super Admin and Landing applications.</p>";
?>
