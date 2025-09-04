<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MajorRecommendation;
use App\Models\Subject;

class SchoolLevelMajorRecommendationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Berdasarkan ketentuan Pusmendik Kemdikbud:
     * - SMK/MAK: Mata pelajaran pilihan 1-18 + produk kreatif kewirausahaan (19)
     * - SMA/MA: Mata pelajaran umum sesuai kurikulum
     */
    public function run(): void
    {
        $this->command->info('Updating major recommendations based on school level (SMK/MAK vs SMA/MA)...');

        // Update untuk SMK/MAK - menggunakan mata pelajaran sesuai ketentuan Pusmendik
        $this->updateSMKMAKMajors();
        
        // Update untuk SMA/MA - menggunakan mata pelajaran umum
        $this->updateSMAMAMajors();

        $this->command->info('School level major recommendations updated successfully!');
    }

    private function updateSMKMAKMajors()
    {
        $this->command->info('Updating majors for SMK/MAK schools...');

        // Dapatkan mata pelajaran SMK/MAK dari database
        $smkMakSubjects = Subject::where('education_level', 'SMK/MAK')
            ->where('is_active', true)
            ->orderBy('subject_number')
            ->get();

        // Mata pelajaran pilihan SMK/MAK (1-18)
        $smkMakPilihan = $smkMakSubjects->where('subject_type', 'Pilihan')->pluck('name')->toArray();
        
        // Mata pelajaran produk kreatif kewirausahaan (19)
        $smkMakProdukKreatif = $smkMakSubjects->where('subject_type', 'Produk_Kreatif_Kewirausahaan')->pluck('name')->toArray();

        // Mata pelajaran wajib untuk semua jenjang
        $wajibSubjects = ['Bahasa Indonesia', 'Matematika', 'Bahasa Inggris'];

        // Jurusan yang cocok untuk SMK/MAK
        $smkMakMajors = [
            // Teknik & Teknologi
            'Ilmu Komputer /  Informatika' => [
                'required' => array_merge($wajibSubjects, ['Teknik Komputer dan Jaringan']),
                'preferred' => array_merge($wajibSubjects, ['Teknik Komputer dan Jaringan', 'Teknik Informatika']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Teknik Komputer dan Jaringan', 'Teknik Informatika', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Teknik Komputer dan Jaringan', 'Teknik Informatika', 'Fisika', 'Kimia']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Teknik Komputer dan Jaringan', 'Teknik Informatika']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Teknik Komputer dan Jaringan', 'Teknik Informatika'])
            ],
            'Teknik Mesin' => [
                'required' => array_merge($wajibSubjects, ['Teknik Mesin']),
                'preferred' => array_merge($wajibSubjects, ['Teknik Mesin', 'Teknik Kendaraan Ringan']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Teknik Mesin', 'Teknik Kendaraan Ringan', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Teknik Mesin', 'Teknik Kendaraan Ringan', 'Fisika', 'Kimia']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Teknik Mesin', 'Teknik Kendaraan Ringan']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Teknik Mesin', 'Teknik Kendaraan Ringan'])
            ],
            'Teknik Elektro' => [
                'required' => array_merge($wajibSubjects, ['Teknik Elektronika']),
                'preferred' => array_merge($wajibSubjects, ['Teknik Elektronika', 'Teknik Listrik']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Teknik Elektronika', 'Teknik Listrik', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Teknik Elektronika', 'Teknik Listrik', 'Fisika', 'Kimia']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Teknik Elektronika', 'Teknik Listrik']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Teknik Elektronika', 'Teknik Listrik'])
            ],
            'Arsitektur' => [
                'required' => array_merge($wajibSubjects, ['Teknik Sipil']),
                'preferred' => array_merge($wajibSubjects, ['Teknik Sipil', 'Desain Komunikasi Visual']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Teknik Sipil', 'Desain Komunikasi Visual', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Teknik Sipil', 'Desain Komunikasi Visual', 'Fisika', 'Kimia']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Teknik Sipil', 'Desain Komunikasi Visual']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Teknik Sipil', 'Desain Komunikasi Visual'])
            ],
            'Teknik Industri' => [
                'required' => array_merge($wajibSubjects, ['Teknik Mesin']),
                'preferred' => array_merge($wajibSubjects, ['Teknik Mesin', 'Akuntansi']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Teknik Mesin', 'Akuntansi', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Teknik Mesin', 'Akuntansi', 'Fisika', 'Kimia']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Teknik Mesin', 'Akuntansi']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Teknik Mesin', 'Akuntansi'])
            ],
            // Bisnis & Ekonomi
            'Ekonomi' => [
                'required' => array_merge($wajibSubjects, ['Akuntansi']),
                'preferred' => array_merge($wajibSubjects, ['Akuntansi', 'Administrasi Perkantoran']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Akuntansi', 'Administrasi Perkantoran', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Akuntansi', 'Administrasi Perkantoran', 'Fisika', 'Kimia']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Akuntansi', 'Administrasi Perkantoran']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Akuntansi', 'Administrasi Perkantoran'])
            ],
            'Manajemen' => [
                'required' => array_merge($wajibSubjects, ['Administrasi Perkantoran']),
                'preferred' => array_merge($wajibSubjects, ['Administrasi Perkantoran', 'Pemasaran']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Administrasi Perkantoran', 'Pemasaran', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Administrasi Perkantoran', 'Pemasaran', 'Fisika', 'Kimia']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Administrasi Perkantoran', 'Pemasaran']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Administrasi Perkantoran', 'Pemasaran'])
            ],
            'Akuntansi' => [
                'required' => array_merge($wajibSubjects, ['Akuntansi']),
                'preferred' => array_merge($wajibSubjects, ['Akuntansi', 'Administrasi Perkantoran']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Akuntansi', 'Administrasi Perkantoran', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Akuntansi', 'Administrasi Perkantoran', 'Fisika', 'Kimia']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Akuntansi', 'Administrasi Perkantoran']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Akuntansi', 'Administrasi Perkantoran'])
            ],
            // Kesehatan
            'Ilmu Kedokteran' => [
                'required' => array_merge($wajibSubjects, ['Biologi', 'Kimia']),
                'preferred' => array_merge($wajibSubjects, ['Biologi', 'Kimia', 'Fisika']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Biologi', 'Kimia', 'Fisika', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Biologi', 'Kimia', 'Fisika', 'Bahasa Inggris']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Biologi', 'Kimia']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Biologi', 'Kimia'])
            ],
            'Ilmu Farmasi' => [
                'required' => array_merge($wajibSubjects, ['Kimia', 'Biologi']),
                'preferred' => array_merge($wajibSubjects, ['Kimia', 'Biologi', 'Matematika']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Kimia', 'Biologi', 'Matematika', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Kimia', 'Biologi', 'Matematika', 'Bahasa Inggris']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Kimia', 'Biologi']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Kimia', 'Biologi'])
            ],
            // Seni & Desain
            'Seni' => [
                'required' => array_merge($wajibSubjects, ['Desain Komunikasi Visual']),
                'preferred' => array_merge($wajibSubjects, ['Desain Komunikasi Visual', 'Tata Busana']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Desain Komunikasi Visual', 'Tata Busana', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Desain Komunikasi Visual', 'Tata Busana', 'Fisika', 'Kimia']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Desain Komunikasi Visual', 'Tata Busana']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Desain Komunikasi Visual', 'Tata Busana'])
            ],
            'Desain' => [
                'required' => array_merge($wajibSubjects, ['Desain Komunikasi Visual']),
                'preferred' => array_merge($wajibSubjects, ['Desain Komunikasi Visual', 'Tata Busana']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Desain Komunikasi Visual', 'Tata Busana', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Desain Komunikasi Visual', 'Tata Busana', 'Fisika', 'Kimia']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Desain Komunikasi Visual', 'Tata Busana']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Desain Komunikasi Visual', 'Tata Busana'])
            ]
        ];

        // Tambahkan mata pelajaran produk kreatif kewirausahaan ke semua jurusan SMK/MAK
        foreach ($smkMakMajors as $majorName => &$subjects) {
            // Tambahkan produk kreatif kewirausahaan ke preferred subjects
            if (!empty($smkMakProdukKreatif)) {
                $subjects['preferred'] = array_merge($subjects['preferred'], $smkMakProdukKreatif);
                $subjects['kurikulum_merdeka'] = array_merge($subjects['kurikulum_merdeka'], $smkMakProdukKreatif);
                $subjects['kurikulum_2013_ipa'] = array_merge($subjects['kurikulum_2013_ipa'], $smkMakProdukKreatif);
                $subjects['kurikulum_2013_ips'] = array_merge($subjects['kurikulum_2013_ips'], $smkMakProdukKreatif);
                $subjects['kurikulum_2013_bahasa'] = array_merge($subjects['kurikulum_2013_bahasa'], $smkMakProdukKreatif);
            }
        }

        foreach ($smkMakMajors as $majorName => $subjects) {
            $this->updateMajor($majorName, $subjects, 'SMK/MAK');
        }
    }

    private function updateSMAMAMajors()
    {
        $this->command->info('Updating majors for SMA/MA schools...');

        // Dapatkan mata pelajaran SMA/MA dari database
        $smaMaSubjects = Subject::where('education_level', 'SMA/MA')
            ->where('is_active', true)
            ->get();

        // Mata pelajaran wajib untuk semua jenjang
        $wajibSubjects = ['Bahasa Indonesia', 'Matematika', 'Bahasa Inggris'];

        // Jurusan yang cocok untuk SMA/MA - menggunakan mata pelajaran umum
        $smaMaMajors = [
            // Sains & Teknologi
            'Matematika' => [
                'required' => array_merge($wajibSubjects, ['Matematika']),
                'preferred' => array_merge($wajibSubjects, ['Matematika', 'Fisika']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Matematika', 'Fisika', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Matematika', 'Fisika', 'Kimia', 'Bahasa Inggris']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Matematika', 'Bahasa Indonesia']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Matematika', 'Bahasa Indonesia'])
            ],
            'Fisika' => [
                'required' => array_merge($wajibSubjects, ['Fisika', 'Matematika']),
                'preferred' => array_merge($wajibSubjects, ['Fisika', 'Matematika', 'Kimia']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Fisika', 'Matematika', 'Kimia', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Fisika', 'Matematika', 'Kimia', 'Bahasa Inggris']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Fisika', 'Matematika', 'Bahasa Indonesia']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Fisika', 'Matematika', 'Bahasa Indonesia'])
            ],
            'Kimia' => [
                'required' => array_merge($wajibSubjects, ['Kimia', 'Matematika']),
                'preferred' => array_merge($wajibSubjects, ['Kimia', 'Matematika', 'Fisika']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Kimia', 'Matematika', 'Fisika', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Kimia', 'Matematika', 'Fisika', 'Bahasa Inggris']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Kimia', 'Matematika', 'Bahasa Indonesia']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Kimia', 'Matematika', 'Bahasa Indonesia'])
            ],
            'Biologi' => [
                'required' => array_merge($wajibSubjects, ['Biologi', 'Kimia']),
                'preferred' => array_merge($wajibSubjects, ['Biologi', 'Kimia', 'Matematika']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Biologi', 'Kimia', 'Matematika', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Biologi', 'Kimia', 'Matematika', 'Bahasa Inggris']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Biologi', 'Kimia', 'Bahasa Indonesia']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Biologi', 'Kimia', 'Bahasa Indonesia'])
            ],
            // Sosial & Humaniora
            'Ekonomi' => [
                'required' => array_merge($wajibSubjects, ['Ekonomi']),
                'preferred' => array_merge($wajibSubjects, ['Ekonomi', 'Matematika']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Ekonomi', 'Matematika', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Ekonomi', 'Matematika', 'Bahasa Inggris']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Ekonomi', 'Matematika', 'Bahasa Inggris']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Ekonomi', 'Matematika', 'Bahasa Inggris'])
            ],
            'Sosiologi' => [
                'required' => array_merge($wajibSubjects, ['Sosiologi']),
                'preferred' => array_merge($wajibSubjects, ['Sosiologi', 'Bahasa Indonesia']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Sosiologi', 'Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Sosiologi', 'Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Sosiologi', 'Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Sosiologi', 'Bahasa Indonesia', 'Bahasa Inggris'])
            ],
            'Psikologi' => [
                'required' => array_merge($wajibSubjects, ['Biologi']),
                'preferred' => array_merge($wajibSubjects, ['Biologi', 'Bahasa Indonesia']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Biologi', 'Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Biologi', 'Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Biologi', 'Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Biologi', 'Bahasa Indonesia', 'Bahasa Inggris'])
            ],
            'Ilmu Sejarah' => [
                'required' => array_merge($wajibSubjects, ['Sejarah Indonesia']),
                'preferred' => array_merge($wajibSubjects, ['Sejarah Indonesia', 'Bahasa Indonesia']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Sejarah', 'Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Sejarah Indonesia', 'Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Sejarah Indonesia', 'Bahasa Indonesia', 'Bahasa Inggris']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Sejarah Indonesia', 'Bahasa Indonesia', 'Bahasa Inggris'])
            ],
            'Geografi' => [
                'required' => array_merge($wajibSubjects, ['Geografi']),
                'preferred' => array_merge($wajibSubjects, ['Geografi', 'Matematika']),
                'kurikulum_merdeka' => array_merge($wajibSubjects, ['Geografi', 'Matematika', 'Bahasa Inggris']),
                'kurikulum_2013_ipa' => array_merge($wajibSubjects, ['Geografi', 'Matematika', 'Bahasa Inggris']),
                'kurikulum_2013_ips' => array_merge($wajibSubjects, ['Geografi', 'Matematika', 'Bahasa Inggris']),
                'kurikulum_2013_bahasa' => array_merge($wajibSubjects, ['Geografi', 'Matematika', 'Bahasa Inggris'])
            ]
        ];

        foreach ($smaMaMajors as $majorName => $subjects) {
            $this->updateMajor($majorName, $subjects, 'SMA/MA');
        }
    }

    private function updateMajor($majorName, $subjects, $schoolLevel)
    {
        $major = MajorRecommendation::where('major_name', $majorName)->first();
        
        if ($major) {
            $major->update([
                'required_subjects' => $subjects['required'],
                'preferred_subjects' => $subjects['preferred'],
                'kurikulum_merdeka_subjects' => $subjects['kurikulum_merdeka'],
                'kurikulum_2013_ipa_subjects' => $subjects['kurikulum_2013_ipa'],
                'kurikulum_2013_ips_subjects' => $subjects['kurikulum_2013_ips'],
                'kurikulum_2013_bahasa_subjects' => $subjects['kurikulum_2013_bahasa']
            ]);
            
            $this->command->info("✅ Updated {$schoolLevel}: {$majorName}");
        } else {
            $this->command->warn("❌ Major not found: {$majorName}");
        }
    }
}
