<?php
echo "<h1>Server Structure Analysis</h1>";

echo "<h2>Current Application</h2>";
echo "Path: " . base_path() . "<br>";
echo "Public: " . public_path() . "<br>";

echo "<h2>Directory Listing</h2>";
echo "<h3>Current Directory:</h3>";
$current = scandir('.');
foreach ($current as $item) {
    if ($item != '.' && $item != '..') {
        echo $item . "<br>";
    }
}

echo "<h3>Parent Directory:</h3>";
$parent = scandir('..');
foreach ($parent as $item) {
    if ($item != '.' && $item != '..') {
        echo $item . "<br>";
    }
}

echo "<h2>Apache Configuration Check</h2>";
// Cek jika kita bisa membaca konfigurasi Apache
$apacheCheck = [
    '/etc/apache2/sites-enabled/' => 'Sites Enabled',
    '/etc/apache2/apache2.conf' => 'Apache Config',
    '/etc/apache2/conf-available/' => 'Conf Available'
];

foreach ($apacheCheck as $path => $desc) {
    if (file_exists($path)) {
        echo "$desc: EXISTS<br>";
        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    echo "&nbsp;&nbsp;- $file<br>";
                }
            }
        }
    } else {
        echo "$desc: NOT FOUND<br>";
    }
}
?>
