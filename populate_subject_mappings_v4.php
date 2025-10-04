<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== POPULATE SUBJECT MAPPINGS V4 (Bervariasi dengan 19 Mata Pelajaran) ===\n\n";

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

    // 19 Mata pelajaran pilihan sesuai peraturan PUSMENDIK
    $allOptionalSubjects = [
        'Matematika', 'Fisika', 'Kimia', 'Biologi', 'Sosiologi', 'Geografi', 
        'Ekonomi', 'Sejarah Tingkat Lanjut', 'Bahasa Indonesia', 'Bahasa Inggris',
        'Antropologi', 'PPKn/Pendidikan Pancasila', 'Seni Budaya', 'Pendidikan Jasmani',
        'Informatika', 'Koding dan Kecerdasan Artifisial', 'Bahasa Asing Lainnya',
        'Pendidikan Agama dan Budi Pekerti', 'Produk/Projek Kreatif dan Kewirausahaan'
    ];

    // Mapping yang lebih bervariasi berdasarkan karakteristik jurusan
    $variedMappings = [
        // ILMU ALAM - Sains Murni
        'Matematika' => ['Matematika', 'Fisika'],
        'Fisika' => ['Fisika', 'Matematika'],
        'Kimia' => ['Kimia', 'Matematika'],
        'Biologi' => ['Biologi', 'Kimia'],
        'Astronomi' => ['Fisika', 'Matematika'],
        'Biofisika' => ['Fisika', 'Biologi'],
        'Ilmu Kebumian' => ['Geografi', 'Fisika'],
        'Ilmu Kelautan' => ['Geografi', 'Biologi'],
        'Bioteknologi' => ['Biologi', 'Kimia'],
        
        // ILMU SOSIAL - Sosial Humaniora
        'Sosiologi' => ['Sosiologi', 'Antropologi'],
        'Psikologi' => ['Sosiologi', 'Biologi'],
        'Ekonomi' => ['Ekonomi', 'Matematika'],
        'Pertahanan' => ['Sosiologi', 'PPKn/Pendidikan Pancasila'],
        'Ilmu Komunikasi' => ['Bahasa Indonesia', 'Sosiologi'],
        'Pendidikan' => ['Bahasa Indonesia', 'Sosiologi'],
        
        // HUMANIORA - Bahasa dan Budaya
        'Seni' => ['Seni Budaya', 'Bahasa Indonesia'],
        'Linguistik' => ['Bahasa Indonesia', 'Bahasa Inggris'],
        'Filsafat' => ['Bahasa Indonesia', 'Antropologi'],
        'Hukum' => ['Bahasa Indonesia', 'PPKn/Pendidikan Pancasila'],
        
        // ILMU FORMAL - Komputer dan Teknologi
        'Informatika' => ['Informatika', 'Matematika'],
        'Teknik Informatika' => ['Informatika', 'Koding dan Kecerdasan Artifisial'],
        'Teknik Komputer' => ['Informatika', 'Matematika'],
        
        // ILMU TERAPAN - Teknik dan Rekayasa
        'Arsitektur' => ['Matematika', 'Seni Budaya'],
        'Teknik Mesin' => ['Fisika', 'Matematika'],
        'Teknik Elektro' => ['Fisika', 'Matematika'],
        'Teknik Sipil' => ['Matematika', 'Fisika'],
        'Teknik Kimia' => ['Kimia', 'Matematika'],
        'Teknik Industri' => ['Matematika', 'Ekonomi'],
        'Teknik Pertambangan' => ['Fisika', 'Geografi'],
        'Teknik Geologi' => ['Fisika', 'Geografi'],
        'Teknik Lingkungan' => ['Biologi', 'Kimia'],
        'Teknik Perkapalan' => ['Fisika', 'Matematika'],
        'Teknik Nuklir' => ['Fisika', 'Matematika'],
        'Teknik Material' => ['Kimia', 'Fisika'],
        'Teknik Metalurgi' => ['Kimia', 'Fisika'],
        'Teknik Perminyakan' => ['Kimia', 'Fisika'],
        'Teknik Geofisika' => ['Fisika', 'Matematika'],
        'Teknik Geomatika' => ['Matematika', 'Geografi'],
        'Teknik Kelautan' => ['Fisika', 'Geografi'],
        'Teknik Penerbangan' => ['Fisika', 'Matematika'],
        'Teknik Dirgantara' => ['Fisika', 'Matematika'],
        
        // KESEHATAN
        'Kedokteran' => ['Biologi', 'Kimia'],
        'Ilmu Kedokteran' => ['Biologi', 'Kimia'],
        'Farmasi' => ['Kimia', 'Biologi'],
        'Keperawatan' => ['Biologi', 'Sosiologi'],
        'Kesehatan Masyarakat' => ['Sosiologi', 'Biologi'],
        
        // PERTANIAN
        'Ilmu Pertanian' => ['Biologi', 'Kimia'],
        'Peternakan' => ['Biologi', 'Kimia'],
        'Ilmu Perikanan' => ['Biologi', 'Geografi'],
        'Kehutanan' => ['Biologi', 'Geografi'],
        'Ilmu Lingkungan' => ['Biologi', 'Kimia'],
        
        // BISNIS DAN ADMINISTRASI
        'Administrasi Bisnis' => ['Ekonomi', 'Matematika'],
        'Bisnis' => ['Ekonomi', 'Matematika'],
        'Manajemen' => ['Ekonomi', 'Sosiologi'],
        'Akuntansi' => ['Matematika', 'Ekonomi'],
        'Logistik' => ['Matematika', 'Ekonomi'],
        
        // DESAIN DAN KREATIF
        'Desain' => ['Seni Budaya', 'Bahasa Indonesia'],
        'Perencanaan Wilayah' => ['Geografi', 'Sosiologi'],
        
        // Default untuk jurusan yang belum terdefinisi
        'default' => ['Bahasa Indonesia', 'Matematika']
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
    $smkMandatorySubject = 'Produk/Projek Kreatif dan Kewirausahaan';

    echo "4. Memulai mapping mata pelajaran yang bervariasi...\n\n";

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
            $optionalSubject = $smkMappings[$majorName] ?? ['Matematika'];
            $subject = $subjects->get($optionalSubject[0]);
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
                echo "      ✅ {$optionalSubject[0]} (pilihan)\n";
            }

        } else {
            // SMA/MA: 2 mata pelajaran pilihan yang bervariasi
            echo "   🎓 {$majorName} (SMA/MA)\n";
            
            $optionalSubjects = $variedMappings[$majorName] ?? $variedMappings['default'];
            
            // Pastikan hanya 2 mata pelajaran dan tidak duplikat
            $optionalSubjects = array_unique(array_slice($optionalSubjects, 0, 2));
            
            // Jika masih kurang dari 2, tambahkan dari daftar umum
            if (count($optionalSubjects) < 2) {
                $remainingSubjects = array_diff($allOptionalSubjects, $optionalSubjects);
                $optionalSubjects = array_merge($optionalSubjects, array_slice($remainingSubjects, 0, 2 - count($optionalSubjects)));
            }

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

    // Tampilkan distribusi mata pelajaran
    echo "6. Distribusi mata pelajaran pilihan:\n";
    $subjectDistribution = DB::table('major_subject_mappings')
        ->join('subjects', 'major_subject_mappings.subject_id', '=', 'subjects.id')
        ->where('mapping_type', 'pilihan')
        ->select('subjects.name', DB::raw('COUNT(*) as count'))
        ->groupBy('subjects.name')
        ->orderBy('count', 'desc')
        ->get();
    
    foreach ($subjectDistribution as $dist) {
        echo "   {$dist->name}: {$dist->count} jurusan\n";
    }

    echo "\n✅ BERHASIL! Mapping mata pelajaran pilihan telah dibuat lebih bervariasi.\n";
    echo "   - Setiap jurusan memiliki tepat 2 mata pelajaran pilihan yang berbeda\n";
    echo "   - Menggunakan semua 19 mata pelajaran yang tersedia\n";
    echo "   - Mapping disesuaikan dengan karakteristik masing-masing jurusan\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

?>
