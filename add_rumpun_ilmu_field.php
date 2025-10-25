<?php

// Simple script to add rumpun_ilmu field to major_recommendations table
$host = 'localhost';
$dbname = 'campusway_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n";
    
    // Check if rumpun_ilmu column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM major_recommendations LIKE 'rumpun_ilmu'");
    $columnExists = $stmt->fetch();
    
    if (!$columnExists) {
        echo "Adding rumpun_ilmu column...\n";
        $pdo->exec("ALTER TABLE major_recommendations ADD COLUMN rumpun_ilmu VARCHAR(255) NULL AFTER category");
        echo "Column added successfully.\n";
    } else {
        echo "Column rumpun_ilmu already exists.\n";
    }
    
    // Update rumpun_ilmu based on category
    $mapping = [
        'Saintek' => 'ILMU ALAM',
        'Soshum' => 'ILMU SOSIAL',
        'Humaniora' => 'HUMANIORA',
        'Ilmu Terapan' => 'ILMU TERAPAN',
        'Ilmu Formal' => 'ILMU FORMAL',
        'Ilmu Alam' => 'ILMU ALAM',
        'Ilmu Sosial' => 'ILMU SOSIAL',
    ];
    
    $updated = 0;
    foreach ($mapping as $category => $rumpunIlmu) {
        $stmt = $pdo->prepare("UPDATE major_recommendations SET rumpun_ilmu = ? WHERE category = ? AND (rumpun_ilmu IS NULL OR rumpun_ilmu = '')");
        $stmt->execute([$rumpunIlmu, $category]);
        $count = $stmt->rowCount();
        
        if ($count > 0) {
            echo "Updated {$count} records with category '{$category}' to rumpun_ilmu '{$rumpunIlmu}'\n";
            $updated += $count;
        }
    }
    
    // Handle special cases for specific majors
    $specialCases = [
        'Administrasi Bisnis' => 'ILMU TERAPAN',
        'Arsitektur' => 'ILMU TERAPAN',
        'Teknik Informatika' => 'ILMU TERAPAN',
        'Teknik Mesin' => 'ILMU TERAPAN',
        'Teknik Elektro' => 'ILMU TERAPAN',
        'Akuntansi' => 'ILMU SOSIAL',
        'Manajemen' => 'ILMU SOSIAL',
        'Ekonomi' => 'ILMU SOSIAL',
        'Psikologi' => 'ILMU SOSIAL',
        'Hukum' => 'ILMU SOSIAL',
        'Pendidikan' => 'ILMU SOSIAL',
        'Komunikasi' => 'ILMU SOSIAL',
        'Seni' => 'HUMANIORA',
        'Sejarah' => 'HUMANIORA',
        'Linguistik' => 'HUMANIORA',
        'Sastra' => 'HUMANIORA',
        'Matematika' => 'ILMU FORMAL',
        'Komputer' => 'ILMU FORMAL',
        'Logika' => 'ILMU FORMAL',
        'Fisika' => 'ILMU ALAM',
        'Kimia' => 'ILMU ALAM',
        'Biologi' => 'ILMU ALAM',
        'Astronomi' => 'ILMU ALAM',
    ];
    
    foreach ($specialCases as $majorName => $rumpunIlmu) {
        $stmt = $pdo->prepare("UPDATE major_recommendations SET rumpun_ilmu = ? WHERE major_name LIKE ?");
        $stmt->execute([$rumpunIlmu, "%{$majorName}%"]);
        $count = $stmt->rowCount();
        
        if ($count > 0) {
            echo "Updated {$count} records for major '{$majorName}' to rumpun_ilmu '{$rumpunIlmu}'\n";
            $updated += $count;
        }
    }
    
    echo "\nTotal records updated: {$updated}\n";
    
    // Show some examples
    echo "\nSample data after update:\n";
    $stmt = $pdo->query("SELECT major_name, category, rumpun_ilmu FROM major_recommendations WHERE rumpun_ilmu IS NOT NULL LIMIT 10");
    $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($samples as $sample) {
        echo "- {$sample['major_name']}: {$sample['category']} → {$sample['rumpun_ilmu']}\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
