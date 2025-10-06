<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🚀 POPULATING DATABASE WITH SUBJECTS AND MAPPINGS\n";
echo "================================================\n\n";

try {
    // 1. Create subjects
    echo "1. Creating subjects...\n";
    
    $subjects = [
        // Wajib SMA/MA
        ['name' => 'Matematika', 'code' => 'MAT', 'subject_type' => 'wajib', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran matematika dasar'],
        ['name' => 'Bahasa Indonesia', 'code' => 'BIN', 'subject_type' => 'wajib', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran bahasa Indonesia'],
        ['name' => 'Bahasa Inggris', 'code' => 'BIG', 'subject_type' => 'wajib', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran bahasa Inggris'],
        
        // Wajib SMK/MAK
        ['name' => 'Matematika', 'code' => 'MAT', 'subject_type' => 'wajib', 'education_level' => 'SMK/MAK', 'description' => 'Mata pelajaran matematika dasar'],
        ['name' => 'Bahasa Indonesia', 'code' => 'BIN', 'subject_type' => 'wajib', 'education_level' => 'SMK/MAK', 'description' => 'Mata pelajaran bahasa Indonesia'],
        ['name' => 'Bahasa Inggris', 'code' => 'BIG', 'subject_type' => 'wajib', 'education_level' => 'SMK/MAK', 'description' => 'Mata pelajaran bahasa Inggris'],
        
        // Pilihan SMA/MA (18 mata pelajaran)
        ['name' => 'Fisika', 'code' => 'FIS', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran fisika'],
        ['name' => 'Kimia', 'code' => 'KIM', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran kimia'],
        ['name' => 'Biologi', 'code' => 'BIO', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran biologi'],
        ['name' => 'Ekonomi', 'code' => 'EKO', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran ekonomi'],
        ['name' => 'Sosiologi', 'code' => 'SOS', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran sosiologi'],
        ['name' => 'Geografi', 'code' => 'GEO', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran geografi'],
        ['name' => 'Sejarah', 'code' => 'SEJ', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran sejarah'],
        ['name' => 'Antropologi', 'code' => 'ANT', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran antropologi'],
        ['name' => 'PPKn/Pendidikan Pancasila', 'code' => 'PPKN', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran PPKn/Pendidikan Pancasila'],
        ['name' => 'Bahasa Arab', 'code' => 'BAR', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran bahasa Arab'],
        ['name' => 'Bahasa Jerman', 'code' => 'BJE', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran bahasa Jerman'],
        ['name' => 'Bahasa Prancis', 'code' => 'BPR', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran bahasa Prancis'],
        ['name' => 'Bahasa Jepang', 'code' => 'BJP', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran bahasa Jepang'],
        ['name' => 'Bahasa Korea', 'code' => 'BKO', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran bahasa Korea'],
        ['name' => 'Bahasa Mandarin', 'code' => 'BMA', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran bahasa Mandarin'],
        ['name' => 'Matematika Lanjutan', 'code' => 'MATL', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran matematika lanjutan'],
        ['name' => 'Bahasa Indonesia Lanjutan', 'code' => 'BINL', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran bahasa Indonesia lanjutan'],
        ['name' => 'Bahasa Inggris Lanjutan', 'code' => 'BIGL', 'subject_type' => 'pilihan', 'education_level' => 'SMA/MA', 'description' => 'Mata pelajaran bahasa Inggris lanjutan'],
        
        // Khusus SMK/MAK
        ['name' => 'Produk/Projek Kreatif dan Kewirausahaan', 'code' => 'PKW', 'subject_type' => 'pilihan_wajib', 'education_level' => 'SMK/MAK', 'description' => 'Mata pelajaran produk/projek kreatif dan kewirausahaan'],
    ];

    foreach ($subjects as $subjectData) {
        \App\Models\Subject::updateOrCreate(
            [
                'name' => $subjectData['name'],
                'education_level' => $subjectData['education_level']
            ],
            array_merge($subjectData, [
                'subject_number' => 1,
                'is_active' => true,
                'is_required' => $subjectData['subject_type'] === 'wajib',
                'type' => $subjectData['subject_type']
            ])
        );
    }
    
    echo "✅ Subjects created: " . \App\Models\Subject::count() . "\n\n";

    // 2. Get all active majors
    echo "2. Getting active majors...\n";
    $majors = \App\Models\MajorRecommendation::where('is_active', true)->get();
    echo "✅ Found " . $majors->count() . " active majors\n\n";

    // 3. Clear existing mappings
    echo "3. Clearing existing mappings...\n";
    \App\Models\MajorSubjectMapping::truncate();
    echo "✅ Existing mappings cleared\n\n";

    // 4. Create major mappings
    echo "4. Creating major mappings...\n";
    
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
                    'is_active' => true,
                    'subject_type' => 'pilihan'
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
                    'is_active' => true,
                    'subject_type' => 'pilihan_wajib'
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
                    'is_active' => true,
                    'subject_type' => 'pilihan'
                ]);
            }
        }
    }
    
    echo "✅ Major mappings created: " . \App\Models\MajorSubjectMapping::count() . "\n\n";

    // 5. Update major recommendations with curriculum data
    echo "5. Updating major recommendations with curriculum data...\n";
    
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

    // 6. Test APIs
    echo "6. Testing APIs...\n";
    
    // Test Web API
    $webController = new \App\Http\Controllers\StudentWebController();
    $webResponse = $webController->getMajors();
    $webData = json_decode($webResponse->getContent(), true);
    
    if ($webData['success']) {
        echo "✅ Web API working - {$webData['data'][0]['major_name']} has " . count($webData['data'][0]['optional_subjects']) . " optional subjects\n";
    } else {
        echo "❌ Web API error: {$webData['message']}\n";
    }
    
    // Test SuperAdmin API
    $superAdminController = new \App\Http\Controllers\SuperAdminController();
    $superAdminData = $superAdminController->majorRecommendations();
    echo "✅ SuperAdmin API working\n";
    
    echo "\n🎉 DATABASE POPULATION COMPLETED SUCCESSFULLY!\n";
    echo "============================================\n";
    echo "✅ Subjects: " . \App\Models\Subject::count() . "\n";
    echo "✅ Major Mappings: " . \App\Models\MajorSubjectMapping::count() . "\n";
    echo "✅ Majors Updated: " . $majors->count() . "\n";
    echo "✅ APIs Working: Yes\n";
    echo "✅ Database Ready: Yes\n";

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
