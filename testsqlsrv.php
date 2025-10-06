<?php
$server   = "localhost,1433"; // host,port
$database = "campusway_db";
$username = "campusway_admin";
$password = "P@ssw0rdBaru!2025";

try {
    $conn = new PDO("sqlsrv:Server=$server;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to SQL Server successfully!";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
