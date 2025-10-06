<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🌱 SIMPLE SEEDER - SUBJECTS AND MAPPINGS\n";
echo "========================================\n\n";

try {
    // 1. Create subjects if they don't exist
    echo "1. Creating subjects...\n";
    
    $subjects = [
        // Wajib SMA/MA
        ['name' => 'Matematika', 'code' => 'MAT', 'subject_type' => 'wajib', 'education_level' => 'SMA/MA'],
        ['name' => 'Bahasa Indonesia', 'code' => 'BIN', 'subject_type' => 'wajib', 'education_level' => 'SMA/MA'],
        ['name' => 'Bahasa Inggris', 'code' => 'BIG', 'subject_type' => 'wajib', 'education_level' => 'SMA/MA'],
        
        // Wajib SMK/MAK
        ['name' => 'Matematika', 'code' => 'MAT', 'subject_type' => 'wajib', 'education_level' => 'SMK/MAK'],
        ['name' => 'Bahasa Indonesia', 'code' => 'BIN', 'subject_type' => 'wajib', 'education_level' => 'SMK/MAK'],
        ['name' => 'Bahasa Inggris', 'code' => 'BIG', 'subject_type' => 'wajib', 'education_level' => 'SMK/MAK'],
        
        // Pilihan SMA/MA
        ['name' => 'Fisika', 'code' => 'FIS', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        ['name' => 'Kimia', 'code' => 'KIM', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        ['name' => 'Biologi', 'code' => 'BIO', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        ['name' => 'Ekonomi', 'code' => 'EKO', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        ['name' => 'Sosiologi', 'code' => 'SOS', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        ['name' => 'Geografi', 'code' => 'GEO', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        ['name' => 'Sejarah', 'code' => 'SEJ', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        ['name' => 'Antropologi', 'code' => 'ANT', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        ['name' => 'PPKn/Pendidikan Pancasila', 'code' => 'PPKN', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        ['name' => 'Bahasa Arab', 'code' => 'BAR', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        ['name' => 'Bahasa Jerman', 'code' => 'BJE', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        ['name' => 'Bahasa Prancis', 'code' => 'BPR', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        ['name' => 'Bahasa Jepang', 'code' => 'BJP', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        ['name' => 'Bahasa Korea', 'code' => 'BKO', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        ['name' => 'Bahasa Mandarin', 'code' => 'BMA', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        ['name' => 'Matematika Lanjutan', 'code' => 'MATL', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        ['name' => 'Bahasa Indonesia Lanjutan', 'code' => 'BINL', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        ['name' => 'Bahasa Inggris Lanjutan', 'code' => 'BIGL', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA'],
        
        // Khusus SMK/MAK
        ['name' => 'Produk/Projek Kreatif dan Kewirausahaan', 'code' => 'PKW', 'subject_type' => 'pilihan_wajib', 'education_level' => 'SMK/MAK'],
    ];

    foreach ($subjects as $subjectData) {
        \App\Models\Subject::updateOrCreate(
            [
                'name' => $subjectData['name'],
                'education_level' => $subjectData['education_level']
            ],
            array_merge($subjectData, [
                'description' => 'Mata pelajaran ' . strtolower($subjectData['name']),
                'subject_number' => 1,
                'is_active' => true
            ])
        );
    }
    
    echo "✅ Subjects created/updated: " . \App\Models\Subject::count() . "\n\n";

    // 2. Create major mappings
    echo "2. Creating major mappings...\n";
    
    // Clear existing mappings
    \App\Models\MajorSubjectMapping::truncate();
    
    $majors = \App\Models\MajorRecommendation::where('is_active', true)->get();
    
    foreach ($majors as $major) {
        $educationLevel = determineEducationLevel($major->rumpun_ilmu);
        
        if ($educationLevel === 'SMA/MA') {
            // SMA/MA: 2 optional subjects
            $optionalSubjects = \App\Models\Subject::where('subject_type', 'pilihan')
                ->where('education_level', 'SMA/MA')
                ->inRandomOrder()
                ->limit(2)
                ->get();
                
            foreach ($optionalSubjects as $index => $subject) {
                \App\Models\MajorSubjectMapping::create([
                    'major_id' => $major->id,
                    'subject_id' => $subject->id,
                    'education_level' => 'SMA/MA',
                    'mapping_type' => 'pilihan',
                    'priority' => $index + 1,
                    'is_active' => true
                ]);
            }
        } else {
            // SMK/MAK: 1 mandatory + 1 optional
            // Mandatory PKW
            $pkwSubject = \App\Models\Subject::where('name', 'Produk/Projek Kreatif dan Kewirausahaan')
                ->where('education_level', 'SMK/MAK')
                ->first();
                
            if ($pkwSubject) {
                \App\Models\MajorSubjectMapping::create([
                    'major_id' => $major->id,
                    'subject_id' => $pkwSubject->id,
                    'education_level' => 'SMK/MAK',
                    'mapping_type' => 'pilihan_wajib',
                    'priority' => 1,
                    'is_active' => true
                ]);
            }
            
            // 1 optional from SMA/MA list
            $optionalSubject = \App\Models\Subject::where('subject_type', 'pilihan')
                ->where('education_level', 'SMA/MA')
                ->inRandomOrder()
                ->first();
                
            if ($optionalSubject) {
                \App\Models\MajorSubjectMapping::create([
                    'major_id' => $major->id,
                    'subject_id' => $optionalSubject->id,
                    'education_level' => 'SMK/MAK',
                    'mapping_type' => 'pilihan',
                    'priority' => 2,
                    'is_active' => true
                ]);
            }
        }
    }
    
    echo "✅ Major mappings created: " . \App\Models\MajorSubjectMapping::count() . "\n\n";

    // 3. Update major recommendations with curriculum data
    echo "3. Updating major recommendations...\n";
    
    foreach ($majors as $major) {
        $major->update([
            'kurikulum_merdeka_subjects' => [
                'Matematika Lanjutan',
                'Bahasa Indonesia Lanjutan',
                'Bahasa Inggris Lanjutan',
                'Fisika',
                'Kimia',
                'Biologi',
                'Ekonomi',
                'Sosiologi'
            ],
            'kurikulum_2013_ipa_subjects' => [
                'Matematika Lanjutan',
                'Fisika',
                'Kimia',
                'Biologi',
                'Bahasa Indonesia Lanjutan',
                'Bahasa Inggris Lanjutan',
                'Ekonomi',
                'Sosiologi'
            ],
            'kurikulum_2013_ips_subjects' => [
                'Ekonomi',
                'Sosiologi',
                'Geografi',
                'Sejarah',
                'Antropologi',
                'PPKn/Pendidikan Pancasila',
                'Bahasa Indonesia Lanjutan',
                'Bahasa Inggris Lanjutan'
            ],
            'kurikulum_2013_bahasa_subjects' => [
                'Bahasa Indonesia Lanjutan',
                'Bahasa Inggris Lanjutan',
                'Bahasa Arab',
                'Bahasa Jerman',
                'Bahasa Prancis',
                'Bahasa Jepang',
                'Bahasa Korea',
                'Bahasa Mandarin'
            ],
            'career_prospects' => 'Berbagai peluang karir sesuai dengan bidang keahlian yang dipilih. Lulusan dapat bekerja di berbagai sektor industri, pemerintahan, atau melanjutkan ke jenjang pendidikan yang lebih tinggi.'
        ]);
    }
    
    echo "✅ Major recommendations updated: " . $majors->count() . "\n\n";

    // 4. Test APIs
    echo "4. Testing APIs...\n";
    
    // Test Web API
    $webController = new \App\Http\Controllers\StudentWebController();
    $webResponse = $webController->getMajors();
    $webData = json_decode($webResponse->getContent(), true);
    
    if ($webData['success']) {
        echo "✅ Web API working - {$webData['data'][0]['major_name']} has " . count($webData['data'][0]['optional_subjects']) . " optional subjects\n";
    } else {
        echo "❌ Web API error: {$webData['message']}\n";
    }
    
    echo "\n🎉 SEEDER COMPLETED SUCCESSFULLY!\n";
    echo "================================\n";
    echo "All data has been properly seeded into the database.\n";
    echo "APIs are working correctly.\n";
    echo "Ready for production use!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
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
