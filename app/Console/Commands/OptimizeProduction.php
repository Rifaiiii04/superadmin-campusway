<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use App\Services\QueryOptimizationService;
use App\Services\ApiResponseService;

class OptimizeProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:production {--force : Force optimization even in development}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize Laravel application for production';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (config('app.env') !== 'production' && !$this->option('force')) {
            $this->error('This command should only be run in production environment!');
            $this->info('Use --force flag to run in development.');
            return 1;
        }

        $this->info('🚀 Starting production optimization...');

        // Clear all caches
        $this->clearCaches();

        // Optimize autoloader
        $this->optimizeAutoloader();

        // Cache configuration
        $this->cacheConfiguration();

        // Cache routes
        $this->cacheRoutes();

        // Cache views
        $this->cacheViews();

        // Warm up cache
        $this->warmUpCache();

        // Optimize database
        $this->optimizeDatabase();

        $this->info('✅ Production optimization completed successfully!');
        return 0;
    }

    /**
     * Clear all caches
     */
    private function clearCaches(): void
    {
        $this->info('🧹 Clearing caches...');
        
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        $this->line('   ✓ All caches cleared');
    }

    /**
     * Optimize autoloader
     */
    private function optimizeAutoloader(): void
    {
        $this->info('⚡ Optimizing autoloader...');
        
        Artisan::call('optimize:autoloader');
        
        $this->line('   ✓ Autoloader optimized');
    }

    /**
     * Cache configuration
     */
    private function cacheConfiguration(): void
    {
        $this->info('📋 Caching configuration...');
        
        Artisan::call('config:cache');
        
        $this->line('   ✓ Configuration cached');
    }

    /**
     * Cache routes
     */
    private function cacheRoutes(): void
    {
        $this->info('🛣️  Caching routes...');
        
        Artisan::call('route:cache');
        
        $this->line('   ✓ Routes cached');
    }

    /**
     * Cache views
     */
    private function cacheViews(): void
    {
        $this->info('👁️  Caching views...');
        
        Artisan::call('view:cache');
        
        $this->line('   ✓ Views cached');
    }

    /**
     * Warm up cache
     */
    private function warmUpCache(): void
    {
        $this->info('🔥 Warming up cache...');
        
        QueryOptimizationService::warmUpCache();
        
        $this->line('   ✓ Cache warmed up');
    }

    /**
     * Optimize database
     */
    private function optimizeDatabase(): void
    {
        $this->info('🗄️  Optimizing database...');
        
        // Run database optimizations
        Artisan::call('migrate', ['--force' => true]);
        
        $this->line('   ✓ Database optimized');
    }
}
