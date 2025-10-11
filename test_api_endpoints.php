<?php
// =====================================================
// Test API Endpoints Production
// =====================================================

echo "==========================================\n";
echo "Testing API Endpoints Production\n";
echo "==========================================\n\n";

$base_url = "http://103.23.198.101/super-admin/api";

// Function to test endpoint
function test_endpoint($method, $url, $description, $data = null) {
    echo "🔍 Testing: $description\n";
    echo "📍 $method $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    if ($method === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ cURL Error: $error\n";
    } elseif ($http_code >= 200 && $http_code < 300) {
        echo "✅ Status: $http_code\n";
        $json = json_decode($response, true);
        if ($json) {
            echo "📄 Response: " . json_encode($json, JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "📄 Response: $response\n";
        }
    } else {
        echo "❌ Status: $http_code\n";
        echo "📄 Response: $response\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

echo "==========================================\n";
echo "1. STUDENT WEB API (Public)\n";
echo "==========================================\n\n";

test_endpoint("GET", "$base_url/web/health", "Health Check");
test_endpoint("GET", "$base_url/web/schools", "Get Schools List");
test_endpoint("GET", "$base_url/web/majors", "Get Majors List");

echo "==========================================\n";
echo "2. PUBLIC API (SuperAdmin Integration)\n";
echo "==========================================\n\n";

test_endpoint("GET", "$base_url/public/health", "Public Health Check");
test_endpoint("GET", "$base_url/public/schools", "Public Schools List");
test_endpoint("GET", "$base_url/public/majors", "Public Majors List");

echo "==========================================\n";
echo "3. TKA SCHEDULES (Public)\n";
echo "==========================================\n\n";

test_endpoint("GET", "$base_url/web/tka-schedules", "Get TKA Schedules");
test_endpoint("GET", "$base_url/web/tka-schedules/upcoming", "Get Upcoming TKA Schedules");

echo "==========================================\n";
echo "4. SCHOOL LOGIN TEST\n";
echo "==========================================\n\n";

$login_data = [
    'npsn' => '12345678',
    'password' => 'password123'
];

test_endpoint("POST", "$base_url/school/login", "School Login", $login_data);

echo "==========================================\n";
echo "5. STUDENT REGISTER TEST\n";
echo "==========================================\n\n";

$register_data = [
    'nisn' => '1234567890',
    'name' => 'Test Student',
    'npsn_sekolah' => '12345678',
    'nama_sekolah' => 'Test School',
    'kelas' => 'X IPA 1',
    'email' => 'test@example.com',
    'phone' => '081234567890',
    'parent_phone' => '081234567891',
    'password' => 'password123'
];

test_endpoint("POST", "$base_url/web/register-student", "Student Register", $register_data);

echo "==========================================\n";
echo "Testing Complete!\n";
echo "==========================================\n\n";

echo "🔧 If you see 404 errors, run these commands on VPS:\n";
echo "ssh root@103.23.198.101\n";
echo "cd /var/www/html/super-admin\n";
echo "php artisan cache:clear\n";
echo "php artisan config:clear\n";
echo "php artisan route:clear\n";
echo "php artisan view:clear\n";
echo "php artisan optimize\n";
echo "chown -R www-data:www-data storage bootstrap/cache\n";
echo "chmod -R 775 storage bootstrap/cache\n";
echo "systemctl restart apache2\n\n";

echo "🔍 Check logs if still having issues:\n";
echo "tail -f /var/www/html/super-admin/storage/logs/laravel.log\n";
echo "tail -f /var/log/apache2/error.log\n\n";

echo "📋 List all routes:\n";
echo "php artisan route:list | grep api\n";
?>