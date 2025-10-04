<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Mengisi mata pelajaran pilihan untuk setiap jurusan...\n\n";

// Mapping mata pelajaran berdasarkan rumpun ilmu
$subjectMappings = [
    // SAINS (Science)
    'SAINS' => [
        'Fisika', 'Kimia', 'Biologi', 'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris'
    ],
    
    // TEKNOLOGI (Technology) 
    'TEKNOLOGI' => [
        'Matematika', 'Fisika', 'Kimia', 'Bahasa Indonesia', 'Bahasa Inggris', 'Teknologi Informasi'
    ],
    
    // SOSIAL (Social Science)
    'SOSIAL' => [
        'Sosiologi', 'Geografi', 'Ekonomi', 'Sejarah', 'Bahasa Indonesia', 'Bahasa Inggris'
    ],
    
    // HUMANIORA (Humanities)
    'HUMANIORA' => [
        'Bahasa Indonesia', 'Bahasa Inggris', 'Sastra Indonesia', 'Antropologi', 'Sejarah', 'Seni Budaya'
    ],
    
    // KESEHATAN (Health Science)
    'KESEHATAN' => [
        'Biologi', 'Kimia', 'Fisika', 'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris'
    ],
    
    // PERTANIAN (Agricultural Science)
    'PERTANIAN' => [
        'Biologi', 'Kimia', 'Matematika', 'Fisika', 'Bahasa Indonesia', 'Bahasa Inggris'
    ]
];

// Mata pelajaran khusus untuk jurusan tertentu
$specificMappings = [
    'Matematika' => ['Matematika', 'Fisika', 'Kimia', 'Bahasa Indonesia', 'Bahasa Inggris', 'Statistika'],
    'Fisika' => ['Fisika', 'Matematika', 'Kimia', 'Bahasa Indonesia', 'Bahasa Inggris', 'Astronomi'],
    'Kimia' => ['Kimia', 'Matematika', 'Fisika', 'Biologi', 'Bahasa Indonesia', 'Bahasa Inggris'],
    'Biologi' => ['Biologi', 'Kimia', 'Matematika', 'Fisika', 'Bahasa Indonesia', 'Bahasa Inggris'],
    'Teknik' => ['Matematika', 'Fisika', 'Kimia', 'Teknologi Informasi', 'Bahasa Indonesia', 'Bahasa Inggris'],
    'Kedokteran' => ['Biologi', 'Kimia', 'Fisika', 'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris'],
    'Ekonomi' => ['Ekonomi', 'Matematika', 'Sosiologi', 'Geografi', 'Bahasa Indonesia', 'Bahasa Inggris'],
    'Hukum' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Sosiologi', 'Sejarah', 'Ekonomi', 'PPKn'],
    'Psikologi' => ['Sosiologi', 'Biologi', 'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Antropologi'],
    'Arsitektur' => ['Matematika', 'Fisika', 'Seni Budaya', 'Bahasa Indonesia', 'Bahasa Inggris', 'Geografi'],
    'Seni' => ['Seni Budaya', 'Bahasa Indonesia', 'Bahasa Inggris', 'Sastra Indonesia', 'Sejarah', 'Antropologi'],
    'Linguistik' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Sastra Indonesia', 'Antropologi', 'Sejarah', 'Filsafat'],
    'Filsafat' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Sejarah', 'Sosiologi', 'Antropologi', 'Psikologi']
];

try {
    // Ambil semua jurusan
    $majors = DB::select('SELECT id, major_name, rumpun_ilmu, optional_subjects FROM major_recommendations WHERE is_active = 1');
    
    // Ambil semua mata pelajaran
    $subjects = DB::select('SELECT id, name, education_level, subject_type FROM subjects WHERE is_active = 1');
    
    // Buat mapping nama mata pelajaran ke ID
    $subjectMap = [];
    foreach ($subjects as $subject) {
        $subjectMap[$subject->name] = $subject->id;
    }
    
    echo "Ditemukan " . count($majors) . " jurusan dan " . count($subjects) . " mata pelajaran\n\n";
    
    $totalMappings = 0;
    
    foreach ($majors as $major) {
        echo "Memproses jurusan: {$major->major_name} (Rumpun Ilmu: {$major->rumpun_ilmu})\n";
        
        // Tentukan mata pelajaran berdasarkan kategori atau nama jurusan
        $selectedSubjects = [];
        
        // Cek mapping khusus berdasarkan nama jurusan
        foreach ($specificMappings as $keyword => $subjects) {
            if (stripos($major->major_name, $keyword) !== false) {
                $selectedSubjects = $subjects;
                echo "  Menggunakan mapping khusus untuk: $keyword\n";
                break;
            }
        }
        
        // Jika tidak ada mapping khusus, gunakan mapping berdasarkan rumpun ilmu
        if (empty($selectedSubjects) && isset($subjectMappings[$major->rumpun_ilmu])) {
            $selectedSubjects = $subjectMappings[$major->rumpun_ilmu];
            echo "  Menggunakan mapping rumpun ilmu: {$major->rumpun_ilmu}\n";
        }
        
        // Jika masih kosong, gunakan mapping default
        if (empty($selectedSubjects)) {
            $selectedSubjects = ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Fisika', 'Kimia', 'Biologi'];
            echo "  Menggunakan mapping default\n";
        }
        
        // Pilih 2-4 mata pelajaran pilihan
        $chosenSubjects = array_slice($selectedSubjects, 0, rand(2, 4));
        
        // Hapus mapping lama untuk jurusan ini
        DB::delete('DELETE FROM major_subject_mappings WHERE major_id = ?', [$major->id]);
        
        // Tambahkan mapping baru
        foreach ($chosenSubjects as $index => $subjectName) {
            if (isset($subjectMap[$subjectName])) {
                $subjectId = $subjectMap[$subjectName];
                
                // Tentukan education level berdasarkan mata pelajaran
                $educationLevel = 'SMA/MA'; // Default
                foreach ($subjects as $subject) {
                    if ($subject->name === $subjectName) {
                        $educationLevel = $subject->education_level;
                        break;
                    }
                }
                
                DB::insert('INSERT INTO major_subject_mappings (major_id, subject_id, education_level, mapping_type, priority, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
                    $major->id,
                    $subjectId,
                    $educationLevel,
                    'pilihan',
                    $index + 1,
                    1,
                    now(),
                    now()
                ]);
                
                echo "  - {$subjectName} (Priority: " . ($index + 1) . ")\n";
                $totalMappings++;
            } else {
                echo "  - Mata pelajaran '{$subjectName}' tidak ditemukan\n";
            }
        }
        
        echo "\n";
    }
    
    echo "Selesai! Total mapping yang dibuat: $totalMappings\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
