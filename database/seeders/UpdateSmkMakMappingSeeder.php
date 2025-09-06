<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateSmkMakMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🏭 Updating SMK/MAK program studi mapping...\n";

        // Check if tables exist
        if (!Schema::hasTable('program_studi_subjects')) {
            echo "❌ Table program_studi_subjects not found!\n";
            return;
        }

        if (!Schema::hasTable('subjects')) {
            echo "❌ Table subjects not found!\n";
            return;
        }

        // Get all program studi
        $programStudi = DB::table('program_studi')->get();

        if ($programStudi->isEmpty()) {
            echo "❌ No program studi found!\n";
            return;
        }

        echo "📚 Found " . $programStudi->count() . " program studi\n";

        // Get SMK/MAK subjects
        $smkSubjects = DB::table('subjects')
            ->where('education_level', 'SMK')
            ->get();

        if ($smkSubjects->isEmpty()) {
            echo "❌ No SMK/MAK subjects found!\n";
            return;
        }

        echo "🏭 Found " . $smkSubjects->count() . " SMK/MAK subjects\n";

        // Clear existing SMK mappings
        $smkSubjectIds = $smkSubjects->pluck('id')->toArray();
        DB::table('program_studi_subjects')
            ->whereIn('subject_id', $smkSubjectIds)
            ->delete();

        echo "🧹 Cleared existing SMK/MAK mappings\n";

        $totalMappings = 0;

        foreach ($programStudi as $program) {
            echo "📖 Processing: {$program->name}\n";

            // Add SMK/MAK curriculum mappings using existing valid values
            $curriculums = ['merdeka', '2013_ipa', '2013_ips', '2013_bahasa'];

            foreach ($curriculums as $curriculum) {
                // Add pilihan pertama - Produk/Projek Kreatif dan Kewirausahaan
                $ppkkSubject = $smkSubjects->where('name', 'Produk/Projek Kreatif dan Kewirausahaan')->first();
                if ($ppkkSubject) {
                    DB::table('program_studi_subjects')->insert([
                        'program_studi_id' => $program->id,
                        'subject_id' => $ppkkSubject->id,
                        'kurikulum_type' => $curriculum,
                        'is_required' => false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    echo "  ✅ {$curriculum}: {$ppkkSubject->name} (Pilihan Pertama)\n";
                    $totalMappings++;
                }

                // Add pilihan kedua - random selection of 2-3 subjects from 1-18
                $pilihanKeduaSubjects = $smkSubjects
                    ->where('subject_type', 'Pilihan Kedua')
                    ->where('subject_number', '>=', 1)
                    ->where('subject_number', '<=', 18)
                    ->random(2); // Random 2 subjects

                foreach ($pilihanKeduaSubjects as $subject) {
                    DB::table('program_studi_subjects')->insert([
                        'program_studi_id' => $program->id,
                        'subject_id' => $subject->id,
                        'kurikulum_type' => $curriculum,
                        'is_required' => false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    echo "  ✅ {$curriculum}: {$subject->name} (Pilihan Kedua)\n";
                    $totalMappings++;
                }
            }
        }

        echo "\n🎉 SMK/MAK mapping update completed!\n";
        echo "📊 Total mappings created: {$totalMappings}\n";
        echo "📋 Mappings follow Pusmendik reference for SMK/MAK\n";
        echo "\n📚 SMK/MAK Structure:\n";
        echo "  🥇 Pilihan Pertama: Produk/Projek Kreatif dan Kewirausahaan (wajib)\n";
        echo "  🥈 Pilihan Kedua: 2 mata pelajaran random dari 1-18\n";
    }
}
