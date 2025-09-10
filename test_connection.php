<?php
/**
 * Script untuk testing koneksi database SQL Server
 * Jalankan dengan: php test_connection.php
 */

require_once 'vendor/autoload.php';

// Konfigurasi database
$config = [
    'host' => 'localhost',
    'port' => '1433',
    'database' => 'tka_database', // Ganti dengan nama database yang benar
    'username' => 'your_username', // Ganti dengan username yang benar
    'password' => 'your_password', // Ganti dengan password yang benar
];

try {
    echo "Mencoba koneksi ke SQL Server...\n";
    echo "Host: {$config['host']}:{$config['port']}\n";
    echo "Database: {$config['database']}\n";
    echo "Username: {$config['username']}\n\n";

    // Buat connection string
    $connectionString = "sqlsrv:Server={$config['host']},{$config['port']};Database={$config['database']}";
    
    // Opsi koneksi dengan timeout yang lebih panjang
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $pdo = new PDO($connectionString, $config['username'], $config['password'], $options);
    
    echo "✅ Koneksi berhasil!\n";
    
    // Test query sederhana
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM major_recommendations WHERE is_active = 1");
    $result = $stmt->fetch();
    
    echo "✅ Query test berhasil!\n";
    echo "Total major_recommendations aktif: " . $result['total'] . "\n";
    
} catch (PDOException $e) {
    echo "❌ Error koneksi: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n";
    
    // Saran troubleshooting
    echo "\n🔧 Saran troubleshooting:\n";
    echo "1. Pastikan SQL Server sedang berjalan\n";
    echo "2. Periksa konfigurasi host dan port\n";
    echo "3. Pastikan username dan password benar\n";
    echo "4. Periksa firewall dan network connectivity\n";
    echo "5. Pastikan ODBC Driver 17 for SQL Server terinstall\n";
    
} catch (Exception $e) {
    echo "❌ Error umum: " . $e->getMessage() . "\n";
}
