<?php

/**
 * Script untuk mengoptimalkan koneksi database
 * Menjalankan optimasi yang diperlukan untuk performa yang lebih baik
 */

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "🚀 Starting Database Optimization...\n\n";

    // Disable query logging untuk performa
    DB::disableQueryLog();

    // Optimize database connection settings
    $connection = DB::connection();
    
    echo "📊 Current Database Configuration:\n";
    echo "- Connection: " . $connection->getDriverName() . "\n";
    echo "- Database: " . $connection->getDatabaseName() . "\n";
    echo "- Host: " . $connection->getConfig('host') . "\n";
    echo "- Port: " . $connection->getConfig('port') . "\n\n";

    // Test connection
    echo "🔍 Testing database connection...\n";
    $startTime = microtime(true);
    
    try {
        $result = DB::select('SELECT 1 as test');
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;
        
        echo "✅ Database connection successful!\n";
        echo "⏱️  Response time: " . round($responseTime, 2) . "ms\n\n";
        
        if ($responseTime > 1000) {
            echo "⚠️  Warning: Database response time is slow (>1s)\n";
            echo "   Consider optimizing database server or network connection.\n\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "\n";
        exit(1);
    }

    // Check table sizes and indexes
    echo "📈 Checking table statistics...\n";
    
    $tables = ['students', 'major_recommendations', 'student_choices', 'schools'];
    
    foreach ($tables as $table) {
        try {
            $count = DB::table($table)->count();
            echo "- {$table}: {$count} records\n";
        } catch (Exception $e) {
            echo "- {$table}: Error - " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n";

    // Test query performance
    echo "⚡ Testing query performance...\n";
    
    $testQueries = [
        'Student count' => 'SELECT COUNT(*) FROM students',
        'Major count' => 'SELECT COUNT(*) FROM major_recommendations WHERE is_active = 1',
        'Student choices count' => 'SELECT COUNT(*) FROM student_choices',
        'Student with school join' => 'SELECT s.name, sc.name as school_name FROM students s JOIN schools sc ON s.school_id = sc.id LIMIT 10'
    ];
    
    foreach ($testQueries as $name => $query) {
        $startTime = microtime(true);
        try {
            $result = DB::select($query);
            $endTime = microtime(true);
            $queryTime = ($endTime - $startTime) * 1000;
            
            echo "- {$name}: " . round($queryTime, 2) . "ms\n";
            
            if ($queryTime > 500) {
                echo "  ⚠️  Slow query detected!\n";
            }
        } catch (Exception $e) {
            echo "- {$name}: Error - " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n";

    // Memory usage check
    echo "💾 Memory Usage Check:\n";
    $memoryUsage = memory_get_usage(true);
    $memoryPeak = memory_get_peak_usage(true);
    
    echo "- Current memory usage: " . $this->formatBytes($memoryUsage) . "\n";
    echo "- Peak memory usage: " . $this->formatBytes($memoryPeak) . "\n";
    echo "- Memory limit: " . ini_get('memory_limit') . "\n\n";

    // Recommendations
    echo "💡 Optimization Recommendations:\n";
    echo "1. Ensure database server has adequate resources\n";
    echo "2. Check network latency between application and database\n";
    echo "3. Consider adding database indexes for frequently queried columns\n";
    echo "4. Use database connection pooling if available\n";
    echo "5. Monitor slow query log for optimization opportunities\n\n";

    echo "✅ Database optimization check completed!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}

/**
 * Format bytes to human readable format
 */
function formatBytes($bytes, $precision = 2)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}
