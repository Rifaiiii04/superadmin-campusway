<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateSubjectsMappingPusmendikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🔄 Starting Subjects Mapping Update to Pusmendik Standard...\n";

        // Clear existing program_studi_subjects mappings
        DB::table('program_studi_subjects')->truncate();
        echo "✅ Cleared existing program_studi_subjects mappings\n";

        // Get subjects and program studi IDs
        $subjects = DB::table('subjects')->pluck('id', 'code')->toArray();
        $programStudi = DB::table('program_studi')->pluck('id', 'name')->toArray();

        // Mapping berdasarkan tabel Pusmendik yang benar
        $mappings = [
            // HUMANIORA
            [
                'program_studi' => 'Seni',
                'rumpun_ilmu' => 'HUMANIORA',
                'subjects' => [
                    'merdeka' => ['SENI'], // Seni Budaya
                    '2013_ipa' => ['SENI'],
                    '2013_ips' => ['SENI'],
                    '2013_bahasa' => ['SENI'],
                ]
            ],
            [
                'program_studi' => 'Sejarah',
                'rumpun_ilmu' => 'HUMANIORA',
                'subjects' => [
                    'merdeka' => ['SEJ'],
                    '2013_ipa' => ['SEJ'],
                    '2013_ips' => ['SEJ'],
                    '2013_bahasa' => ['SEJ'],
                ]
            ],
            [
                'program_studi' => 'Linguistik',
                'rumpun_ilmu' => 'HUMANIORA',
                'subjects' => [
                    'merdeka' => ['BIN_L', 'BIG_L'], // Bahasa Indonesia Tingkat Lanjut dan/atau Bahasa Inggris
                    '2013_ipa' => ['BIN_L', 'BIG_L'],
                    '2013_ips' => ['BIN_L', 'BIG_L'],
                    '2013_bahasa' => ['BIN_L', 'BIG_L'],
                ]
            ],
            [
                'program_studi' => 'Sastra',
                'rumpun_ilmu' => 'HUMANIORA',
                'subjects' => [
                    'merdeka' => ['BIN_L', 'BAR', 'BJE', 'BPR', 'BJP', 'BKO', 'BMA'], // Sastra atau Bahasa Indonesia Tingkat Lanjut dan/atau bahasa asing yang relevan
                    '2013_ipa' => ['BIN_L', 'BAR', 'BJE', 'BPR', 'BJP', 'BKO', 'BMA'],
                    '2013_ips' => ['BIN_L', 'BAR', 'BJE', 'BPR', 'BJP', 'BKO', 'BMA'],
                    '2013_bahasa' => ['BIN_L', 'BAR', 'BJE', 'BPR', 'BJP', 'BKO', 'BMA'],
                ]
            ],
            [
                'program_studi' => 'Filsafat',
                'rumpun_ilmu' => 'HUMANIORA',
                'subjects' => [
                    'merdeka' => ['SOS', 'SEJ', 'ANT'], // Sosiologi, Sejarah Indonesia, Sosiologi, Antropologi
                    '2013_ipa' => ['SOS', 'SEJ'],
                    '2013_ips' => ['SOS', 'ANT'],
                    '2013_bahasa' => ['SOS', 'ANT'],
                ]
            ],

            // ILMU SOSIAL
            [
                'program_studi' => 'Sosiologi',
                'rumpun_ilmu' => 'ILMU SOSIAL',
                'subjects' => [
                    'merdeka' => ['SOS', 'SEJ', 'ANT'], // Sosiologi, Sejarah Indonesia, Sosiologi, Antropologi
                    '2013_ipa' => ['SOS', 'SEJ'],
                    '2013_ips' => ['SOS', 'ANT'],
                    '2013_bahasa' => ['SOS', 'ANT'],
                ]
            ],
            [
                'program_studi' => 'Ekonomi',
                'rumpun_ilmu' => 'ILMU SOSIAL',
                'subjects' => [
                    'merdeka' => ['EKO', 'MTK_L'], // Ekonomi dan/atau matematika
                    '2013_ipa' => ['EKO', 'MTK_L'],
                    '2013_ips' => ['EKO', 'MTK_L'],
                    '2013_bahasa' => ['EKO', 'MTK_L'],
                ]
            ],
            [
                'program_studi' => 'Pertahanan',
                'rumpun_ilmu' => 'ILMU SOSIAL',
                'subjects' => [
                    'merdeka' => ['PPKN'], // Pendidikan Pancasila
                    '2013_ipa' => ['PPKN'],
                    '2013_ips' => ['PPKN'],
                    '2013_bahasa' => ['PPKN'],
                ]
            ],
            [
                'program_studi' => 'Psikologi',
                'rumpun_ilmu' => 'ILMU SOSIAL',
                'subjects' => [
                    'merdeka' => ['SOS', 'MTK_L'], // Sosiologi dan/atau Matematika
                    '2013_ipa' => ['SOS', 'MTK_L'],
                    '2013_ips' => ['SOS', 'MTK_L'],
                    '2013_bahasa' => ['SOS', 'MTK_L'],
                ]
            ],

            // ILMU ALAM
            [
                'program_studi' => 'Kimia',
                'rumpun_ilmu' => 'ILMU ALAM',
                'subjects' => [
                    'merdeka' => ['KIM'],
                    '2013_ipa' => ['KIM'],
                    '2013_ips' => ['KIM'],
                    '2013_bahasa' => ['KIM'],
                ]
            ],
            [
                'program_studi' => 'Ilmu Kebumian',
                'rumpun_ilmu' => 'ILMU ALAM',
                'subjects' => [
                    'merdeka' => ['FIS', 'MTK_L'], // Fisika dan/atau Matematika Tingkat Lanjut
                    '2013_ipa' => ['FIS', 'MTK_L'],
                    '2013_ips' => ['FIS', 'GEO'], // Fisika dan/atau Geografi
                    '2013_bahasa' => ['FIS', 'MTK_L'],
                ]
            ],
            [
                'program_studi' => 'Ilmu Kelautan',
                'rumpun_ilmu' => 'ILMU ALAM',
                'subjects' => [
                    'merdeka' => ['BIO'],
                    '2013_ipa' => ['BIO'],
                    '2013_ips' => ['BIO', 'GEO'], // Biologi dan/atau Geografi
                    '2013_bahasa' => ['BIO'],
                ]
            ],
            [
                'program_studi' => 'Biologi',
                'rumpun_ilmu' => 'ILMU ALAM',
                'subjects' => [
                    'merdeka' => ['BIO'],
                    '2013_ipa' => ['BIO'],
                    '2013_ips' => ['BIO'],
                    '2013_bahasa' => ['BIO'],
                ]
            ],
            [
                'program_studi' => 'Biofisika',
                'rumpun_ilmu' => 'ILMU ALAM',
                'subjects' => [
                    'merdeka' => ['FIS'],
                    '2013_ipa' => ['FIS'],
                    '2013_ips' => ['FIS'],
                    '2013_bahasa' => ['FIS'],
                ]
            ],
            [
                'program_studi' => 'Fisika',
                'rumpun_ilmu' => 'ILMU ALAM',
                'subjects' => [
                    'merdeka' => ['FIS'],
                    '2013_ipa' => ['FIS'],
                    '2013_ips' => ['FIS'],
                    '2013_bahasa' => ['FIS'],
                ]
            ],
            [
                'program_studi' => 'Astronomi',
                'rumpun_ilmu' => 'ILMU ALAM',
                'subjects' => [
                    'merdeka' => ['FIS', 'MTK_L'], // Fisika dan/atau Matematika Tingkat Lanjut
                    '2013_ipa' => ['FIS', 'MTK_L'],
                    '2013_ips' => ['FIS', 'MTK_L'],
                    '2013_bahasa' => ['FIS', 'MTK_L'],
                ]
            ],

            // ILMU FORMAL
            [
                'program_studi' => 'Komputer',
                'rumpun_ilmu' => 'ILMU FORMAL',
                'subjects' => [
                    'merdeka' => ['MTK_L'], // Matematika Tingkat Lanjut
                    '2013_ipa' => ['MTK_L'],
                    '2013_ips' => ['MTK_L'],
                    '2013_bahasa' => ['MTK_L'],
                ]
            ],
            [
                'program_studi' => 'Logika',
                'rumpun_ilmu' => 'ILMU FORMAL',
                'subjects' => [
                    'merdeka' => ['MTK_L'],
                    '2013_ipa' => ['MTK_L'],
                    '2013_ips' => ['MTK_L'],
                    '2013_bahasa' => ['MTK_L'],
                ]
            ],
            [
                'program_studi' => 'Matematika',
                'rumpun_ilmu' => 'ILMU FORMAL',
                'subjects' => [
                    'merdeka' => ['MTK_L'],
                    '2013_ipa' => ['MTK_L'],
                    '2013_ips' => ['MTK_L'],
                    '2013_bahasa' => ['MTK_L'],
                ]
            ],

            // ILMU TERAPAN
            [
                'program_studi' => 'Pertanian',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['BIO'],
                    '2013_ipa' => ['BIO'],
                    '2013_ips' => ['BIO'],
                    '2013_bahasa' => ['BIO'],
                ]
            ],
            [
                'program_studi' => 'Peternakan',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['BIO'],
                    '2013_ipa' => ['BIO'],
                    '2013_ips' => ['BIO'],
                    '2013_bahasa' => ['BIO'],
                ]
            ],
            [
                'program_studi' => 'Perikanan',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['BIO'],
                    '2013_ipa' => ['BIO'],
                    '2013_ips' => ['BIO'],
                    '2013_bahasa' => ['BIO'],
                ]
            ],
            [
                'program_studi' => 'Arsitektur',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['MTK_L', 'FIS'], // Matematika dan/atau Fisika
                    '2013_ipa' => ['MTK_L', 'FIS'],
                    '2013_ips' => ['MTK_L', 'FIS'],
                    '2013_bahasa' => ['MTK_L', 'FIS'],
                ]
            ],
            [
                'program_studi' => 'Perencanaan Wilayah',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['EKO', 'MTK_L'], // Ekonomi dan/atau Matematika
                    '2013_ipa' => ['EKO', 'MTK_L'],
                    '2013_ips' => ['EKO', 'MTK_L'],
                    '2013_bahasa' => ['EKO', 'MTK_L'],
                ]
            ],
            [
                'program_studi' => 'Desain',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['SENI', 'MTK_L'], // Seni Budaya, Matematika dan/atau Seni Budaya
                    '2013_ipa' => ['SENI', 'MTK_L'],
                    '2013_ips' => ['SENI', 'MTK_L'],
                    '2013_bahasa' => ['SENI', 'MTK_L'],
                ]
            ],
            [
                'program_studi' => 'Akuntansi',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['EKO', 'MTK_L'], // Ekonomi, Matematika dan/atau Ekonomi
                    '2013_ipa' => ['EKO', 'MTK_L'],
                    '2013_ips' => ['EKO', 'MTK_L'],
                    '2013_bahasa' => ['EKO', 'MTK_L'],
                ]
            ],
            [
                'program_studi' => 'Manajemen',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['EKO', 'MTK_L'],
                    '2013_ipa' => ['EKO', 'MTK_L'],
                    '2013_ips' => ['EKO', 'MTK_L'],
                    '2013_bahasa' => ['EKO', 'MTK_L'],
                ]
            ],
            [
                'program_studi' => 'Teknik',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['FIS', 'KIM', 'MTK_L'], // Fisika/Kimia dan/atau Matematika Tingkat Lanjut
                    '2013_ipa' => ['FIS', 'KIM', 'MTK_L'],
                    '2013_ips' => ['FIS', 'KIM', 'MTK_L'],
                    '2013_bahasa' => ['FIS', 'KIM', 'MTK_L'],
                ]
            ],
            [
                'program_studi' => 'Kedokteran',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['BIO', 'KIM'], // Biologi dan/atau Kimia
                    '2013_ipa' => ['BIO', 'KIM'],
                    '2013_ips' => ['BIO', 'KIM'],
                    '2013_bahasa' => ['BIO', 'KIM'],
                ]
            ],
            [
                'program_studi' => 'Kedokteran Gigi',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['BIO', 'KIM'],
                    '2013_ipa' => ['BIO', 'KIM'],
                    '2013_ips' => ['BIO', 'KIM'],
                    '2013_bahasa' => ['BIO', 'KIM'],
                ]
            ],
            [
                'program_studi' => 'Veteriner',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['BIO', 'KIM'],
                    '2013_ipa' => ['BIO', 'KIM'],
                    '2013_ips' => ['BIO', 'KIM'],
                    '2013_bahasa' => ['BIO', 'KIM'],
                ]
            ],
            [
                'program_studi' => 'Farmasi',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['BIO', 'KIM'],
                    '2013_ipa' => ['BIO', 'KIM'],
                    '2013_ips' => ['BIO', 'KIM'],
                    '2013_bahasa' => ['BIO', 'KIM'],
                ]
            ],
            [
                'program_studi' => 'Gizi',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['BIO', 'KIM'],
                    '2013_ipa' => ['BIO', 'KIM'],
                    '2013_ips' => ['BIO', 'KIM'],
                    '2013_bahasa' => ['BIO', 'KIM'],
                ]
            ],
            [
                'program_studi' => 'Kesehatan Masyarakat',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['BIO'],
                    '2013_ipa' => ['BIO'],
                    '2013_ips' => ['SOS', 'BIO'], // Sosiologi dan/atau Biologi
                    '2013_bahasa' => ['BIO'],
                ]
            ],
            [
                'program_studi' => 'Kebidanan',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['BIO'],
                    '2013_ipa' => ['BIO'],
                    '2013_ips' => ['BIO'],
                    '2013_bahasa' => ['BIO'],
                ]
            ],
            [
                'program_studi' => 'Keperawatan',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['BIO'],
                    '2013_ipa' => ['BIO'],
                    '2013_ips' => ['BIO'],
                    '2013_bahasa' => ['BIO'],
                ]
            ],
            [
                'program_studi' => 'Teknologi Informasi',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['MTK_L'], // Matematika Tingkat Lanjut
                    '2013_ipa' => ['MTK_L'],
                    '2013_ips' => ['MTK_L'],
                    '2013_bahasa' => ['MTK_L'],
                ]
            ],
            [
                'program_studi' => 'Hukum',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['SOS', 'PPKN'], // Sosiologi dan/atau Pendidikan Pancasila
                    '2013_ipa' => ['SOS', 'PPKN'],
                    '2013_ips' => ['SOS', 'PPKN'],
                    '2013_bahasa' => ['SOS', 'PPKN'],
                ]
            ],
            [
                'program_studi' => 'Militer',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['SOS', 'PPKN'], // Sosiologi, PPKn dan/atau Sosiologi
                    '2013_ipa' => ['SOS', 'PPKN'],
                    '2013_ips' => ['SOS', 'PPKN', 'ANT'], // Sosiologi, PPKn dan/atau Antropologi
                    '2013_bahasa' => ['SOS', 'PPKN', 'ANT'],
                ]
            ],
            [
                'program_studi' => 'Urusan Publik',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['SOS', 'PPKN'],
                    '2013_ipa' => ['SOS', 'PPKN'],
                    '2013_ips' => ['SOS', 'PPKN', 'ANT'],
                    '2013_bahasa' => ['SOS', 'PPKN', 'ANT'],
                ]
            ],
            [
                'program_studi' => 'Keolahragaan',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['PJOK', 'BIO'], // Pendidikan Jasmani, Olahraga, dan Kesehatan (PJOK) dan/atau Biologi
                    '2013_ipa' => ['PJOK', 'BIO'],
                    '2013_ips' => ['PJOK'],
                    '2013_bahasa' => ['PJOK'],
                ]
            ],
            [
                'program_studi' => 'Pariwisata',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['EKO', 'BIG_L', 'BAR', 'BJE', 'BPR', 'BJP', 'BKO', 'BMA'], // Ekonomi dan/atau Bahasa Inggris Tingkat Lanjut/Bahasa Asing lainnya
                    '2013_ipa' => ['EKO', 'BIG_L'],
                    '2013_ips' => ['EKO', 'BIG_L'],
                    '2013_bahasa' => ['BIG_L', 'BAR', 'BJE', 'BPR', 'BJP', 'BKO', 'BMA'], // Bahasa Sastra dan/atau Bahasa lainnya dan Asing Inggris
                ]
            ],
            [
                'program_studi' => 'Transportasi',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['MTK_L'],
                    '2013_ipa' => ['MTK_L'],
                    '2013_ips' => ['MTK_L'],
                    '2013_bahasa' => ['MTK_L'],
                ]
            ],
            [
                'program_studi' => 'Bioteknologi',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['BIO', 'MTK_L'], // Biologi dan/atau Matematika
                    '2013_ipa' => ['BIO', 'MTK_L'],
                    '2013_ips' => ['BIO', 'MTK_L'],
                    '2013_bahasa' => ['BIO', 'MTK_L'],
                ]
            ],
            [
                'program_studi' => 'Geografi',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['GEO'], // Geografi
                    '2013_ipa' => ['GEO', 'MTK_L'], // Geografi dan/atau Matematika
                    '2013_ips' => ['FIS', 'MTK_L'], // Fisika dan/atau Matematika
                    '2013_bahasa' => ['GEO', 'MTK_L'], // Geografi dan/atau Matematika
                ]
            ],
            [
                'program_studi' => 'Informatika Medis',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['BIO', 'MTK_L'], // Biologi dan/atau Matematika Tingkat Lanjut
                    '2013_ipa' => ['BIO', 'MTK_L'],
                    '2013_ips' => ['BIO', 'MTK_L'],
                    '2013_bahasa' => ['BIO', 'MTK_L'],
                ]
            ],
            [
                'program_studi' => 'Konservasi',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['BIO'],
                    '2013_ipa' => ['BIO'],
                    '2013_ips' => ['BIO'],
                    '2013_bahasa' => ['BIO'],
                ]
            ],
            [
                'program_studi' => 'Teknologi Pangan',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['KIM', 'BIO'], // Kimia dan/atau Biologi
                    '2013_ipa' => ['KIM', 'BIO'],
                    '2013_ips' => ['KIM', 'BIO'],
                    '2013_bahasa' => ['KIM', 'BIO'],
                ]
            ],
            [
                'program_studi' => 'Sains Data',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['MTK_L'],
                    '2013_ipa' => ['MTK_L'],
                    '2013_ips' => ['MTK_L'],
                    '2013_bahasa' => ['MTK_L'],
                ]
            ],
            [
                'program_studi' => 'Sains Perkopian',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['BIO'],
                    '2013_ipa' => ['BIO'],
                    '2013_ips' => ['BIO'],
                    '2013_bahasa' => ['BIO'],
                ]
            ],
            [
                'program_studi' => 'Studi Humanitas',
                'rumpun_ilmu' => 'ILMU TERAPAN',
                'subjects' => [
                    'merdeka' => ['ANT', 'SOS', 'PPKN'], // Antropologi dan/atau Sosiologi, PPKn dan/atau Sosiologi
                    '2013_ipa' => ['ANT', 'SOS', 'PPKN'],
                    '2013_ips' => ['ANT', 'SOS'],
                    '2013_bahasa' => ['ANT', 'SOS'],
                ]
            ],
        ];

        // Insert mappings
        $insertedCount = 0;
        foreach ($mappings as $mapping) {
            $programStudiId = $programStudi[$mapping['program_studi']] ?? null;
            if (!$programStudiId) {
                echo "⚠️ Program studi '{$mapping['program_studi']}' not found\n";
                continue;
            }

            foreach ($mapping['subjects'] as $kurikulumType => $subjectCodes) {
                foreach ($subjectCodes as $subjectCode) {
                    $subjectId = $subjects[$subjectCode] ?? null;
                    if (!$subjectId) {
                        echo "⚠️ Subject code '{$subjectCode}' not found\n";
                        continue;
                    }

                    DB::table('program_studi_subjects')->insert([
                        'program_studi_id' => $programStudiId,
                        'subject_id' => $subjectId,
                        'kurikulum_type' => $kurikulumType,
                        'is_required' => in_array($subjectCode, ['MTK_L', 'BIN_L', 'BIG_L']), // Wajib untuk mata pelajaran wajib
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $insertedCount++;
                }
            }
        }

        echo "✅ Inserted {$insertedCount} subject mappings\n";

        // Show summary by rumpun_ilmu
        echo "\n📊 Summary by rumpun_ilmu:\n";
        $summary = DB::table('program_studi_subjects')
            ->join('program_studi', 'program_studi_subjects.program_studi_id', '=', 'program_studi.id')
            ->join('rumpun_ilmu', 'program_studi.rumpun_ilmu_id', '=', 'rumpun_ilmu.id')
            ->select('rumpun_ilmu.name', DB::raw('COUNT(DISTINCT program_studi_subjects.program_studi_id) as program_count'))
            ->groupBy('rumpun_ilmu.name')
            ->get();

        foreach ($summary as $item) {
            echo "  - {$item->name}: {$item->program_count} program studi\n";
        }

        echo "\n🎉 Subjects Mapping Update Completed Successfully!\n";
        echo "✅ All mappings now follow Pusmendik standard exactly\n";
    }
}
