<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Mengisi mata pelajaran pilihan untuk setiap jurusan...\n\n";

// Mapping mata pelajaran berdasarkan rumpun ilmu (menggunakan mata pelajaran yang tersedia)
$subjectMappings = [
    'SAINS' => [
        'Fisika', 'Kimia', 'Biologi', 'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris'
    ],
    'TEKNOLOGI' => [
        'Matematika', 'Fisika', 'Kimia', 'Bahasa Indonesia', 'Bahasa Inggris', 'Informatika'
    ],
    'SOSIAL' => [
        'Sosiologi', 'Geografi', 'Ekonomi', 'Bahasa Indonesia', 'Bahasa Inggris', 'PPKn/Pendidikan Pancasila'
    ],
    'HUMANIORA' => [
        'Bahasa Indonesia', 'Bahasa Inggris', 'Antropologi', 'Sosiologi', 'PPKn/Pendidikan Pancasila', 'Prakarya dan Kewirausahaan'
    ],
    'KESEHATAN' => [
        'Biologi', 'Kimia', 'Fisika', 'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris'
    ],
    'PERTANIAN' => [
        'Biologi', 'Kimia', 'Matematika', 'Fisika', 'Bahasa Indonesia', 'Bahasa Inggris'
    ]
];

// Mata pelajaran khusus untuk jurusan tertentu
$specificMappings = [
    'Matematika' => ['Matematika', 'Fisika', 'Kimia', 'Bahasa Indonesia', 'Bahasa Inggris'],
    'Fisika' => ['Fisika', 'Matematika', 'Kimia', 'Bahasa Indonesia', 'Bahasa Inggris'],
    'Kimia' => ['Kimia', 'Matematika', 'Fisika', 'Biologi', 'Bahasa Indonesia', 'Bahasa Inggris'],
    'Biologi' => ['Biologi', 'Kimia', 'Matematika', 'Fisika', 'Bahasa Indonesia', 'Bahasa Inggris'],
    'Teknik' => ['Matematika', 'Fisika', 'Kimia', 'Informatika', 'Bahasa Indonesia', 'Bahasa Inggris'],
    'Kedokteran' => ['Biologi', 'Kimia', 'Fisika', 'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris'],
    'Ekonomi' => ['Ekonomi', 'Matematika', 'Sosiologi', 'Geografi', 'Bahasa Indonesia', 'Bahasa Inggris'],
    'Hukum' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Sosiologi', 'PPKn/Pendidikan Pancasila', 'Ekonomi'],
    'Psikologi' => ['Sosiologi', 'Biologi', 'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Antropologi'],
    'Arsitektur' => ['Matematika', 'Fisika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Geografi'],
    'Seni' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Antropologi', 'Sosiologi', 'Prakarya dan Kewirausahaan'],
    'Linguistik' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Antropologi', 'Sosiologi'],
    'Filsafat' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Sosiologi', 'Antropologi']
];

try {
    // Ambil semua jurusan
    $majors = DB::select('SELECT id, major_name, rumpun_ilmu FROM major_recommendations WHERE is_active = 1');
    
    // Ambil semua mata pelajaran
    $subjects = DB::select('SELECT id, name, education_level FROM subjects WHERE is_active = 1');
    
    // Buat mapping nama mata pelajaran ke ID (ambil yang unik)
    $subjectMap = [];
    foreach ($subjects as $subject) {
        if (!isset($subjectMap[$subject->name])) {
            $subjectMap[$subject->name] = $subject->id;
        }
    }
    
    echo "Ditemukan " . count($majors) . " jurusan dan " . count($subjectMap) . " mata pelajaran unik\n\n";
    
    $totalMappings = 0;
    
    foreach ($majors as $major) {
        echo "Memproses jurusan: {$major->major_name} (Rumpun Ilmu: {$major->rumpun_ilmu})\n";
        
        // Tentukan mata pelajaran berdasarkan nama jurusan atau rumpun ilmu
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
        
        // Pilih 2-4 mata pelajaran pilihan yang tersedia
        $availableSubjects = [];
        foreach ($selectedSubjects as $subjectName) {
            if (isset($subjectMap[$subjectName])) {
                $availableSubjects[] = $subjectName;
            }
        }
        
        // Pilih 2-4 mata pelajaran
        $chosenSubjects = array_slice($availableSubjects, 0, min(4, max(2, count($availableSubjects))));
        
        // Hapus mapping lama untuk jurusan ini
        DB::delete('DELETE FROM major_subject_mappings WHERE major_id = ?', [$major->id]);
        
        // Tambahkan mapping baru
        foreach ($chosenSubjects as $index => $subjectName) {
            $subjectId = $subjectMap[$subjectName];
            
            // Tentukan education level berdasarkan mata pelajaran
            $educationLevel = 'SMA/MA'; // Default
            foreach ($subjects as $subject) {
                if (is_object($subject) && $subject->name === $subjectName) {
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
        }
        
        echo "\n";
    }
    
    echo "Selesai! Total mapping yang dibuat: $totalMappings\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

?>
