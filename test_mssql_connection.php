<?php

// Test MSSQL Connection
$server = 'localhost,1433';
$database = 'campusway_db';

echo "Testing MSSQL Connection...\n";
echo "Server: $server\n";
echo "Database: $database\n";
echo "Authentication: Windows Authentication\n\n";

try {
    // First, try to connect to master database using Windows Authentication
    $connectionString = "sqlsrv:Server=$server;Database=master;Trusted_Connection=yes";
    $pdo = new PDO($connectionString);
    echo "✓ Connected to SQL Server successfully!\n";
    
    // Check if campusway_db exists
    $stmt = $pdo->query("SELECT name FROM sys.databases WHERE name = '$database'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "✓ Database '$database' already exists.\n";
    } else {
        echo "⚠ Database '$database' does not exist. Creating it...\n";
        $pdo->exec("CREATE DATABASE [$database]");
        echo "✓ Database '$database' created successfully!\n";
    }
    
    // Now test connection to the specific database
    $connectionString = "sqlsrv:Server=$server;Database=$database;Trusted_Connection=yes";
    $pdo = new PDO($connectionString);
    echo "✓ Connected to '$database' database successfully!\n";
    
} catch (PDOException $e) {
    echo "✗ Connection failed: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting tips:\n";
    echo "1. Make sure SQL Server is running\n";
    echo "2. Check if SQL Server Authentication is enabled\n";
    echo "3. Verify username and password\n";
    echo "4. Check if SQL Server is listening on port 1433\n";
    echo "5. Try connecting with SQL Server Management Studio first\n";
}

?>
