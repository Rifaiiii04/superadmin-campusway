<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\MajorRecommendation;
use App\Models\MajorSubjectMapping;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding subjects and major mappings...');

        // 1. Seed Subjects
        $this->seedSubjects();
        
        // 2. Seed Major Subject Mappings
        $this->seedMajorSubjectMappings();
        
        // 3. Update Major Recommendations with curriculum data
        $this->updateMajorRecommendations();

        $this->command->info('✅ Subject seeding completed!');
    }

    private function seedSubjects()
    {
        $this->command->info('📚 Seeding subjects...');

        $subjects = [
            // Mata Pelajaran Wajib SMA/MA
            [
                'name' => 'Matematika',
                'code' => 'MAT',
                'description' => 'Mata pelajaran matematika dasar',
                'subject_type' => 'wajib',
                'education_level' => 'SMA/MA',
                'subject_number' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Bahasa Indonesia',
                'code' => 'BIN',
                'description' => 'Mata pelajaran bahasa Indonesia',
                'subject_type' => 'wajib',
                'education_level' => 'SMA/MA',
                'subject_number' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Bahasa Inggris',
                'code' => 'BIG',
                'description' => 'Mata pelajaran bahasa Inggris',
                'subject_type' => 'wajib',
                'education_level' => 'SMA/MA',
                'subject_number' => 3,
                'is_active' => true
            ],

            // Mata Pelajaran Wajib SMK/MAK
            [
                'name' => 'Matematika',
                'code' => 'MAT',
                'description' => 'Mata pelajaran matematika dasar',
                'subject_type' => 'wajib',
                'education_level' => 'SMK/MAK',
                'subject_number' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Bahasa Indonesia',
                'code' => 'BIN',
                'description' => 'Mata pelajaran bahasa Indonesia',
                'subject_type' => 'wajib',
                'education_level' => 'SMK/MAK',
                'subject_number' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Bahasa Inggris',
                'code' => 'BIG',
                'description' => 'Mata pelajaran bahasa Inggris',
                'subject_type' => 'wajib',
                'education_level' => 'SMK/MAK',
                'subject_number' => 3,
                'is_active' => true
            ],

            // Mata Pelajaran Pilihan (18 mata pelajaran untuk SMA/MA)
            [
                'name' => 'Fisika',
                'code' => 'FIS',
                'description' => 'Mata pelajaran fisika',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Kimia',
                'code' => 'KIM',
                'description' => 'Mata pelajaran kimia',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Biologi',
                'code' => 'BIO',
                'description' => 'Mata pelajaran biologi',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Ekonomi',
                'code' => 'EKO',
                'description' => 'Mata pelajaran ekonomi',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 4,
                'is_active' => true
            ],
            [
                'name' => 'Sosiologi',
                'code' => 'SOS',
                'description' => 'Mata pelajaran sosiologi',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 5,
                'is_active' => true
            ],
            [
                'name' => 'Geografi',
                'code' => 'GEO',
                'description' => 'Mata pelajaran geografi',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 6,
                'is_active' => true
            ],
            [
                'name' => 'Sejarah',
                'code' => 'SEJ',
                'description' => 'Mata pelajaran sejarah',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 7,
                'is_active' => true
            ],
            [
                'name' => 'Antropologi',
                'code' => 'ANT',
                'description' => 'Mata pelajaran antropologi',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 8,
                'is_active' => true
            ],
            [
                'name' => 'PPKn/Pendidikan Pancasila',
                'code' => 'PPKN',
                'description' => 'Mata pelajaran PPKn/Pendidikan Pancasila',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 9,
                'is_active' => true
            ],
            [
                'name' => 'Bahasa Arab',
                'code' => 'BAR',
                'description' => 'Mata pelajaran bahasa Arab',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 10,
                'is_active' => true
            ],
            [
                'name' => 'Bahasa Jerman',
                'code' => 'BJE',
                'description' => 'Mata pelajaran bahasa Jerman',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 11,
                'is_active' => true
            ],
            [
                'name' => 'Bahasa Prancis',
                'code' => 'BPR',
                'description' => 'Mata pelajaran bahasa Prancis',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 12,
                'is_active' => true
            ],
            [
                'name' => 'Bahasa Jepang',
                'code' => 'BJP',
                'description' => 'Mata pelajaran bahasa Jepang',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 13,
                'is_active' => true
            ],
            [
                'name' => 'Bahasa Korea',
                'code' => 'BKO',
                'description' => 'Mata pelajaran bahasa Korea',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 14,
                'is_active' => true
            ],
            [
                'name' => 'Bahasa Mandarin',
                'code' => 'BMA',
                'description' => 'Mata pelajaran bahasa Mandarin',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 15,
                'is_active' => true
            ],
            [
                'name' => 'Matematika Lanjutan',
                'code' => 'MATL',
                'description' => 'Mata pelajaran matematika lanjutan',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 16,
                'is_active' => true
            ],
            [
                'name' => 'Bahasa Indonesia Lanjutan',
                'code' => 'BINL',
                'description' => 'Mata pelajaran bahasa Indonesia lanjutan',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 17,
                'is_active' => true
            ],
            [
                'name' => 'Bahasa Inggris Lanjutan',
                'code' => 'BIGL',
                'description' => 'Mata pelajaran bahasa Inggris lanjutan',
                'subject_type' => 'pilihan',
                'education_level' => 'SMA/MA',
                'subject_number' => 18,
                'is_active' => true
            ],

            // Mata Pelajaran Khusus SMK/MAK
            [
                'name' => 'Produk/Projek Kreatif dan Kewirausahaan',
                'code' => 'PKW',
                'description' => 'Mata pelajaran produk/projek kreatif dan kewirausahaan',
                'subject_type' => 'pilihan_wajib',
                'education_level' => 'SMK/MAK',
                'subject_number' => 1,
                'is_active' => true
            ]
        ];

        foreach ($subjects as $subjectData) {
            Subject::updateOrCreate(
                [
                    'name' => $subjectData['name'],
                    'education_level' => $subjectData['education_level']
                ],
                $subjectData
            );
        }

        $this->command->info('✅ Subjects seeded successfully!');
    }

    private function seedMajorSubjectMappings()
    {
        $this->command->info('🔗 Seeding major subject mappings...');

        // Clear existing mappings
        MajorSubjectMapping::truncate();

        $majors = MajorRecommendation::where('is_active', true)->get();
        
        foreach ($majors as $major) {
            $educationLevel = $this->determineEducationLevel($major->rumpun_ilmu);
            
            if ($educationLevel === 'SMA/MA') {
                $this->mapSMAOptionalSubjects($major);
            } else {
                $this->mapSMKOptionalSubjects($major);
            }
        }

        $this->command->info('✅ Major subject mappings seeded successfully!');
    }

    private function mapSMAOptionalSubjects($major)
    {
        // Get 2 random optional subjects for SMA/MA
        $optionalSubjects = Subject::where('subject_type', 'pilihan')
            ->where('education_level', 'SMA/MA')
            ->inRandomOrder()
            ->limit(2)
            ->get();

        foreach ($optionalSubjects as $subject) {
            MajorSubjectMapping::create([
                'major_id' => $major->id,
                'subject_id' => $subject->id,
                'education_level' => 'SMA/MA',
                'mapping_type' => 'pilihan',
                'priority' => 0,
                'is_active' => true
            ]);
        }
    }

    private function mapSMKOptionalSubjects($major)
    {
        // SMK/MAK: 1 mandatory + 1 optional
        // 1. Add mandatory PKW subject
        $pkwSubject = Subject::where('name', 'Produk/Projek Kreatif dan Kewirausahaan')
            ->where('education_level', 'SMK/MAK')
            ->first();

        if ($pkwSubject) {
            MajorSubjectMapping::create([
                'major_id' => $major->id,
                'subject_id' => $pkwSubject->id,
                'education_level' => 'SMK/MAK',
                'mapping_type' => 'pilihan_wajib',
                'priority' => 1,
                'is_active' => true
            ]);
        }

        // 2. Add 1 random optional subject from SMA/MA list
        $optionalSubject = Subject::where('subject_type', 'pilihan')
            ->where('education_level', 'SMA/MA')
            ->inRandomOrder()
            ->first();

        if ($optionalSubject) {
            MajorSubjectMapping::create([
                'major_id' => $major->id,
                'subject_id' => $optionalSubject->id,
                'education_level' => 'SMK/MAK',
                'mapping_type' => 'pilihan',
                'priority' => 2,
                'is_active' => true
            ]);
        }
    }

    private function updateMajorRecommendations()
    {
        $this->command->info('📋 Updating major recommendations with curriculum data...');

        $majors = MajorRecommendation::where('is_active', true)->get();
        
        foreach ($majors as $major) {
            // Update with sample curriculum data
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

        $this->command->info('✅ Major recommendations updated successfully!');
    }

    private function determineEducationLevel($rumpunIlmu)
    {
        $smaRumpun = ['ILMU ALAM', 'ILMU SOSIAL', 'ILMU BUDAYA', 'HUMANIORA', 'ILMU FORMAL'];
        
        if (in_array($rumpunIlmu, $smaRumpun)) {
            return 'SMA/MA';
        } else {
            return 'SMK/MAK';
        }
    }
}
