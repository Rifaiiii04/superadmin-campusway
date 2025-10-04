<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== POPULATE SUBJECT MAPPINGS V3 (Sesuai Peraturan PUSMENDIK) ===\n\n";

try {
    // Hapus semua mapping lama
    echo "1. Menghapus mapping lama...\n";
    DB::table('major_subject_mappings')->truncate();
    echo "   ✅ Mapping lama berhasil dihapus\n\n";

    // Dapatkan semua jurusan
    $majors = DB::table('major_recommendations')->where('is_active', true)->get();
    echo "2. Ditemukan {$majors->count()} jurusan aktif\n\n";

    // Dapatkan semua mata pelajaran
    $subjects = DB::table('subjects')->get()->keyBy('name');
    echo "3. Ditemukan {$subjects->count()} mata pelajaran\n\n";

    // Mapping mata pelajaran pilihan untuk SMA/MA (daftar 1-18 sesuai peraturan)
    $smaOptionalSubjects = [
        'Matematika', 'Fisika', 'Kimia', 'Biologi', 'Sosiologi', 'Geografi', 
        'Ekonomi', 'Sejarah Tingkat Lanjut', 'Bahasa Indonesia', 'Bahasa Inggris',
        'Antropologi', 'PPKn/Pendidikan Pancasila', 'Seni Budaya', 'Pendidikan Jasmani',
        'Informatika', 'Koding dan Kecerdasan Artifisial', 'Bahasa Asing Lainnya',
        'Pendidikan Agama dan Budi Pekerti'
    ];

    // Mata pelajaran wajib untuk SMK/MAK
    $smkMandatorySubject = 'Produk/Projek Kreatif dan Kewirausahaan';

    // Mapping berdasarkan rumpun ilmu untuk SMA/MA
    $smaMappings = [
        'ILMU ALAM' => ['Matematika', 'Fisika', 'Kimia', 'Biologi'],
        'ILMU SOSIAL' => ['Sosiologi', 'Geografi', 'Ekonomi', 'Sejarah Tingkat Lanjut'],
        'HUMANIORA' => ['Bahasa Indonesia', 'Bahasa Inggris', 'Antropologi', 'Sosiologi'],
        'ILMU FORMAL' => ['Matematika', 'Informatika', 'Koding dan Kecerdasan Artifisial'],
        'ILMU TERAPAN' => ['Matematika', 'Fisika', 'Kimia', 'Biologi']
    ];

    // Mapping khusus untuk jurusan tertentu SMA/MA
    $specificSmaMappings = [
        'Seni' => ['Bahasa Indonesia', 'Bahasa Inggris'],
        'Linguistik' => ['Bahasa Indonesia', 'Bahasa Inggris'],
        'Filsafat' => ['Bahasa Indonesia', 'Bahasa Inggris'],
        'Ekonomi' => ['Ekonomi', 'Matematika'],
        'Psikologi' => ['Sosiologi', 'Biologi'],
        'Kimia' => ['Kimia', 'Matematika'],
        'Biologi' => ['Biologi', 'Kimia'],
        'Fisika' => ['Fisika', 'Matematika'],
        'Matematika' => ['Matematika', 'Fisika'],
        'Arsitektur' => ['Matematika', 'Fisika'],
        'Kedokteran' => ['Biologi', 'Kimia'],
        'Hukum' => ['Bahasa Indonesia', 'Bahasa Inggris'],
        'Teknik' => ['Matematika', 'Fisika']
    ];

    // Mapping untuk SMK/MAK (1 wajib + 1 pilihan)
    $smkMappings = [
        'Teknik Informatika' => ['Informatika'],
        'Teknik Komputer' => ['Informatika'],
        'Teknik Mesin' => ['Fisika'],
        'Teknik Elektro' => ['Fisika'],
        'Teknik Sipil' => ['Matematika'],
        'Teknik Kimia' => ['Kimia'],
        'Teknik Industri' => ['Matematika'],
        'Teknik Pertambangan' => ['Fisika'],
        'Teknik Geologi' => ['Fisika'],
        'Teknik Lingkungan' => ['Biologi'],
        'Teknik Perkapalan' => ['Fisika'],
        'Teknik Nuklir' => ['Fisika'],
        'Teknik Material' => ['Kimia'],
        'Teknik Metalurgi' => ['Kimia'],
        'Teknik Perminyakan' => ['Kimia'],
        'Teknik Geofisika' => ['Fisika'],
        'Teknik Geomatika' => ['Matematika'],
        'Teknik Kelautan' => ['Fisika'],
        'Teknik Penerbangan' => ['Fisika'],
        'Teknik Dirgantara' => ['Fisika']
    ];

    $insertedCount = 0;

    echo "4. Memulai mapping mata pelajaran...\n\n";

    foreach ($majors as $major) {
        $rumpunIlmu = $major->rumpun_ilmu;
        $majorName = $major->major_name;
        
        // Tentukan jenjang pendidikan
        $isSMK = in_array($rumpunIlmu, ['TEKNOLOGI', 'KESEHATAN', 'PERTANIAN']) || 
                 strpos(strtolower($majorName), 'teknik') !== false ||
                 strpos(strtolower($majorName), 'informatika') !== false ||
                 strpos(strtolower($majorName), 'komputer') !== false;

        if ($isSMK) {
            // SMK/MAK: 1 wajib + 1 pilihan
            echo "   📚 {$majorName} (SMK/MAK)\n";
            
            // Mata pelajaran wajib untuk SMK
            $mandatorySubject = $subjects->get($smkMandatorySubject);
            if ($mandatorySubject) {
                DB::table('major_subject_mappings')->insert([
                    'major_id' => $major->id,
                    'subject_id' => $mandatorySubject->id,
                    'education_level' => 'SMK/MAK',
                    'mapping_type' => 'pilihan_wajib',
                    'priority' => 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $insertedCount++;
                echo "      ✅ {$smkMandatorySubject} (wajib)\n";
            }

            // Mata pelajaran pilihan untuk SMK
            $optionalSubject = null;
            if (isset($smkMappings[$majorName])) {
                $optionalSubject = $smkMappings[$majorName][0];
            } else {
                // Default untuk SMK lainnya
                $optionalSubject = 'Matematika';
            }

            $subject = $subjects->get($optionalSubject);
            if ($subject) {
                DB::table('major_subject_mappings')->insert([
                    'major_id' => $major->id,
                    'subject_id' => $subject->id,
                    'education_level' => 'SMK/MAK',
                    'mapping_type' => 'pilihan',
                    'priority' => 2,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $insertedCount++;
                echo "      ✅ {$optionalSubject} (pilihan)\n";
            }

        } else {
            // SMA/MA: 2 mata pelajaran pilihan
            echo "   🎓 {$majorName} (SMA/MA)\n";
            
            $optionalSubjects = [];
            
            // Cek mapping khusus
            if (isset($specificSmaMappings[$majorName])) {
                $optionalSubjects = $specificSmaMappings[$majorName];
            } else {
                // Gunakan mapping berdasarkan rumpun ilmu
                $optionalSubjects = $smaMappings[$rumpunIlmu] ?? ['Matematika', 'Bahasa Indonesia'];
            }

            // Pastikan hanya 2 mata pelajaran
            $optionalSubjects = array_slice($optionalSubjects, 0, 2);

            foreach ($optionalSubjects as $index => $subjectName) {
                $subject = $subjects->get($subjectName);
                if ($subject) {
                    DB::table('major_subject_mappings')->insert([
                        'major_id' => $major->id,
                        'subject_id' => $subject->id,
                        'education_level' => 'SMA/MA',
                        'mapping_type' => 'pilihan',
                        'priority' => $index + 1,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $insertedCount++;
                    echo "      ✅ {$subjectName} (pilihan)\n";
                }
            }
        }
        echo "\n";
    }

    echo "5. Verifikasi hasil...\n";
    
    // Hitung total mapping
    $totalMappings = DB::table('major_subject_mappings')->count();
    echo "   Total mapping: {$totalMappings}\n";
    
    // Hitung per jenjang
    $smaCount = DB::table('major_subject_mappings')
        ->where('education_level', 'SMA/MA')
        ->where('mapping_type', 'pilihan')
        ->count();
    $smkCount = DB::table('major_subject_mappings')
        ->where('education_level', 'SMK/MAK')
        ->count();
    
    echo "   SMA/MA (pilihan): {$smaCount}\n";
    echo "   SMK/MAK (total): {$smkCount}\n\n";

    // Tampilkan contoh data
    echo "6. Contoh data yang dihasilkan:\n";
    $sampleMajors = DB::table('major_subject_mappings')
        ->join('major_recommendations', 'major_subject_mappings.major_id', '=', 'major_recommendations.id')
        ->join('subjects', 'major_subject_mappings.subject_id', '=', 'subjects.id')
        ->select('major_recommendations.major_name', 'subjects.name as subject_name', 'major_subject_mappings.education_level', 'major_subject_mappings.mapping_type')
        ->orderBy('major_recommendations.major_name')
        ->orderBy('major_subject_mappings.priority')
        ->limit(10)
        ->get();

    foreach ($sampleMajors as $mapping) {
        $type = $mapping->mapping_type === 'pilihan_wajib' ? 'wajib' : 'pilihan';
        echo "   {$mapping->major_name} ({$mapping->education_level}): {$mapping->subject_name} ({$type})\n";
    }

    echo "\n✅ BERHASIL! Mapping mata pelajaran pilihan telah disesuaikan dengan peraturan PUSMENDIK.\n";
    echo "   - SMA/MA: Maksimal 2 mata pelajaran pilihan\n";
    echo "   - SMK/MAK: 1 mata pelajaran wajib + 1 mata pelajaran pilihan\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

?>
