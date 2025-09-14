<?php

echo "Testing MSSQL Connection with campusway_admin...\n\n";

$server = 'localhost,1433';
$database = 'campusway_db';
$username = 'campusway_admin';
$password = 'P@ssw0rdBaru!2025';

echo "Server: $server\n";
echo "Database: $database\n";
echo "Username: $username\n";
echo "Password: " . str_repeat('*', strlen($password)) . "\n\n";

try {
    // Test connection
    $connectionString = "sqlsrv:Server=$server;Database=$database";
    $pdo = new PDO($connectionString, $username, $password);
    echo "✅ Connection successful!\n";
    
    // Test query
    $stmt = $pdo->query("SELECT @@VERSION as version");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Database version: " . substr($result['version'], 0, 50) . "...\n";
    
    // Check if campusway_db exists and has tables
    $stmt = $pdo->query("SELECT COUNT(*) as table_count FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Tables in database: " . $result['table_count'] . "\n";
    
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n\n";
    echo "Troubleshooting steps:\n";
    echo "1. Make sure SQL Server is running\n";
    echo "2. Check if user 'campusway_admin' exists in SQL Server\n";
    echo "3. Verify the password is correct\n";
    echo "4. Ensure SQL Server Authentication is enabled\n";
    echo "5. Check if user has access to 'campusway_db' database\n\n";
    echo "To create the user, run this in SQL Server Management Studio:\n";
    echo "CREATE LOGIN campusway_admin WITH PASSWORD = 'P@ssw0rdBaru!2025';\n";
    echo "USE campusway_db;\n";
    echo "CREATE USER campusway_admin FOR LOGIN campusway_admin;\n";
    echo "ALTER ROLE db_owner ADD MEMBER campusway_admin;\n";
}

?>
