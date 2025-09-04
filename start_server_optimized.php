<?php

/**
 * Optimized Server Startup Script
 * Menjalankan server Laravel dengan konfigurasi optimal
 */

echo "🚀 Starting TKA Super Admin Server (Optimized)...\n\n";

// Set optimal PHP settings
ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 0); // No time limit for server
ini_set('max_input_time', 300);
ini_set('max_input_vars', 3000);

// OPcache settings
if (function_exists('opcache_get_status')) {
    ini_set('opcache.enable', 1);
    ini_set('opcache.memory_consumption', 256);
    ini_set('opcache.interned_strings_buffer', 16);
    ini_set('opcache.max_accelerated_files', 20000);
    ini_set('opcache.validate_timestamps', 0);
    ini_set('opcache.save_comments', 1);
    ini_set('opcache.fast_shutdown', 1);
    echo "✅ OPcache enabled\n";
}

// Error reporting
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/storage/logs/php_errors.log');

echo "✅ PHP settings optimized\n";
echo "📊 Memory limit: " . ini_get('memory_limit') . "\n";
echo "⏱️  Max execution time: " . ini_get('max_execution_time') . "s\n\n";

// Check if Laravel is properly installed
if (!file_exists(__DIR__ . '/artisan')) {
    echo "❌ Laravel not found. Please run this script from Laravel root directory.\n";
    exit(1);
}

// Check database connection
echo "🔍 Checking database connection...\n";
try {
    $output = shell_exec('php artisan tinker --execute="DB::select(\'SELECT 1\'); echo \'Database connected successfully\';" 2>&1');
    if (strpos($output, 'Database connected successfully') !== false) {
        echo "✅ Database connection successful\n\n";
    } else {
        echo "⚠️  Database connection warning (continuing anyway)\n\n";
    }
} catch (Exception $e) {
    echo "⚠️  Database connection warning: " . $e->getMessage() . "\n\n";
}

// Clear cache for fresh start
echo "🧹 Clearing application cache...\n";
shell_exec('php artisan cache:clear 2>/dev/null');
shell_exec('php artisan config:clear 2>/dev/null');
shell_exec('php artisan route:clear 2>/dev/null');
shell_exec('php artisan view:clear 2>/dev/null');
echo "✅ Cache cleared\n\n";

// Optimize for production
echo "⚡ Optimizing for performance...\n";
shell_exec('php artisan config:cache 2>/dev/null');
shell_exec('php artisan route:cache 2>/dev/null');
shell_exec('php artisan view:cache 2>/dev/null');
echo "✅ Application optimized\n\n";

// Start server
echo "🌐 Starting Laravel development server...\n";
echo "📍 Server will be available at: http://localhost:8000\n";
echo "📍 Optimized API endpoints: http://localhost:8000/api/optimized/\n";
echo "📍 Health check: http://localhost:8000/api/optimized/health\n";
echo "⏹️  Press Ctrl+C to stop the server\n\n";

// Start the server with optimal settings
$command = 'php artisan serve --host=0.0.0.0 --port=8000';
echo "Running: {$command}\n\n";

// Execute the command
passthru($command);
