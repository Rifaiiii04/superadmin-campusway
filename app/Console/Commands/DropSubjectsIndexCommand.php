<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DropSubjectsIndexCommand extends Command
{
    protected $signature = 'subjects:drop-index';
    protected $description = 'Drop subjects unique index';

    public function handle()
    {
        $this->info('📚 Menghapus unique index subjects...');

        try {
            DB::statement('DROP INDEX subjects_name_unique ON subjects');
            $this->info('✅ Index subjects_name_unique dihapus');
        } catch (\Exception $e) {
            $this->warn('⚠️ Index tidak ditemukan atau sudah dihapus: ' . $e->getMessage());
        }

        $this->info('🎉 Selesai!');
    }
}
