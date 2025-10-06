<?php
/**
 * Production Setup Test Script
 * Run this script to verify all production configurations are working correctly
 */

echo "🧪 TKA Super Admin Production Setup Test\n";
echo "=====================================\n\n";

$baseUrl = 'http://103.23.198.101/super-admin';
$errors = [];
$warnings = [];

// Test 1: Check if application is accessible
echo "1. Testing application accessibility...\n";
$response = @file_get_contents($baseUrl);
if ($response === false) {
    $errors[] = "❌ Application not accessible at $baseUrl";
} else {
    echo "✅ Application is accessible\n";
}

// Test 2: Check API endpoints
echo "\n2. Testing API endpoints...\n";
$apiEndpoints = [
    '/api/optimized/health',
    '/api/web/schools',
    '/api/web/majors',
    '/api/school-level/majors'
];

foreach ($apiEndpoints as $endpoint) {
    $url = $baseUrl . $endpoint;
    $response = @file_get_contents($url);
    if ($response === false) {
        $errors[] = "❌ API endpoint $endpoint not accessible";
    } else {
        echo "✅ API endpoint $endpoint is working\n";
    }
}

// Test 3: Check static assets
echo "\n3. Testing static assets...\n";
$staticAssets = [
    '/build/assets/app.js',
    '/build/assets/app.css'
];

foreach ($staticAssets as $asset) {
    $url = $baseUrl . $asset;
    $headers = @get_headers($url);
    if ($headers && strpos($headers[0], '200') !== false) {
        // Check MIME type
        $contentType = '';
        foreach ($headers as $header) {
            if (stripos($header, 'content-type:') === 0) {
                $contentType = $header;
                break;
            }
        }
        
        if (strpos($asset, '.js') !== false && strpos($contentType, 'application/javascript') === false) {
            $warnings[] = "⚠️  JavaScript file $asset has incorrect MIME type: $contentType";
        } elseif (strpos($asset, '.css') !== false && strpos($contentType, 'text/css') === false) {
            $warnings[] = "⚠️  CSS file $asset has incorrect MIME type: $contentType";
        } else {
            echo "✅ Static asset $asset is working with correct MIME type\n";
        }
    } else {
        $errors[] = "❌ Static asset $asset not accessible";
    }
}

// Test 4: Check database connection
echo "\n4. Testing database connection...\n";
try {
    $pdo = new PDO(
        'sqlsrv:Server=localhost;Database=tka_database',
        'your_username',
        'your_password'
    );
    echo "✅ Database connection successful\n";
} catch (PDOException $e) {
    $errors[] = "❌ Database connection failed: " . $e->getMessage();
}

// Test 5: Check file permissions
echo "\n5. Testing file permissions...\n";
$directories = [
    'storage',
    'bootstrap/cache',
    'public/build'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "✅ Directory $dir is writable\n";
        } else {
            $errors[] = "❌ Directory $dir is not writable";
        }
    } else {
        $warnings[] = "⚠️  Directory $dir does not exist";
    }
}

// Test 6: Check environment configuration
echo "\n6. Testing environment configuration...\n";
if (file_exists('.env')) {
    $env = file_get_contents('.env');
    $requiredVars = [
        'APP_URL',
        'DB_CONNECTION',
        'DB_HOST',
        'DB_DATABASE'
    ];
    
    foreach ($requiredVars as $var) {
        if (strpos($env, $var) !== false) {
            echo "✅ Environment variable $var is set\n";
        } else {
            $errors[] = "❌ Environment variable $var is missing";
        }
    }
} else {
    $errors[] = "❌ .env file not found";
}

// Test 7: Check Apache configuration
echo "\n7. Testing Apache configuration...\n";
$apacheConfig = file_get_contents('apache-config.conf');
if (strpos($apacheConfig, 'Content-Type') !== false) {
    echo "✅ Apache MIME type configuration found\n";
} else {
    $warnings[] = "⚠️  Apache MIME type configuration not found";
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "TEST SUMMARY\n";
echo str_repeat("=", 50) . "\n";

if (empty($errors) && empty($warnings)) {
    echo "🎉 All tests passed! Production setup is ready.\n";
} else {
    if (!empty($errors)) {
        echo "\n❌ ERRORS FOUND:\n";
        foreach ($errors as $error) {
            echo "   $error\n";
        }
    }
    
    if (!empty($warnings)) {
        echo "\n⚠️  WARNINGS:\n";
        foreach ($warnings as $warning) {
            echo "   $warning\n";
        }
    }
    
    echo "\n🔧 Please fix the errors before deploying to production.\n";
}

echo "\n📊 Test completed at: " . date('Y-m-d H:i:s') . "\n";
?>
