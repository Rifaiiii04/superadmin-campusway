<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class ClearAllCacheCommand extends Command
{
    protected $signature = 'cache:clear-all';
    protected $description = 'Clear all caches and optimize application';

    public function handle()
    {
        $this->info('🧹 Clearing all caches...');

        // Clear application cache
        Artisan::call('cache:clear');
        $this->info('✅ Application cache cleared');

        // Clear config cache
        Artisan::call('config:clear');
        $this->info('✅ Config cache cleared');

        // Clear route cache
        Artisan::call('route:clear');
        $this->info('✅ Route cache cleared');

        // Clear view cache
        Artisan::call('view:clear');
        $this->info('✅ View cache cleared');

        // Clear custom caches
        Cache::flush();
        $this->info('✅ Custom caches cleared');

        // Optimize for production
        if (app()->environment('production')) {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            $this->info('✅ Production optimizations applied');
        }

        $this->info('🎉 All caches cleared successfully!');
    }
}
