<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIX REMAINING INCOMPLETE MAPPINGS ===\n\n";

try {
    $subjects = DB::table('subjects')->get()->keyBy('name');
    
    // Fix specific majors
    $fixes = [
        'Seni' => 'Seni Budaya',
        'Arsitektur' => 'Fisika', 
        'Desain' => 'Bahasa Inggris'
    ];
    
    foreach ($fixes as $majorName => $subjectName) {
        $major = DB::table('major_recommendations')->where('major_name', $majorName)->first();
        if ($major) {
            $subject = $subjects->get($subjectName);
            if ($subject) {
                // Check if mapping already exists
                $existing = DB::table('major_subject_mappings')
                    ->where('major_id', $major->id)
                    ->where('subject_id', $subject->id)
                    ->first();
                
                if (!$existing) {
                    DB::table('major_subject_mappings')->insert([
                        'major_id' => $major->id,
                        'subject_id' => $subject->id,
                        'education_level' => 'SMA/MA',
                        'mapping_type' => 'pilihan',
                        'priority' => 2,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    echo "✅ {$majorName}: Ditambahkan {$subjectName}\n";
                } else {
                    echo "⚠️  {$majorName}: {$subjectName} sudah ada\n";
                }
            }
        }
    }
    
    // Final verification
    echo "\nVerifikasi final:\n";
    $incomplete = DB::table('major_subject_mappings')
        ->join('major_recommendations', 'major_subject_mappings.major_id', '=', 'major_recommendations.id')
        ->where('major_subject_mappings.education_level', 'SMA/MA')
        ->where('major_subject_mappings.mapping_type', 'pilihan')
        ->select('major_recommendations.major_name', DB::raw('COUNT(*) as count'))
        ->groupBy('major_recommendations.major_name')
        ->havingRaw('COUNT(*) < 2')
        ->get();
    
    if ($incomplete->count() == 0) {
        echo "✅ Semua jurusan SMA/MA sekarang memiliki minimal 2 mata pelajaran pilihan!\n";
        
        // Show final distribution
        echo "\nDistribusi mata pelajaran pilihan:\n";
        $distribution = DB::table('major_subject_mappings')
            ->join('subjects', 'major_subject_mappings.subject_id', '=', 'subjects.id')
            ->where('mapping_type', 'pilihan')
            ->select('subjects.name', DB::raw('COUNT(*) as count'))
            ->groupBy('subjects.name')
            ->orderBy('count', 'desc')
            ->get();
        
        foreach ($distribution as $dist) {
            echo "   {$dist->name}: {$dist->count} jurusan\n";
        }
    } else {
        echo "❌ Masih ada " . $incomplete->count() . " jurusan yang belum lengkap:\n";
        foreach ($incomplete as $inc) {
            echo "   - {$inc->major_name} ({$inc->count} mata pelajaran)\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
