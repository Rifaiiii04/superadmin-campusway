<?php
/**
 * Script untuk mengupdate semua warna biru menjadi maroon
 * Jalankan dengan: php update_colors.php
 */

echo "========================================\n";
echo "TKA SuperAdmin Color Update Script\n";
echo "Mengubah warna biru menjadi maroon\n";
echo "========================================\n\n";

$jsDir = __DIR__ . '/resources/js';
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($jsDir),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$colorMappings = [
    // Background colors
    'bg-blue-50' => 'bg-maroon-50',
    'bg-blue-100' => 'bg-maroon-100',
    'bg-blue-200' => 'bg-maroon-200',
    'bg-blue-300' => 'bg-maroon-300',
    'bg-blue-400' => 'bg-maroon-400',
    'bg-blue-500' => 'bg-maroon-600',
    'bg-blue-600' => 'bg-maroon-600',
    'bg-blue-700' => 'bg-maroon-700',
    'bg-blue-800' => 'bg-maroon-800',
    'bg-blue-900' => 'bg-maroon-900',
    
    // Text colors
    'text-blue-50' => 'text-maroon-50',
    'text-blue-100' => 'text-maroon-100',
    'text-blue-200' => 'text-maroon-200',
    'text-blue-300' => 'text-maroon-300',
    'text-blue-400' => 'text-maroon-400',
    'text-blue-500' => 'text-maroon-600',
    'text-blue-600' => 'text-maroon-600',
    'text-blue-700' => 'text-maroon-700',
    'text-blue-800' => 'text-maroon-800',
    'text-blue-900' => 'text-maroon-900',
    
    // Border colors
    'border-blue-50' => 'border-maroon-50',
    'border-blue-100' => 'border-maroon-100',
    'border-blue-200' => 'border-maroon-200',
    'border-blue-300' => 'border-maroon-300',
    'border-blue-400' => 'border-maroon-400',
    'border-blue-500' => 'border-maroon-600',
    'border-blue-600' => 'border-maroon-600',
    'border-blue-700' => 'border-maroon-700',
    'border-blue-800' => 'border-maroon-800',
    'border-blue-900' => 'border-maroon-900',
    
    // Ring colors (focus states)
    'ring-blue-500' => 'ring-maroon-500',
    'ring-blue-600' => 'ring-maroon-600',
    'ring-blue-700' => 'ring-maroon-700',
    
    // Hover states
    'hover:bg-blue-50' => 'hover:bg-maroon-50',
    'hover:bg-blue-100' => 'hover:bg-maroon-100',
    'hover:bg-blue-200' => 'hover:bg-maroon-200',
    'hover:bg-blue-300' => 'hover:bg-maroon-300',
    'hover:bg-blue-400' => 'hover:bg-maroon-400',
    'hover:bg-blue-500' => 'hover:bg-maroon-600',
    'hover:bg-blue-600' => 'hover:bg-maroon-700',
    'hover:bg-blue-700' => 'hover:bg-maroon-800',
    'hover:bg-blue-800' => 'hover:bg-maroon-900',
    'hover:bg-blue-900' => 'hover:bg-maroon-900',
    
    'hover:text-blue-50' => 'hover:text-maroon-50',
    'hover:text-blue-100' => 'hover:text-maroon-100',
    'hover:text-blue-200' => 'hover:text-maroon-200',
    'hover:text-blue-300' => 'hover:text-maroon-300',
    'hover:text-blue-400' => 'hover:text-maroon-400',
    'hover:text-blue-500' => 'hover:text-maroon-600',
    'hover:text-blue-600' => 'hover:text-maroon-700',
    'hover:text-blue-700' => 'hover:text-maroon-800',
    'hover:text-blue-800' => 'hover:text-maroon-900',
    'hover:text-blue-900' => 'hover:text-maroon-900',
    
    // Focus states
    'focus:ring-blue-500' => 'focus:ring-maroon-500',
    'focus:ring-blue-600' => 'focus:ring-maroon-600',
    'focus:ring-blue-700' => 'focus:ring-maroon-700',
    'focus:border-blue-500' => 'focus:border-maroon-500',
    'focus:border-blue-600' => 'focus:border-maroon-600',
    'focus:border-blue-700' => 'focus:border-maroon-700',
];

$updatedFiles = 0;
$totalReplacements = 0;

foreach ($files as $file) {
    if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'jsx') {
        $filePath = $file->getRealPath();
        $content = file_get_contents($filePath);
        $originalContent = $content;
        
        foreach ($colorMappings as $oldColor => $newColor) {
            $content = str_replace($oldColor, $newColor, $content);
        }
        
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            $updatedFiles++;
            
            // Count replacements
            $replacements = substr_count($originalContent, 'blue-') - substr_count($content, 'blue-');
            $totalReplacements += $replacements;
            
            echo "✅ Updated: " . str_replace(__DIR__ . '/', '', $filePath) . " ($replacements replacements)\n";
        }
    }
}

echo "\n========================================\n";
echo "Update selesai!\n";
echo "Files updated: $updatedFiles\n";
echo "Total replacements: $totalReplacements\n";
echo "========================================\n";

// Update Chart.js colors in Dashboard
$dashboardFile = __DIR__ . '/resources/js/Pages/SuperAdmin/Dashboard.jsx';
if (file_exists($dashboardFile)) {
    $content = file_get_contents($dashboardFile);
    $content = str_replace('rgba(59, 130, 246,', 'rgba(128, 0, 0,', $content);
    file_put_contents($dashboardFile, $content);
    echo "✅ Updated Chart.js colors in Dashboard\n";
}

echo "\nLangkah selanjutnya:\n";
echo "1. Jalankan: npm run build\n";
echo "2. Jalankan: php artisan serve\n";
echo "3. Buka browser untuk melihat perubahan warna\n";
