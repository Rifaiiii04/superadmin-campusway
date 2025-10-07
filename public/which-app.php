<?php
header('Content-Type: text/plain');
echo "=== APPLICATION IDENTIFICATION ===\n";
echo "Server IP: " . ($_SERVER['SERVER_ADDR'] ?? 'Unknown') . "\n";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "\n";
echo "Script Path: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'Unknown') . "\n";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "\n";
echo "PHP Self: " . ($_SERVER['PHP_SELF'] ?? 'Unknown') . "\n";
echo "Current Directory: " . __DIR__ . "\n";
echo "=== END ===\n";
?>
