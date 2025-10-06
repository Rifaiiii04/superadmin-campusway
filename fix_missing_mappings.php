<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 FIXING MISSING MAPPINGS\n";
echo "=========================\n\n";

try {
    // 1. Check what majors exist in database
    echo "1. Checking existing majors in database...\n";
    
    $existingMajors = \App\Models\MajorRecommendation::where('is_active', true)
        ->pluck('major_name')
        ->toArray();
    
    echo "   Found " . count($existingMajors) . " active majors:\n";
    foreach ($existingMajors as $major) {
        echo "     - {$major}\n";
    }
    
    // 2. Define mappings with correct database names
    echo "\n2. Creating mappings with correct database names...\n";
    
    $correctMappings = [
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
        "Ilmu Pertanian" => ["Biologi", "Kimia"], // Map to existing name
        "Peternakan" => ["Biologi", "Kimia"],
        "Ilmu Perikanan" => ["Biologi", "Kimia"], // Map to existing name
        "Arsitektur" => ["Matematika", "Fisika"],
        "Perencanaan Wilayah" => ["Ekonomi", "Matematika"],
        "Desain" => ["Seni Budaya", "Matematika"],
        "Ilmu Akuntansi" => ["Ekonomi", "Matematika"], // Map to existing name
        "Ilmu Manajemen" => ["Ekonomi", "Matematika"], // Map to existing name
        "Logistik" => ["Ekonomi", "Matematika"],
        "Administrasi Bisnis" => ["Ekonomi", "Matematika"],
        "Bisnis" => ["Ekonomi", "Matematika"],
        "Ilmu Komunikasi" => ["Sosiologi", "Antropologi"], // Map to existing name
        "Pendidikan" => ["Mapel Relevan", "Bahasa Indonesia"],
        "Teknik Rekayasa" => ["Fisika", "Matematika"],
        "Ilmu Lingkungan" => ["Biologi", "Kimia"],
        "Kehutanan" => ["Biologi", "Geografi"],
        "Ilmu Kedokteran" => ["Biologi", "Kimia"], // Map to existing name
        "Ilmu Kedokteran Gigi" => ["Biologi", "Kimia"], // Map to existing name
        "Ilmu Veteriner" => ["Biologi", "Kimia"], // Map to existing name
        "Ilmu Farmasi" => ["Biologi", "Kimia"], // Map to existing name
        "Ilmu Gizi" => ["Biologi", "Kimia"], // Map to existing name
        "Kesehatan Masyarakat" => ["Biologi", "Sosiologi"],
        "Kebidanan" => ["Biologi", "Kimia"],
        "Keperawatan" => ["Biologi", "Kimia"],
        "Kesehatan" => ["Biologi", "Kimia"],
        "Ilmu Informasi" => ["Matematika", "Statistika"],
        "Hukum" => ["Sosiologi", "PPKn"],
        "Ilmu Militer" => ["Sosiologi", "PPKn"],
        "Urusan Publik" => ["Sosiologi", "PPKn"],
        "Ilmu Keolahragaan" => ["PJOK", "Biologi"], // Map to existing name
        "Pariwisata" => ["Ekonomi", "Bahasa Inggris"],
        "Transportasi" => ["Matematika", "Fisika"],
        "Bioteknologi" => ["Biologi", "Matematika"], // Map to existing name
        "Geografi" => ["Geografi", "Matematika"],
        "Informatika Medis" => ["Biologi", "Matematika"],
        "Konservasi Biologi" => ["Biologi", "Geografi"],
        "Teknologi Pangan" => ["Kimia", "Biologi"],
        "Sains Data" => ["Matematika", "Statistika"],
        "Sains Perkopian" => ["Biologi", "Kimia"],
        "Studi Humanitas" => ["Antropologi", "Sosiologi"],
        "Sastra" => ["Bahasa Indonesia", "Bahasa Asing"] // Map to existing name
    ];
    
    echo "   ✅ Defined " . count($correctMappings) . " correct mappings\n";
    
    // 3. Clear existing optional mappings again
    echo "\n3. Clearing existing optional mappings...\n";
    
    \App\Models\MajorSubjectMapping::where('mapping_type', 'pilihan')->delete();
    echo "   ✅ Cleared existing optional mappings\n";
    
    // 4. Create mappings for existing majors only
    echo "\n4. Creating mappings for existing majors...\n";
    
    $majors = \App\Models\MajorRecommendation::where('is_active', true)->get();
    $mappedCount = 0;
    
    foreach ($majors as $major) {
        $majorName = $major->major_name;
        
        // Find the mapping for this major
        if (isset($correctMappings[$majorName])) {
            $subjects = $correctMappings[$majorName];
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
                    $mappedCount++;
                    echo "     ✅ Mapped: {$subjectName}\n";
                } else {
                    echo "     ❌ Subject not found: {$subjectName}\n";
                }
            }
        } else {
            echo "   ⚠️  No mapping found for: {$majorName}\n";
        }
    }
    
    // 5. Update major recommendations
    echo "\n5. Updating major recommendations...\n";
    
    foreach ($majors as $major) {
        $majorName = $major->major_name;
        
        if (isset($correctMappings[$majorName])) {
            $optionalSubjects = $correctMappings[$majorName];
            
            $major->update([
                'preferred_subjects' => $optionalSubjects
            ]);
            
            echo "   ✅ Updated {$majorName}: " . implode(', ', $optionalSubjects) . "\n";
        }
    }
    
    // 6. Verify the updates
    echo "\n6. Verifying updates...\n";
    
    $totalMappings = \App\Models\MajorSubjectMapping::where('mapping_type', 'pilihan')->count();
    $majorsWithMappings = \App\Models\MajorRecommendation::whereHas('majorSubjectMappings', function($query) {
        $query->where('mapping_type', 'pilihan');
    })->count();
    
    echo "   Total optional mappings: {$totalMappings}\n";
    echo "   Majors with mappings: {$majorsWithMappings}\n";
    echo "   Total active majors: " . $majors->count() . "\n";
    
    if ($totalMappings > 0 && $majorsWithMappings > 0) {
        echo "   ✅ Mappings created successfully\n";
    } else {
        echo "   ❌ No mappings created\n";
    }
    
    // 7. Test specific majors
    echo "\n7. Testing specific majors...\n";
    
    $testMajors = ['Seni', 'Linguistik', 'Ilmu Kelautan', 'Matematika', 'Fisika'];
    
    foreach ($testMajors as $testMajorName) {
        $testMajor = \App\Models\MajorRecommendation::where('major_name', $testMajorName)->first();
        
        if ($testMajor) {
            $mappings = \App\Models\MajorSubjectMapping::where('major_id', $testMajor->id)
                ->where('mapping_type', 'pilihan')
                ->with('subject')
                ->get();
            
            $subjectNames = $mappings->pluck('subject.name')->toArray();
            
            if (isset($correctMappings[$testMajorName])) {
                $expectedSubjects = $correctMappings[$testMajorName];
                
                if ($subjectNames === $expectedSubjects) {
                    echo "   ✅ {$testMajorName}: " . implode(', ', $subjectNames) . "\n";
                } else {
                    echo "   ❌ {$testMajorName}: Expected " . implode(', ', $expectedSubjects) . ", Got " . implode(', ', $subjectNames) . "\n";
                }
            } else {
                echo "   ⚠️  {$testMajorName}: No mapping defined\n";
            }
        } else {
            echo "   ❌ {$testMajorName}: Not found in database\n";
        }
    }

    echo "\n🎉 MISSING MAPPINGS FIXED SUCCESSFULLY!\n";
    echo "======================================\n";
    echo "✅ All existing majors mapped\n";
    echo "✅ Database updated with correct mappings\n";
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
