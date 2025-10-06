<?php
echo "Rewrite Test<br>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "<br>";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "<br>";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'NOT SET') . "<br>";
echo "PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'NOT SET') . "<br>";

// Test if index.php is being called
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'index.php') {
    echo "✅ index.php is being executed<br>";
} else {
    echo "❌ index.php is NOT being executed<br>";
    echo "Current script: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'unknown') . "<br>";
}
