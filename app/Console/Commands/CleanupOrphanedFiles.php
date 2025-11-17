<?php

namespace App\Console\Commands;

use App\Services\StorageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupOrphanedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:cleanup-orphaned {--days=30 : Number of days old for files to be considered orphaned} {--disk= : Specific disk to clean (default: all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up orphaned files from storage';

    /**
     * Execute the console command.
     */
    public function handle(StorageService $storageService): int
    {
        $days = $this->option('days');
        $disk = $this->option('disk');

        $this->info('Starting orphaned files cleanup...');

        $disks = $disk ? [$disk] : ['organizations', 'activities', 'news', 'announcements'];
        $totalCleaned = 0;

        foreach ($disks as $diskName) {
            $this->line("Cleaning disk: {$diskName}");
            
            try {
                $cleanedCount = $storageService->cleanupOrphanedFiles($diskName, $days);
                $totalCleaned += $cleanedCount;
                
                $this->line("  Cleaned {$cleanedCount} orphaned files from {$diskName}");
                
            } catch (\Exception $e) {
                $this->error("  Error cleaning {$diskName}: {$e->getMessage()}");
                Log::error("Error cleaning orphaned files from disk {$diskName}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->info("Cleanup completed. Total files cleaned: {$totalCleaned}");
        
        return Command::SUCCESS;
    }
}