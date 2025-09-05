<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateMajorRecommendationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🔄 Starting Major Recommendations Update...\n";

        // 1. Update all existing majors to use proper rumpun_ilmu
        echo "📚 Step 1: Updating existing majors with proper rumpun_ilmu...\n";
        
        // Get all existing majors
        $majors = DB::table('major_recommendations')->get();
        echo "Found " . $majors->count() . " existing majors\n";

        // Define mapping of major names to rumpun_ilmu
        $majorMapping = [
            // HUMANIORA
            'Seni' => 'HUMANIORA',
            'Sejarah' => 'HUMANIORA',
            'Linguistik' => 'HUMANIORA',
            'Sastra' => 'HUMANIORA',
            'Filsafat' => 'HUMANIORA',
            'Bahasa' => 'HUMANIORA',
            'Sastra Indonesia' => 'HUMANIORA',
            'Sastra Inggris' => 'HUMANIORA',
            'Sastra Arab' => 'HUMANIORA',
            'Sastra Jepang' => 'HUMANIORA',
            'Sastra Korea' => 'HUMANIORA',
            'Sastra Mandarin' => 'HUMANIORA',
            'Sastra Prancis' => 'HUMANIORA',
            'Sastra Jerman' => 'HUMANIORA',
            'Seni Rupa' => 'HUMANIORA',
            'Seni Musik' => 'HUMANIORA',
            'Seni Tari' => 'HUMANIORA',
            'Seni Teater' => 'HUMANIORA',
            'Desain Komunikasi Visual' => 'HUMANIORA',
            'Desain Interior' => 'HUMANIORA',
            'Desain Produk' => 'HUMANIORA',
            'Fashion Design' => 'HUMANIORA',
            'Antropologi' => 'HUMANIORA',
            'Arkeologi' => 'HUMANIORA',
            'Sejarah' => 'HUMANIORA',
            'Ilmu Sejarah' => 'HUMANIORA',

            // ILMU SOSIAL
            'Sosiologi' => 'ILMU SOSIAL',
            'Psikologi' => 'ILMU SOSIAL',
            'Ekonomi' => 'ILMU SOSIAL',
            'Manajemen' => 'ILMU SOSIAL',
            'Administrasi Bisnis' => 'ILMU SOSIAL',
            'Administrasi Negara' => 'ILMU SOSIAL',
            'Administrasi Publik' => 'ILMU SOSIAL',
            'Hubungan Internasional' => 'ILMU SOSIAL',
            'Ilmu Politik' => 'ILMU SOSIAL',
            'Ilmu Komunikasi' => 'ILMU SOSIAL',
            'Jurnalistik' => 'ILMU SOSIAL',
            'Public Relations' => 'ILMU SOSIAL',
            'Periklanan' => 'ILMU SOSIAL',
            'Penyiaran' => 'ILMU SOSIAL',
            'Akuntansi' => 'ILMU SOSIAL',
            'Keuangan' => 'ILMU SOSIAL',
            'Perbankan' => 'ILMU SOSIAL',
            'Pemasaran' => 'ILMU SOSIAL',
            'Bisnis' => 'ILMU SOSIAL',
            'Kewirausahaan' => 'ILMU SOSIAL',
            'Geografi' => 'ILMU SOSIAL',
            'Ilmu Geografi' => 'ILMU SOSIAL',
            'Pendidikan' => 'ILMU SOSIAL',
            'Pendidikan Guru' => 'ILMU SOSIAL',
            'Bimbingan Konseling' => 'ILMU SOSIAL',
            'Kesejahteraan Sosial' => 'ILMU SOSIAL',
            'Ilmu Kesejahteraan Sosial' => 'ILMU SOSIAL',
            'Kriminologi' => 'ILMU SOSIAL',
            'Ilmu Kriminologi' => 'ILMU SOSIAL',

            // ILMU ALAM
            'Fisika' => 'ILMU ALAM',
            'Kimia' => 'ILMU ALAM',
            'Biologi' => 'ILMU ALAM',
            'Matematika' => 'ILMU ALAM',
            'Statistika' => 'ILMU ALAM',
            'Astronomi' => 'ILMU ALAM',
            'Geofisika' => 'ILMU ALAM',
            'Geologi' => 'ILMU ALAM',
            'Meteorologi' => 'ILMU ALAM',
            'Oseanografi' => 'ILMU ALAM',
            'Ilmu Kelautan' => 'ILMU ALAM',
            'Ilmu Kebumian' => 'ILMU ALAM',
            'Ilmu Lingkungan' => 'ILMU ALAM',
            'Biokimia' => 'ILMU ALAM',
            'Biofisika' => 'ILMU ALAM',
            'Biologi Molekuler' => 'ILMU ALAM',
            'Mikrobiologi' => 'ILMU ALAM',
            'Botani' => 'ILMU ALAM',
            'Zoologi' => 'ILMU ALAM',
            'Ekologi' => 'ILMU ALAM',
            'Farmasi' => 'ILMU ALAM',
            'Kedokteran' => 'ILMU ALAM',
            'Kedokteran Gigi' => 'ILMU ALAM',
            'Kedokteran Hewan' => 'ILMU ALAM',
            'Keperawatan' => 'ILMU ALAM',
            'Kesehatan Masyarakat' => 'ILMU ALAM',
            'Gizi' => 'ILMU ALAM',
            'Ilmu Gizi' => 'ILMU ALAM',
            'Kesehatan Lingkungan' => 'ILMU ALAM',
            'Epidemiologi' => 'ILMU ALAM',
            'Biomedis' => 'ILMU ALAM',
            'Ilmu Biomedis' => 'ILMU ALAM',

            // ILMU FORMAL
            'Matematika Murni' => 'ILMU FORMAL',
            'Matematika Terapan' => 'ILMU FORMAL',
            'Statistika' => 'ILMU FORMAL',
            'Aktuaria' => 'ILMU FORMAL',
            'Ilmu Aktuaria' => 'ILMU FORMAL',
            'Logika' => 'ILMU FORMAL',
            'Ilmu Logika' => 'ILMU FORMAL',
            'Filsafat' => 'ILMU FORMAL',
            'Ilmu Filsafat' => 'ILMU FORMAL',

            // ILMU TERAPAN
            'Teknik' => 'ILMU TERAPAN',
            'Teknik Sipil' => 'ILMU TERAPAN',
            'Teknik Mesin' => 'ILMU TERAPAN',
            'Teknik Elektro' => 'ILMU TERAPAN',
            'Teknik Informatika' => 'ILMU TERAPAN',
            'Sistem Informasi' => 'ILMU TERAPAN',
            'Teknologi Informasi' => 'ILMU TERAPAN',
            'Teknik Komputer' => 'ILMU TERAPAN',
            'Teknik Industri' => 'ILMU TERAPAN',
            'Teknik Kimia' => 'ILMU TERAPAN',
            'Teknik Lingkungan' => 'ILMU TERAPAN',
            'Teknik Pertambangan' => 'ILMU TERAPAN',
            'Teknik Perminyakan' => 'ILMU TERAPAN',
            'Teknik Geologi' => 'ILMU TERAPAN',
            'Teknik Geofisika' => 'ILMU TERAPAN',
            'Teknik Nuklir' => 'ILMU TERAPAN',
            'Teknik Material' => 'ILMU TERAPAN',
            'Teknik Metalurgi' => 'ILMU TERAPAN',
            'Teknik Fisika' => 'ILMU TERAPAN',
            'Teknik Biomedis' => 'ILMU TERAPAN',
            'Teknik Penerbangan' => 'ILMU TERAPAN',
            'Teknik Dirgantara' => 'ILMU TERAPAN',
            'Teknik Kelautan' => 'ILMU TERAPAN',
            'Teknik Perkapalan' => 'ILMU TERAPAN',
            'Teknik Perikanan' => 'ILMU TERAPAN',
            'Teknik Pertanian' => 'ILMU TERAPAN',
            'Teknik Pangan' => 'ILMU TERAPAN',
            'Teknik Biosistem' => 'ILMU TERAPAN',
            'Teknik Sipil' => 'ILMU TERAPAN',
            'Arsitektur' => 'ILMU TERAPAN',
            'Perencanaan Wilayah dan Kota' => 'ILMU TERAPAN',
            'Teknik Geomatika' => 'ILMU TERAPAN',
            'Teknik Geodesi' => 'ILMU TERAPAN',
            'Teknik Geologi' => 'ILMU TERAPAN',
            'Teknik Geofisika' => 'ILMU TERAPAN',
            'Teknik Pertambangan' => 'ILMU TERAPAN',
            'Teknik Perminyakan' => 'ILMU TERAPAN',
            'Teknik Nuklir' => 'ILMU TERAPAN',
            'Teknik Material' => 'ILMU TERAPAN',
            'Teknik Metalurgi' => 'ILMU TERAPAN',
            'Teknik Fisika' => 'ILMU TERAPAN',
            'Teknik Biomedis' => 'ILMU TERAPAN',
            'Teknik Penerbangan' => 'ILMU TERAPAN',
            'Teknik Dirgantara' => 'ILMU TERAPAN',
            'Teknik Kelautan' => 'ILMU TERAPAN',
            'Teknik Perkapalan' => 'ILMU TERAPAN',
            'Teknik Perikanan' => 'ILMU TERAPAN',
            'Teknik Pertanian' => 'ILMU TERAPAN',
            'Teknik Pangan' => 'ILMU TERAPAN',
            'Teknik Biosistem' => 'ILMU TERAPAN',
            'Arsitektur' => 'ILMU TERAPAN',
            'Perencanaan Wilayah dan Kota' => 'ILMU TERAPAN',
            'Teknik Geomatika' => 'ILMU TERAPAN',
            'Teknik Geodesi' => 'ILMU TERAPAN',
        ];

        $updatedCount = 0;
        $defaultCount = 0;

        foreach ($majors as $major) {
            $majorName = $major->major_name;
            $rumpunIlmu = 'ILMU ALAM'; // Default

            // Try to find exact match
            if (isset($majorMapping[$majorName])) {
                $rumpunIlmu = $majorMapping[$majorName];
            } else {
                // Try partial match
                foreach ($majorMapping as $key => $value) {
                    if (stripos($majorName, $key) !== false) {
                        $rumpunIlmu = $value;
                        break;
                    }
                }
            }

            // Update the major
            DB::table('major_recommendations')
                ->where('id', $major->id)
                ->update(['rumpun_ilmu' => $rumpunIlmu]);

            if ($rumpunIlmu !== 'ILMU ALAM') {
                $updatedCount++;
            } else {
                $defaultCount++;
            }
        }

        echo "✅ Updated {$updatedCount} majors with specific rumpun_ilmu\n";
        echo "⚠️ {$defaultCount} majors set to default ILMU ALAM\n";

        // 2. Update subjects data for each major based on rumpun_ilmu
        echo "\n📖 Step 2: Updating subjects data for each major...\n";

        // Get subjects mapping
        $subjects = DB::table('subjects')->pluck('id', 'code')->toArray();

        $majorsWithSubjects = DB::table('major_recommendations')->get();

        foreach ($majorsWithSubjects as $major) {
            $subjectsToAdd = [];

            switch ($major->rumpun_ilmu) {
                case 'HUMANIORA':
                    $subjectsToAdd = [
                        'merdeka' => ['BIN_L', 'BIG_L', 'BAR', 'BJE', 'BPR', 'BJP', 'BKO', 'BMA'],
                        '2013_ipa' => ['BIN_L', 'BIG_L', 'BAR', 'BJE', 'BPR', 'BJP', 'BKO', 'BMA'],
                        '2013_ips' => ['BIN_L', 'BIG_L', 'BAR', 'BJE', 'BPR', 'BJP', 'BKO', 'BMA'],
                        '2013_bahasa' => ['BIN_L', 'BIG_L', 'BAR', 'BJE', 'BPR', 'BJP', 'BKO', 'BMA'],
                    ];
                    break;

                case 'ILMU SOSIAL':
                    $subjectsToAdd = [
                        'merdeka' => ['MTK_L', 'EKO', 'SOS', 'SEJ', 'GEO', 'ANT', 'PPKN'],
                        '2013_ipa' => ['MTK_L', 'EKO', 'SOS', 'SEJ', 'GEO', 'ANT', 'PPKN'],
                        '2013_ips' => ['MTK_L', 'EKO', 'SOS', 'SEJ', 'GEO', 'ANT', 'PPKN'],
                        '2013_bahasa' => ['MTK_L', 'EKO', 'SOS', 'SEJ', 'GEO', 'ANT', 'PPKN'],
                    ];
                    break;

                case 'ILMU ALAM':
                    $subjectsToAdd = [
                        'merdeka' => ['MTK_L', 'FIS', 'KIM', 'BIO'],
                        '2013_ipa' => ['MTK_L', 'FIS', 'KIM', 'BIO'],
                        '2013_ips' => ['MTK_L', 'FIS', 'KIM', 'BIO'],
                        '2013_bahasa' => ['MTK_L', 'FIS', 'KIM', 'BIO'],
                    ];
                    break;

                case 'ILMU FORMAL':
                    $subjectsToAdd = [
                        'merdeka' => ['MTK_L'],
                        '2013_ipa' => ['MTK_L'],
                        '2013_ips' => ['MTK_L'],
                        '2013_bahasa' => ['MTK_L'],
                    ];
                    break;

                case 'ILMU TERAPAN':
                    $subjectsToAdd = [
                        'merdeka' => ['MTK_L', 'FIS', 'KIM', 'BIO', 'PKK'],
                        '2013_ipa' => ['MTK_L', 'FIS', 'KIM', 'BIO', 'PKK'],
                        '2013_ips' => ['MTK_L', 'FIS', 'KIM', 'BIO', 'PKK'],
                        '2013_bahasa' => ['MTK_L', 'FIS', 'KIM', 'BIO', 'PKK'],
                    ];
                    break;
            }

            // Update major with subjects
            $updateData = [];
            foreach ($subjectsToAdd as $kurikulum => $subjectCodes) {
                $subjectNames = [];
                foreach ($subjectCodes as $code) {
                    if (isset($subjects[$code])) {
                        $subject = DB::table('subjects')->where('code', $code)->first();
                        if ($subject) {
                            $subjectNames[] = $subject->name;
                        }
                    }
                }
                
                switch ($kurikulum) {
                    case 'merdeka':
                        $updateData['kurikulum_merdeka_subjects'] = json_encode($subjectNames);
                        break;
                    case '2013_ipa':
                        $updateData['kurikulum_2013_ipa_subjects'] = json_encode($subjectNames);
                        break;
                    case '2013_ips':
                        $updateData['kurikulum_2013_ips_subjects'] = json_encode($subjectNames);
                        break;
                    case '2013_bahasa':
                        $updateData['kurikulum_2013_bahasa_subjects'] = json_encode($subjectNames);
                        break;
                }
            }

            if (!empty($updateData)) {
                DB::table('major_recommendations')
                    ->where('id', $major->id)
                    ->update($updateData);
            }
        }

        echo "✅ Updated subjects data for all majors\n";

        // 3. Show summary by rumpun_ilmu
        echo "\n📊 Step 3: Summary by rumpun_ilmu...\n";
        
        $summary = DB::table('major_recommendations')
            ->select('rumpun_ilmu', DB::raw('COUNT(*) as count'))
            ->groupBy('rumpun_ilmu')
            ->get();

        foreach ($summary as $item) {
            echo "  - {$item->rumpun_ilmu}: {$item->count} majors\n";
        }

        echo "\n🎉 Major Recommendations Update Completed Successfully!\n";
    }
}
