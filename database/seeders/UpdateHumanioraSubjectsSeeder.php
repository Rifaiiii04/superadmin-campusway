<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateHumanioraSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🎨 Updating HUMANIORA subjects according to Pusmendik reference...\n";

        // Check if tables exist
        if (!Schema::hasTable('program_studi_subjects')) {
            echo "❌ Table program_studi_subjects not found!\n";
            return;
        }

        if (!Schema::hasTable('subjects')) {
            echo "❌ Table subjects not found!\n";
            return;
        }

        // Get HUMANIORA rumpun_ilmu ID
        $humanioraRumpun = DB::table('rumpun_ilmu')
            ->where('name', 'HUMANIORA')
            ->first();

        if (!$humanioraRumpun) {
            echo "❌ HUMANIORA rumpun_ilmu not found!\n";
            return;
        }

        // Get HUMANIORA program studi IDs
        $humanioraPrograms = DB::table('program_studi')
            ->where('rumpun_ilmu_id', $humanioraRumpun->id)
            ->get();

        if ($humanioraPrograms->isEmpty()) {
            echo "❌ No HUMANIORA program studi found!\n";
            return;
        }

        echo "📚 Found " . $humanioraPrograms->count() . " HUMANIORA program studi\n";

        // Clear existing mappings for HUMANIORA programs
        foreach ($humanioraPrograms as $program) {
            DB::table('program_studi_subjects')
                ->where('program_studi_id', $program->id)
                ->delete();
        }

        echo "🧹 Cleared existing HUMANIORA mappings\n";

        // Get subject IDs
        $subjects = DB::table('subjects')->pluck('id', 'name');

        // Define mappings according to Pusmendik reference
        $mappings = [
            'Seni' => [
                'merdeka' => ['Seni Budaya'],
                '2013_ipa' => ['Seni Budaya'],
                '2013_ips' => ['Seni Budaya'],
                '2013_bahasa' => ['Seni Budaya']
            ],
            'Sejarah' => [
                'merdeka' => ['Sejarah'],
                '2013_ipa' => ['Sejarah Indonesia'],
                '2013_ips' => ['Sejarah Indonesia'],
                '2013_bahasa' => ['Sejarah Indonesia']
            ],
            'Linguistik' => [
                'merdeka' => ['Bahasa Indonesia Lanjutan', 'Bahasa Inggris Lanjutan'],
                '2013_ipa' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                '2013_ips' => ['Bahasa Indonesia', 'Bahasa Inggris'],
                '2013_bahasa' => ['Bahasa Indonesia', 'Bahasa Inggris']
            ],
            'Sastra' => [
                'merdeka' => ['Bahasa Indonesia Lanjutan', 'Bahasa Arab', 'Bahasa Jerman', 'Bahasa Prancis', 'Bahasa Jepang', 'Bahasa Korea', 'Bahasa Mandarin'],
                '2013_ipa' => ['Bahasa Indonesia', 'Bahasa Arab', 'Bahasa Jerman', 'Bahasa Prancis', 'Bahasa Jepang', 'Bahasa Korea', 'Bahasa Mandarin'],
                '2013_ips' => ['Bahasa Indonesia', 'Bahasa Arab', 'Bahasa Jerman', 'Bahasa Prancis', 'Bahasa Jepang', 'Bahasa Korea', 'Bahasa Mandarin'],
                '2013_bahasa' => ['Bahasa Indonesia', 'Bahasa Arab', 'Bahasa Jerman', 'Bahasa Prancis', 'Bahasa Jepang', 'Bahasa Korea', 'Bahasa Mandarin']
            ],
            'Filsafat' => [
                'merdeka' => ['Sosiologi'],
                '2013_ipa' => ['Sejarah Indonesia'],
                '2013_ips' => ['Sosiologi'],
                '2013_bahasa' => ['Antropologi']
            ]
        ];

        $totalMappings = 0;

        foreach ($humanioraPrograms as $program) {
            $programName = $program->name;
            echo "📖 Processing: {$programName}\n";

            if (!isset($mappings[$programName])) {
                echo "⚠️ No mapping defined for: {$programName}\n";
                continue;
            }

            $programMappings = $mappings[$programName];

            foreach ($programMappings as $curriculum => $subjectNames) {
                foreach ($subjectNames as $subjectName) {
                    // Find subject ID
                    $subjectId = null;
                    foreach ($subjects as $name => $id) {
                        if (stripos($name, $subjectName) !== false || stripos($subjectName, $name) !== false) {
                            $subjectId = $id;
                            break;
                        }
                    }

                    if ($subjectId) {
                        // Insert mapping
                        DB::table('program_studi_subjects')->insert([
                            'program_studi_id' => $program->id,
                            'subject_id' => $subjectId,
                            'kurikulum_type' => $curriculum,
                            'is_required' => true,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        echo "  ✅ {$curriculum}: {$subjectName} (ID: {$subjectId})\n";
                        $totalMappings++;
                    } else {
                        echo "  ❌ Subject not found: {$subjectName}\n";
                    }
                }
            }
        }

        echo "\n🎉 HUMANIORA subjects update completed!\n";
        echo "📊 Total mappings created: {$totalMappings}\n";
        echo "📋 Mappings follow Pusmendik reference for SMA/MA/SMK/MAK\n";
    }
}
