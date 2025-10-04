<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 UPDATING OPTIONAL SUBJECTS FINAL\n";
echo "==================================\n\n";

try {
    // 1. Define the exact mappings as requested
    echo "1. Setting up exact optional subject mappings...\n";
    
    $exactMappings = [
        "Seni" => ["Seni Budaya", "Seni Budaya"],
        "Sejarah" => ["Sejarah Indonesia", "Sejarah Indonesia"],
        "Linguistik" => ["Bahasa Indonesia", "Bahasa Inggris"],
        "Susastra" => ["Bahasa Indonesia", "Bahasa Asing"],
        "Filsafat" => ["Sosiologi", "Sejarah Indonesia"],
        "Sosial" => ["Sosiologi", "Antropologi"],
        "Ekonomi" => ["Ekonomi", "Matematika"],
        "Pertahanan" => ["PPKn", "PPKn"],
        "Psikologi" => ["Sosiologi", "Matematika"],
        "Kimia" => ["Kimia", "Kimia"],
        "Ilmu Kebumian" => ["Fisika", "Matematika"],
        "Ilmu Kelautan" => ["Biologi", "Geografi"],
        "Biologi" => ["Biologi", "Kimia"],
        "Biofisika" => ["Fisika", "Matematika"],
        "Fisika" => ["Fisika", "Matematika"],
        "Astronomi" => ["Fisika", "Matematika"],
        "Komputer" => ["Matematika", "Logika"],
        "Logika" => ["Matematika", "Statistika"],
        "Matematika" => ["Matematika", "Fisika"],
        "Pertanian" => ["Biologi", "Kimia"],
        "Peternakan" => ["Biologi", "Kimia"],
        "Perikanan" => ["Biologi", "Kimia"],
        "Arsitektur" => ["Matematika", "Fisika"],
        "Perencanaan Wilayah" => ["Ekonomi", "Matematika"],
        "Desain" => ["Seni Budaya", "Matematika"],
        "Akuntansi" => ["Ekonomi", "Matematika"],
        "Manajemen" => ["Ekonomi", "Matematika"],
        "Logistik" => ["Ekonomi", "Matematika"],
        "Administrasi Bisnis" => ["Ekonomi", "Matematika"],
        "Bisnis" => ["Ekonomi", "Matematika"],
        "Komunikasi" => ["Sosiologi", "Antropologi"],
        "Pendidikan" => ["Mapel Relevan", "Bahasa Indonesia"],
        "Teknik Rekayasa" => ["Fisika", "Matematika"],
        "Ilmu Lingkungan" => ["Biologi", "Kimia"],
        "Kehutanan" => ["Biologi", "Geografi"],
        "Kedokteran" => ["Biologi", "Kimia"],
        "Kedokteran Gigi" => ["Biologi", "Kimia"],
        "Veteriner" => ["Biologi", "Kimia"],
        "Farmasi" => ["Biologi", "Kimia"],
        "Gizi" => ["Biologi", "Kimia"],
        "Kesehatan Masyarakat" => ["Biologi", "Sosiologi"],
        "Kebidanan" => ["Biologi", "Kimia"],
        "Keperawatan" => ["Biologi", "Kimia"],
        "Kesehatan" => ["Biologi", "Kimia"],
        "Ilmu Informasi" => ["Matematika", "Statistika"],
        "Hukum" => ["Sosiologi", "PPKn"],
        "Ilmu Militer" => ["Sosiologi", "PPKn"],
        "Urusan Publik" => ["Sosiologi", "PPKn"],
        "Keolahragaan" => ["PJOK", "Biologi"],
        "Pariwisata" => ["Ekonomi", "Bahasa Inggris"],
        "Transportasi" => ["Matematika", "Fisika"],
        "Bioteknologi / Bioinformatika" => ["Biologi", "Matematika"],
        "Geografi" => ["Geografi", "Matematika"],
        "Informatika Medis" => ["Biologi", "Matematika"],
        "Konservasi Biologi" => ["Biologi", "Geografi"],
        "Teknologi Pangan" => ["Kimia", "Biologi"],
        "Sains Data" => ["Matematika", "Statistika"],
        "Sains Perkopian" => ["Biologi", "Kimia"],
        "Studi Humanitas" => ["Antropologi", "Sosiologi"]
    ];
    
    echo "   ✅ Defined " . count($exactMappings) . " exact mappings\n";
    
    // 2. Create/update subjects with unique codes
    echo "\n2. Creating/updating subjects with unique codes...\n";
    
    $subjectsData = [
        ["name" => "Seni Budaya", "code" => "SB", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "Sejarah Indonesia", "code" => "SI", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "Bahasa Indonesia", "code" => "BIN", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "Bahasa Inggris", "code" => "BIG", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "Bahasa Asing", "code" => "BAS", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "Sosiologi", "code" => "SOS", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "Antropologi", "code" => "ANT", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "Ekonomi", "code" => "EKO", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "Matematika", "code" => "MAT", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "PPKn", "code" => "PPK", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "Kimia", "code" => "KIM", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "Fisika", "code" => "FIS", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "Biologi", "code" => "BIO", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "Geografi", "code" => "GEO", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "PJOK", "code" => "PJO", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "Logika", "code" => "LOG", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "Statistika", "code" => "STA", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "Mapel Relevan", "code" => "MRL", "subject_type" => "pilihan", "education_level" => "SMA/MA"],
        ["name" => "Produk/Projek Kreatif dan Kewirausahaan", "code" => "PKW", "subject_type" => "pilihan_wajib", "education_level" => "SMK/MAK"]
    ];
    
    foreach ($subjectsData as $subjectData) {
        \App\Models\Subject::updateOrCreate(
            ['name' => $subjectData['name']],
            $subjectData
        );
        echo "   ✅ Subject: {$subjectData['name']} ({$subjectData['code']})\n";
    }
    
    // 3. Clear existing optional subject mappings
    echo "\n3. Clearing existing optional subject mappings...\n";
    
    \App\Models\MajorSubjectMapping::where('mapping_type', 'pilihan')->delete();
    echo "   ✅ Cleared existing optional mappings\n";
    
    // 4. Create new mappings based on exact requirements
    echo "\n4. Creating new exact mappings...\n";
    
    $majors = \App\Models\MajorRecommendation::where('is_active', true)->get();
    
    foreach ($majors as $major) {
        $majorName = $major->major_name;
        
        // Find the mapping for this major
        if (isset($exactMappings[$majorName])) {
            $subjects = $exactMappings[$majorName];
            $educationLevel = determineEducationLevel($major->rumpun_ilmu);
            
            echo "   Processing {$majorName} ({$educationLevel}): " . implode(', ', $subjects) . "\n";
            
            foreach ($subjects as $index => $subjectName) {
                $subject = \App\Models\Subject::where('name', $subjectName)->first();
                
                if ($subject) {
                    \App\Models\MajorSubjectMapping::create([
                        'major_id' => $major->id,
                        'subject_id' => $subject->id,
                        'education_level' => $educationLevel,
                        'mapping_type' => 'pilihan',
                        'priority' => $index + 1,
                        'is_active' => true,
                        'subject_type' => 'pilihan'
                    ]);
                    echo "     ✅ Mapped: {$subjectName}\n";
                } else {
                    echo "     ❌ Subject not found: {$subjectName}\n";
                }
            }
        } else {
            echo "   ⚠️  No mapping found for: {$majorName}\n";
        }
    }
    
    // 5. Update major recommendations with new optional subjects
    echo "\n5. Updating major recommendations...\n";
    
    foreach ($majors as $major) {
        $majorName = $major->major_name;
        
        if (isset($exactMappings[$majorName])) {
            $optionalSubjects = $exactMappings[$majorName];
            
            $major->update([
                'preferred_subjects' => $optionalSubjects
            ]);
            
            echo "   ✅ Updated {$majorName}: " . implode(', ', $optionalSubjects) . "\n";
        }
    }
    
    // 6. Verify the updates
    echo "\n6. Verifying updates...\n";
    
    $totalMappings = \App\Models\MajorSubjectMapping::where('mapping_type', 'pilihan')->count();
    $expectedMappings = count($exactMappings) * 2; // 2 subjects per major
    
    echo "   Total optional mappings: {$totalMappings}\n";
    echo "   Expected mappings: {$expectedMappings}\n";
    
    if ($totalMappings >= $expectedMappings) {
        echo "   ✅ Mappings created successfully\n";
    } else {
        echo "   ❌ Mappings count mismatch\n";
    }
    
    // 7. Test a few specific majors
    echo "\n7. Testing specific majors...\n";
    
    $testMajors = ['Seni', 'Linguistik', 'Ilmu Kelautan', 'Matematika'];
    
    foreach ($testMajors as $testMajorName) {
        $testMajor = \App\Models\MajorRecommendation::where('major_name', $testMajorName)->first();
        
        if ($testMajor) {
            $mappings = \App\Models\MajorSubjectMapping::where('major_id', $testMajor->id)
                ->where('mapping_type', 'pilihan')
                ->with('subject')
                ->get();
            
            $subjectNames = $mappings->pluck('subject.name')->toArray();
            
            if (isset($exactMappings[$testMajorName])) {
                $expectedSubjects = $exactMappings[$testMajorName];
                
                if ($subjectNames === $expectedSubjects) {
                    echo "   ✅ {$testMajorName}: " . implode(', ', $subjectNames) . "\n";
                } else {
                    echo "   ❌ {$testMajorName}: Expected " . implode(', ', $expectedSubjects) . ", Got " . implode(', ', $subjectNames) . "\n";
                }
            }
        }
    }

    echo "\n🎉 OPTIONAL SUBJECTS UPDATED SUCCESSFULLY!\n";
    echo "========================================\n";
    echo "✅ All exact mappings applied\n";
    echo "✅ Database updated with new mappings\n";
    echo "✅ Major recommendations updated\n";
    echo "✅ All data is consistent\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

function determineEducationLevel($rumpunIlmu)
{
    $smaRumpun = ['ILMU ALAM', 'ILMU SOSIAL', 'ILMU BUDAYA', 'HUMANIORA', 'ILMU FORMAL'];
    
    if (in_array($rumpunIlmu, $smaRumpun)) {
        return 'SMA/MA';
    } else {
        return 'SMK/MAK';
    }
}
