<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateRumpunIlmuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Updating rumpun_ilmu values in major_recommendations...\n";
        
        // Update rumpun_ilmu based on category
        $mapping = [
            'Saintek' => 'ILMU ALAM',
            'Soshum' => 'ILMU SOSIAL',
            'Humaniora' => 'HUMANIORA',
            'Ilmu Terapan' => 'ILMU TERAPAN',
            'Ilmu Formal' => 'ILMU FORMAL',
            'Ilmu Alam' => 'ILMU ALAM',
            'Ilmu Sosial' => 'ILMU SOSIAL',
        ];
        
        $updated = 0;
        foreach ($mapping as $category => $rumpunIlmu) {
            $count = DB::table('major_recommendations')
                ->where('category', $category)
                ->whereNull('rumpun_ilmu')
                ->update(['rumpun_ilmu' => $rumpunIlmu]);
            
            if ($count > 0) {
                echo "Updated {$count} records with category '{$category}' to rumpun_ilmu '{$rumpunIlmu}'\n";
                $updated += $count;
            }
        }
        
        // Handle special cases for specific majors
        $specialCases = [
            'Administrasi Bisnis' => 'ILMU TERAPAN',
            'Arsitektur' => 'ILMU TERAPAN',
            'Teknik Informatika' => 'ILMU TERAPAN',
            'Teknik Mesin' => 'ILMU TERAPAN',
            'Teknik Elektro' => 'ILMU TERAPAN',
            'Akuntansi' => 'ILMU SOSIAL',
            'Manajemen' => 'ILMU SOSIAL',
            'Ekonomi' => 'ILMU SOSIAL',
            'Psikologi' => 'ILMU SOSIAL',
            'Hukum' => 'ILMU SOSIAL',
            'Pendidikan' => 'ILMU SOSIAL',
            'Komunikasi' => 'ILMU SOSIAL',
            'Seni' => 'HUMANIORA',
            'Sejarah' => 'HUMANIORA',
            'Linguistik' => 'HUMANIORA',
            'Sastra' => 'HUMANIORA',
            'Matematika' => 'ILMU FORMAL',
            'Komputer' => 'ILMU FORMAL',
            'Logika' => 'ILMU FORMAL',
            'Fisika' => 'ILMU ALAM',
            'Kimia' => 'ILMU ALAM',
            'Biologi' => 'ILMU ALAM',
            'Astronomi' => 'ILMU ALAM',
        ];
        
        foreach ($specialCases as $majorName => $rumpunIlmu) {
            $count = DB::table('major_recommendations')
                ->where('major_name', 'LIKE', "%{$majorName}%")
                ->update(['rumpun_ilmu' => $rumpunIlmu]);
            
            if ($count > 0) {
                echo "Updated {$count} records for major '{$majorName}' to rumpun_ilmu '{$rumpunIlmu}'\n";
                $updated += $count;
            }
        }
        
        echo "Total records updated: {$updated}\n";
    }
}
