<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\MajorRecommendation;
use App\Models\MajorSubjectMapping;

class SmartMajorMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🧠 Seeding smart major mappings...');

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

        $this->command->info('✅ Smart major mappings seeded successfully!');
    }

    private function mapSMAOptionalSubjects($major)
    {
        // Get subjects based on major characteristics
        $subjects = $this->getSubjectsForMajor($major);
        
        foreach ($subjects as $index => $subjectName) {
            $subject = Subject::where('name', $subjectName)
                ->where('education_level', 'SMA/MA')
                ->first();
                
            if ($subject) {
                MajorSubjectMapping::create([
                    'major_id' => $major->id,
                    'subject_id' => $subject->id,
                    'education_level' => 'SMA/MA',
                    'mapping_type' => 'pilihan',
                    'priority' => $index + 1,
                    'is_active' => true
                ]);
            }
        }
    }

    private function mapSMKOptionalSubjects($major)
    {
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

        // 2. Add 1 optional subject based on major
        $optionalSubjectName = $this->getOptionalSubjectForSMK($major);
        $optionalSubject = Subject::where('name', $optionalSubjectName)
            ->where('education_level', 'SMA/MA')
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

    private function getSubjectsForMajor($major)
    {
        $majorName = strtolower($major->major_name);
        $rumpunIlmu = $major->rumpun_ilmu;

        // Define subject combinations based on major characteristics
        $subjectCombinations = [
            // Ilmu Alam
            'fisika' => ['Fisika', 'Matematika Lanjutan'],
            'kimia' => ['Kimia', 'Matematika Lanjutan'],
            'biologi' => ['Biologi', 'Kimia'],
            'matematika' => ['Matematika Lanjutan', 'Fisika'],
            'statistika' => ['Matematika Lanjutan', 'Ekonomi'],
            'geofisika' => ['Fisika', 'Geografi'],
            'geologi' => ['Geografi', 'Kimia'],
            'astronomi' => ['Fisika', 'Matematika Lanjutan'],
            'farmasi' => ['Kimia', 'Biologi'],
            'kedokteran' => ['Biologi', 'Kimia'],
            'kesehatan' => ['Biologi', 'Kimia'],
            'teknik' => ['Fisika', 'Matematika Lanjutan'],
            'informatika' => ['Matematika Lanjutan', 'Fisika'],
            'komputer' => ['Matematika Lanjutan', 'Fisika'],
            'sistem' => ['Matematika Lanjutan', 'Fisika'],
            'data' => ['Matematika Lanjutan', 'Ekonomi'],
            'cyber' => ['Matematika Lanjutan', 'Fisika'],
            'artificial' => ['Matematika Lanjutan', 'Fisika'],
            'machine' => ['Matematika Lanjutan', 'Fisika'],
            'robot' => ['Fisika', 'Matematika Lanjutan'],
            'elektro' => ['Fisika', 'Matematika Lanjutan'],
            'mesin' => ['Fisika', 'Matematika Lanjutan'],
            'sipil' => ['Fisika', 'Matematika Lanjutan'],
            'arsitektur' => ['Matematika Lanjutan', 'Geografi'],
            'lingkungan' => ['Geografi', 'Kimia'],
            'pertanian' => ['Biologi', 'Kimia'],
            'kehutanan' => ['Biologi', 'Geografi'],
            'perikanan' => ['Biologi', 'Kimia'],
            'peternakan' => ['Biologi', 'Kimia'],
            'veteriner' => ['Biologi', 'Kimia'],

            // Ilmu Sosial
            'ekonomi' => ['Ekonomi', 'Matematika Lanjutan'],
            'manajemen' => ['Ekonomi', 'Sosiologi'],
            'akuntansi' => ['Ekonomi', 'Matematika Lanjutan'],
            'bisnis' => ['Ekonomi', 'Sosiologi'],
            'pemasaran' => ['Ekonomi', 'Sosiologi'],
            'keuangan' => ['Ekonomi', 'Matematika Lanjutan'],
            'perbankan' => ['Ekonomi', 'Matematika Lanjutan'],
            'asuransi' => ['Ekonomi', 'Matematika Lanjutan'],
            'pajak' => ['Ekonomi', 'Matematika Lanjutan'],
            'pembangunan' => ['Ekonomi', 'Sosiologi'],
            'koperasi' => ['Ekonomi', 'Sosiologi'],
            'sosiologi' => ['Sosiologi', 'Antropologi'],
            'antropologi' => ['Antropologi', 'Sosiologi'],
            'psikologi' => ['Sosiologi', 'Antropologi'],
            'komunikasi' => ['Sosiologi', 'Bahasa Indonesia Lanjutan'],
            'jurnalistik' => ['Bahasa Indonesia Lanjutan', 'Sosiologi'],
            'hubungan' => ['Sosiologi', 'Bahasa Indonesia Lanjutan'],
            'publik' => ['Sosiologi', 'Bahasa Indonesia Lanjutan'],
            'politik' => ['Sosiologi', 'PPKn/Pendidikan Pancasila'],
            'pemerintahan' => ['PPKn/Pendidikan Pancasila', 'Sosiologi'],
            'administrasi' => ['Sosiologi', 'Ekonomi'],
            'kebijakan' => ['Sosiologi', 'PPKn/Pendidikan Pancasila'],
            'hukum' => ['PPKn/Pendidikan Pancasila', 'Sosiologi'],
            'kriminologi' => ['Sosiologi', 'PPKn/Pendidikan Pancasila'],
            'kesejahteraan' => ['Sosiologi', 'Antropologi'],
            'pembangunan' => ['Sosiologi', 'Ekonomi'],
            'perencanaan' => ['Sosiologi', 'Geografi'],
            'geografi' => ['Geografi', 'Sosiologi'],
            'demografi' => ['Geografi', 'Sosiologi'],
            'kependudukan' => ['Geografi', 'Sosiologi'],
            'lingkungan' => ['Geografi', 'Sosiologi'],
            'pariwisata' => ['Geografi', 'Sosiologi'],
            'perhotelan' => ['Sosiologi', 'Bahasa Inggris Lanjutan'],
            'kepariwisataan' => ['Geografi', 'Sosiologi'],

            // Humaniora
            'bahasa' => ['Bahasa Indonesia Lanjutan', 'Bahasa Inggris Lanjutan'],
            'sastra' => ['Bahasa Indonesia Lanjutan', 'Bahasa Inggris Lanjutan'],
            'linguistik' => ['Bahasa Indonesia Lanjutan', 'Bahasa Inggris Lanjutan'],
            'filologi' => ['Bahasa Indonesia Lanjutan', 'Sejarah'],
            'sejarah' => ['Sejarah', 'Antropologi'],
            'arkeologi' => ['Sejarah', 'Antropologi'],
            'filsafat' => ['Antropologi', 'Sosiologi'],
            'seni' => ['Bahasa Indonesia Lanjutan', 'Sosiologi'],
            'musik' => ['Bahasa Indonesia Lanjutan', 'Sosiologi'],
            'tari' => ['Bahasa Indonesia Lanjutan', 'Sosiologi'],
            'teater' => ['Bahasa Indonesia Lanjutan', 'Sosiologi'],
            'rupa' => ['Bahasa Indonesia Lanjutan', 'Sosiologi'],
            'desain' => ['Bahasa Indonesia Lanjutan', 'Sosiologi'],
            'kriya' => ['Bahasa Indonesia Lanjutan', 'Sosiologi'],
            'budaya' => ['Antropologi', 'Sosiologi'],
            'antropologi' => ['Antropologi', 'Sosiologi'],
            'etnologi' => ['Antropologi', 'Sosiologi'],
            'folklor' => ['Antropologi', 'Bahasa Indonesia Lanjutan'],
            'tradisi' => ['Antropologi', 'Bahasa Indonesia Lanjutan'],
            'agama' => ['PPKn/Pendidikan Pancasila', 'Antropologi'],
            'islam' => ['PPKn/Pendidikan Pancasila', 'Bahasa Arab'],
            'kristen' => ['PPKn/Pendidikan Pancasila', 'Bahasa Inggris Lanjutan'],
            'hindu' => ['PPKn/Pendidikan Pancasila', 'Antropologi'],
            'buddha' => ['PPKn/Pendidikan Pancasila', 'Antropologi'],
            'konghucu' => ['PPKn/Pendidikan Pancasila', 'Bahasa Mandarin'],
        ];

        // Find matching subjects
        foreach ($subjectCombinations as $keyword => $subjects) {
            if (strpos($majorName, $keyword) !== false) {
                return $subjects;
            }
        }

        // Default based on rumpun ilmu
        switch ($rumpunIlmu) {
            case 'ILMU ALAM':
                return ['Fisika', 'Matematika Lanjutan'];
            case 'ILMU SOSIAL':
                return ['Ekonomi', 'Sosiologi'];
            case 'HUMANIORA':
                return ['Bahasa Indonesia Lanjutan', 'Antropologi'];
            case 'ILMU FORMAL':
                return ['Matematika Lanjutan', 'Fisika'];
            case 'ILMU TERAPAN':
                return ['Fisika', 'Matematika Lanjutan'];
            default:
                return ['Bahasa Indonesia Lanjutan', 'Bahasa Inggris Lanjutan'];
        }
    }

    private function getOptionalSubjectForSMK($major)
    {
        $majorName = strtolower($major->major_name);
        
        // SMK subjects mapping
        $smkSubjects = [
            'teknik' => 'Matematika Lanjutan',
            'informatika' => 'Matematika Lanjutan',
            'komputer' => 'Matematika Lanjutan',
            'elektro' => 'Fisika',
            'mesin' => 'Fisika',
            'sipil' => 'Fisika',
            'arsitektur' => 'Geografi',
            'desain' => 'Bahasa Indonesia Lanjutan',
            'seni' => 'Bahasa Indonesia Lanjutan',
            'musik' => 'Bahasa Indonesia Lanjutan',
            'tari' => 'Bahasa Indonesia Lanjutan',
            'teater' => 'Bahasa Indonesia Lanjutan',
            'rupa' => 'Bahasa Indonesia Lanjutan',
            'kriya' => 'Bahasa Indonesia Lanjutan',
            'perhotelan' => 'Bahasa Inggris Lanjutan',
            'pariwisata' => 'Geografi',
            'kepariwisataan' => 'Geografi',
            'akuntansi' => 'Matematika Lanjutan',
            'manajemen' => 'Ekonomi',
            'pemasaran' => 'Ekonomi',
            'bisnis' => 'Ekonomi',
            'keuangan' => 'Matematika Lanjutan',
            'perbankan' => 'Matematika Lanjutan',
            'asuransi' => 'Matematika Lanjutan',
            'pajak' => 'Matematika Lanjutan',
            'komunikasi' => 'Bahasa Indonesia Lanjutan',
            'jurnalistik' => 'Bahasa Indonesia Lanjutan',
            'hubungan' => 'Bahasa Indonesia Lanjutan',
            'publik' => 'Bahasa Indonesia Lanjutan',
            'administrasi' => 'Sosiologi',
            'hukum' => 'PPKn/Pendidikan Pancasila',
            'kesehatan' => 'Biologi',
            'farmasi' => 'Kimia',
            'kedokteran' => 'Biologi',
            'perawat' => 'Biologi',
            'gizi' => 'Biologi',
            'kebidanan' => 'Biologi',
            'fisioterapi' => 'Biologi',
            'farmasi' => 'Kimia',
            'pertanian' => 'Biologi',
            'kehutanan' => 'Biologi',
            'perikanan' => 'Biologi',
            'peternakan' => 'Biologi',
            'veteriner' => 'Biologi',
            'lingkungan' => 'Geografi',
            'geologi' => 'Geografi',
            'geofisika' => 'Fisika',
            'astronomi' => 'Fisika',
            'statistika' => 'Matematika Lanjutan',
            'data' => 'Matematika Lanjutan',
            'cyber' => 'Matematika Lanjutan',
            'artificial' => 'Matematika Lanjutan',
            'machine' => 'Matematika Lanjutan',
            'robot' => 'Fisika',
            'sistem' => 'Matematika Lanjutan',
            'elektro' => 'Fisika',
            'mesin' => 'Fisika',
            'sipil' => 'Fisika',
            'arsitektur' => 'Geografi',
            'desain' => 'Bahasa Indonesia Lanjutan',
            'seni' => 'Bahasa Indonesia Lanjutan',
            'musik' => 'Bahasa Indonesia Lanjutan',
            'tari' => 'Bahasa Indonesia Lanjutan',
            'teater' => 'Bahasa Indonesia Lanjutan',
            'rupa' => 'Bahasa Indonesia Lanjutan',
            'kriya' => 'Bahasa Indonesia Lanjutan',
            'perhotelan' => 'Bahasa Inggris Lanjutan',
            'pariwisata' => 'Geografi',
            'kepariwisataan' => 'Geografi',
            'akuntansi' => 'Matematika Lanjutan',
            'manajemen' => 'Ekonomi',
            'pemasaran' => 'Ekonomi',
            'bisnis' => 'Ekonomi',
            'keuangan' => 'Matematika Lanjutan',
            'perbankan' => 'Matematika Lanjutan',
            'asuransi' => 'Matematika Lanjutan',
            'pajak' => 'Matematika Lanjutan',
            'komunikasi' => 'Bahasa Indonesia Lanjutan',
            'jurnalistik' => 'Bahasa Indonesia Lanjutan',
            'hubungan' => 'Bahasa Indonesia Lanjutan',
            'publik' => 'Bahasa Indonesia Lanjutan',
            'administrasi' => 'Sosiologi',
            'hukum' => 'PPKn/Pendidikan Pancasila',
            'kesehatan' => 'Biologi',
            'farmasi' => 'Kimia',
            'kedokteran' => 'Biologi',
            'perawat' => 'Biologi',
            'gizi' => 'Biologi',
            'kebidanan' => 'Biologi',
            'fisioterapi' => 'Biologi',
            'pertanian' => 'Biologi',
            'kehutanan' => 'Biologi',
            'perikanan' => 'Biologi',
            'peternakan' => 'Biologi',
            'veteriner' => 'Biologi',
            'lingkungan' => 'Geografi',
            'geologi' => 'Geografi',
            'geofisika' => 'Fisika',
            'astronomi' => 'Fisika',
            'statistika' => 'Matematika Lanjutan',
            'data' => 'Matematika Lanjutan',
            'cyber' => 'Matematika Lanjutan',
            'artificial' => 'Matematika Lanjutan',
            'machine' => 'Matematika Lanjutan',
            'robot' => 'Fisika',
            'sistem' => 'Matematika Lanjutan',
        ];

        foreach ($smkSubjects as $keyword => $subject) {
            if (strpos($majorName, $keyword) !== false) {
                return $subject;
            }
        }

        // Default for SMK
        return 'Matematika Lanjutan';
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
