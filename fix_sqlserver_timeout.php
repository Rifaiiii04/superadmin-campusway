<?php
/**
 * Script untuk mengatasi masalah timeout SQL Server
 * Mencoba berbagai strategi koneksi
 */

require_once 'vendor/autoload.php';

echo "=== SQL Server Timeout Fix Script ===\n\n";

// Konfigurasi database
$config = [
    'host' => 'localhost',
    'port' => '1433',
    'database' => 'campusway_db',
    'username' => 'campusway_admin',
    'password' => 'P@ssw0rdBaru!2025',
];

// Strategi koneksi yang berbeda
$connectionStrategies = [
    'strategy1' => [
        'name' => 'Standard Connection',
        'connectionString' => "sqlsrv:Server={$config['host']},{$config['port']};Database={$config['database']}",
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 30,
        ]
    ],
    'strategy2' => [
        'name' => 'Extended Timeout',
        'connectionString' => "sqlsrv:Server={$config['host']},{$config['port']};Database={$config['database']};ConnectionTimeout=60;LoginTimeout=60",
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 60,
        ]
    ],
    'strategy3' => [
        'name' => 'IP Address Connection',
        'connectionString' => "sqlsrv:Server=127.0.0.1,{$config['port']};Database={$config['database']};ConnectionTimeout=60;LoginTimeout=60",
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 60,
        ]
    ],
    'strategy4' => [
        'name' => 'Master Database First',
        'connectionString' => "sqlsrv:Server={$config['host']},{$config['port']};Database=master;ConnectionTimeout=60;LoginTimeout=60",
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 60,
        ]
    ],
    'strategy5' => [
        'name' => 'Windows Authentication',
        'connectionString' => "sqlsrv:Server={$config['host']},{$config['port']};Database={$config['database']};Trusted_Connection=yes;ConnectionTimeout=60;LoginTimeout=60",
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 60,
        ]
    ]
];

foreach ($connectionStrategies as $strategyKey => $strategy) {
    echo "Testing {$strategy['name']}...\n";
    echo "Connection String: {$strategy['connectionString']}\n";
    
    try {
        if ($strategyKey === 'strategy5') {
            // Windows Authentication
            $pdo = new PDO($strategy['connectionString'], null, null, $strategy['options']);
        } else {
            // SQL Server Authentication
            $pdo = new PDO($strategy['connectionString'], $config['username'], $config['password'], $strategy['options']);
        }
        
        echo "✅ Koneksi berhasil!\n";
        
        // Test query
        $stmt = $pdo->query("SELECT @@VERSION as version");
        $result = $stmt->fetch();
        echo "✅ SQL Server Version: " . substr($result['version'], 0, 50) . "...\n";
        
        // Jika berhasil koneksi ke master, coba switch ke database target
        if ($strategyKey === 'strategy4') {
            try {
                $pdo->exec("USE [{$config['database']}]");
                echo "✅ Berhasil switch ke database {$config['database']}\n";
            } catch (PDOException $e) {
                echo "⚠️  Tidak bisa switch ke database {$config['database']}: " . $e->getMessage() . "\n";
            }
        }
        
        echo "✅ Strategi '{$strategy['name']}' BERHASIL!\n\n";
        
        // Jika berhasil, buat konfigurasi yang optimal
        echo "=== KONFIGURASI YANG BERHASIL ===\n";
        if ($strategyKey === 'strategy5') {
            echo "Gunakan Windows Authentication di .env:\n";
            echo "DB_CONNECTION=sqlsrv\n";
            echo "DB_HOST={$config['host']}\n";
            echo "DB_PORT={$config['port']}\n";
            echo "DB_DATABASE={$config['database']}\n";
            echo "DB_USERNAME=\n";
            echo "DB_PASSWORD=\n";
        } else {
            echo "Gunakan SQL Server Authentication di .env:\n";
            echo "DB_CONNECTION=sqlsrv\n";
            echo "DB_HOST=" . ($strategyKey === 'strategy3' ? '127.0.0.1' : $config['host']) . "\n";
            echo "DB_PORT={$config['port']}\n";
            echo "DB_DATABASE={$config['database']}\n";
            echo "DB_USERNAME={$config['username']}\n";
            echo "DB_PASSWORD={$config['password']}\n";
        }
        echo "\n";
        
        break; // Keluar dari loop jika berhasil
        
    } catch (PDOException $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        echo "Error Code: " . $e->getCode() . "\n\n";
    } catch (Exception $e) {
        echo "❌ Error umum: " . $e->getMessage() . "\n\n";
    }
}

echo "=== TROUBLESHOOTING TAMBAHAN ===\n";
echo "Jika semua strategi gagal, coba langkah berikut:\n\n";

echo "1. Periksa SQL Server Configuration Manager:\n";
echo "   - Buka SQL Server Configuration Manager\n";
echo "   - SQL Server Services → Pastikan SQL Server (MSSQLSERVER) Running\n";
echo "   - SQL Server Network Configuration → Protocols for MSSQLSERVER\n";
echo "   - Pastikan TCP/IP Enabled dan IP Addresses dikonfigurasi dengan benar\n\n";

echo "2. Periksa Windows Services:\n";
echo "   - Buka Services.msc\n";
echo "   - Pastikan 'SQL Server (MSSQLSERVER)' Running\n";
echo "   - Pastikan 'SQL Server Browser' Running\n\n";

echo "3. Test koneksi manual:\n";
echo "   - Buka Command Prompt sebagai Administrator\n";
echo "   - Jalankan: telnet localhost 1433\n";
echo "   - Jika berhasil, akan muncul blank screen\n\n";

echo "4. Periksa Firewall:\n";
echo "   - Buka Windows Defender Firewall\n";
echo "   - Allow SQL Server through firewall\n";
echo "   - Pastikan port 1433 inbound/outbound allowed\n\n";

echo "5. Restart Services:\n";
echo "   - Restart SQL Server service\n";
echo "   - Restart SQL Server Browser service\n";
echo "   - Restart Windows jika perlu\n\n";

echo "6. Periksa ODBC Driver:\n";
echo "   - Pastikan ODBC Driver 17 for SQL Server terinstall\n";
echo "   - Update driver jika perlu\n\n";

?>
