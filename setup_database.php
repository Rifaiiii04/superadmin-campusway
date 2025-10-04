<?php
/**
 * Script untuk setup konfigurasi database
 * Jalankan dengan: php setup_database.php
 */

echo "========================================\n";
echo "TKA SuperAdmin Database Setup\n";
echo "========================================\n\n";

// Input konfigurasi database
echo "Masukkan konfigurasi database SQL Server:\n";
echo "Host (default: localhost): ";
$host = trim(fgets(STDIN)) ?: 'localhost';

echo "Port (default: 1433): ";
$port = trim(fgets(STDIN)) ?: '1433';

echo "Database name: ";
$database = trim(fgets(STDIN));

if (empty($database)) {
    echo "❌ Nama database tidak boleh kosong!\n";
    exit(1);
}

echo "Username: ";
$username = trim(fgets(STDIN));

if (empty($username)) {
    echo "❌ Username tidak boleh kosong!\n";
    exit(1);
}

echo "Password: ";
$password = trim(fgets(STDIN));

echo "\nTesting koneksi...\n";

try {
    $connectionString = "sqlsrv:Server={$host},{$port};Database={$database}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $pdo = new PDO($connectionString, $username, $password, $options);
    echo "✅ Koneksi berhasil!\n";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM major_recommendations WHERE is_active = 1");
    $result = $stmt->fetch();
    echo "✅ Query test berhasil! Total records: " . $result['total'] . "\n";
    
    // Buat file .env
    $envContent = "APP_NAME=TKA_SuperAdmin
APP_ENV=local
APP_KEY=base64:your-app-key-here
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlsrv
DB_HOST={$host}
DB_PORT={$port}
DB_DATABASE={$database}
DB_USERNAME={$username}
DB_PASSWORD={$password}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=\"hello@example.com\"
MAIL_FROM_NAME=\"\${APP_NAME}\"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME=\"\${APP_NAME}\"
VITE_PUSHER_APP_KEY=\"\${PUSHER_APP_KEY}\"
VITE_PUSHER_HOST=\"\${PUSHER_HOST}\"
VITE_PUSHER_PORT=\"\${PUSHER_PORT}\"
VITE_PUSHER_SCHEME=\"\${PUSHER_SCHEME}\"
VITE_PUSHER_APP_CLUSTER=\"\${PUSHER_APP_CLUSTER}\"";

    file_put_contents('.env', $envContent);
    echo "✅ File .env berhasil dibuat!\n";
    
    echo "\nLangkah selanjutnya:\n";
    echo "1. Jalankan: php artisan key:generate\n";
    echo "2. Jalankan: php artisan serve\n";
    echo "3. Buka browser ke: http://localhost:8000\n";
    
} catch (PDOException $e) {
    echo "❌ Error koneksi: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n";
    
    echo "\n🔧 Saran troubleshooting:\n";
    echo "1. Pastikan SQL Server sedang berjalan\n";
    echo "2. Periksa konfigurasi host dan port\n";
    echo "3. Pastikan username dan password benar\n";
    echo "4. Periksa firewall dan network connectivity\n";
    echo "5. Pastikan ODBC Driver 17 for SQL Server terinstall\n";
    
} catch (Exception $e) {
    echo "❌ Error umum: " . $e->getMessage() . "\n";
}
