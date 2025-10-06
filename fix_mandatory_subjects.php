<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 FIXING MANDATORY SUBJECTS\n";
echo "===========================\n\n";

try {
    // 1. Check current mandatory subjects
    echo "1. Checking current mandatory subjects...\n";
    
    $currentMandatory = \App\Models\Subject::where('subject_type', 'wajib')->get();
    
    echo "   Current mandatory subjects:\n";
    foreach ($currentMandatory as $subject) {
        echo "     - {$subject->name} ({$subject->education_level})\n";
    }
    
    // 2. Define the correct mandatory subjects
    echo "\n2. Setting up correct mandatory subjects...\n";
    
    $mandatorySubjects = [
        [
            'name' => 'Bahasa Indonesia',
            'code' => 'BIN',
            'subject_type' => 'wajib',
            'education_level' => 'SMA/MA',
            'is_active' => true
        ],
        [
            'name' => 'Bahasa Inggris', 
            'code' => 'BIG',
            'subject_type' => 'wajib',
            'education_level' => 'SMA/MA',
            'is_active' => true
        ],
        [
            'name' => 'Matematika',
            'code' => 'MAT',
            'subject_type' => 'wajib', 
            'education_level' => 'SMA/MA',
            'is_active' => true
        ],
        [
            'name' => 'Bahasa Indonesia',
            'code' => 'BIN',
            'subject_type' => 'wajib',
            'education_level' => 'SMK/MAK',
            'is_active' => true
        ],
        [
            'name' => 'Bahasa Inggris',
            'code' => 'BIG', 
            'subject_type' => 'wajib',
            'education_level' => 'SMK/MAK',
            'is_active' => true
        ],
        [
            'name' => 'Matematika',
            'code' => 'MAT',
            'subject_type' => 'wajib',
            'education_level' => 'SMK/MAK', 
            'is_active' => true
        ]
    ];
    
    // 3. Clear existing mandatory subjects
    echo "3. Clearing existing mandatory subjects...\n";
    
    \App\Models\Subject::where('subject_type', 'wajib')->delete();
    echo "   ✅ Existing mandatory subjects cleared\n";
    
    // 4. Create new mandatory subjects
    echo "4. Creating new mandatory subjects...\n";
    
    foreach ($mandatorySubjects as $subjectData) {
        \App\Models\Subject::create($subjectData);
        echo "   ✅ Created: {$subjectData['name']} ({$subjectData['education_level']})\n";
    }
    
    // 5. Update all major recommendations to have correct mandatory subjects
    echo "\n5. Updating major recommendations...\n";
    
    $majors = \App\Models\MajorRecommendation::where('is_active', true)->get();
    
    foreach ($majors as $major) {
        $educationLevel = determineEducationLevel($major->rumpun_ilmu);
        
        // Get mandatory subjects for this education level
        $mandatorySubjectNames = \App\Models\Subject::where('subject_type', 'wajib')
            ->where('education_level', $educationLevel)
            ->pluck('name')
            ->toArray();
        
        // Update the major recommendation
        $major->update([
            'required_subjects' => $mandatorySubjectNames
        ]);
        
        echo "   ✅ Updated {$major->major_name}: " . implode(', ', $mandatorySubjectNames) . "\n";
    }
    
    // 6. Update major_subject_mappings for mandatory subjects
    echo "\n6. Updating major_subject_mappings for mandatory subjects...\n";
    
    // Clear existing mandatory mappings
    \App\Models\MajorSubjectMapping::where('mapping_type', 'wajib')->delete();
    
    foreach ($majors as $major) {
        $educationLevel = determineEducationLevel($major->rumpun_ilmu);
        
        // Get mandatory subjects for this education level
        $mandatorySubjects = \App\Models\Subject::where('subject_type', 'wajib')
            ->where('education_level', $educationLevel)
            ->get();
        
        foreach ($mandatorySubjects as $index => $subject) {
            \App\Models\MajorSubjectMapping::create([
                'major_id' => $major->id,
                'subject_id' => $subject->id,
                'education_level' => $educationLevel,
                'mapping_type' => 'wajib',
                'priority' => $index + 1,
                'is_active' => true
            ]);
        }
        
        echo "   ✅ Updated mappings for {$major->major_name}\n";
    }
    
    // 7. Verify the changes
    echo "\n7. Verifying changes...\n";
    
    $newMandatory = \App\Models\Subject::where('subject_type', 'wajib')->get();
    echo "   New mandatory subjects:\n";
    foreach ($newMandatory as $subject) {
        echo "     - {$subject->name} ({$subject->education_level})\n";
    }
    
    // Test a few majors
    $testMajors = \App\Models\MajorRecommendation::where('is_active', true)->take(3)->get();
    echo "\n   Sample major requirements:\n";
    foreach ($testMajors as $major) {
        $educationLevel = determineEducationLevel($major->rumpun_ilmu);
        $mandatoryNames = \App\Models\Subject::where('subject_type', 'wajib')
            ->where('education_level', $educationLevel)
            ->pluck('name')
            ->toArray();
        echo "     - {$major->major_name} ({$educationLevel}): " . implode(', ', $mandatoryNames) . "\n";
    }

    echo "\n🎉 MANDATORY SUBJECTS FIXED SUCCESSFULLY!\n";
    echo "========================================\n";
    echo "✅ All mandatory subjects set to: Bahasa Indonesia, Bahasa Inggris, Matematika\n";
    echo "✅ Applied to both SMA/MA and SMK/MAK education levels\n";
    echo "✅ Updated all major recommendations\n";
    echo "✅ Updated major_subject_mappings\n";

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
