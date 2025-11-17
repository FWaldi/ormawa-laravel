<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StorageService;
use Illuminate\Support\Facades\Log;

class StorageCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:cleanup {--disk= : The storage disk to clean} {--days=30 : Files older than this many days will be considered for cleanup} {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up orphaned files and monitor disk usage';

    protected $storageService;

    /**
     * Create a new command instance.
     *
     * @param StorageService $storageService
     */
    public function __construct(StorageService $storageService)
    {
        parent::__construct();
        $this->storageService = $storageService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $disk = $this->option('disk') ?? 'organizations';
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $this->info("Starting storage cleanup for disk: {$disk}");
        $this->info("Files older than {$days} days will be considered for cleanup");
        
        if ($dryRun) {
            $this->warn("DRY RUN MODE - No files will be deleted");
        }

        // Get disk usage before cleanup
        $usageBefore = $this->storageService->getDiskUsage($disk);
        $this->displayDiskUsage($usageBefore, 'Before Cleanup');

        if ($dryRun) {
            $this->info("\nChecking for orphaned files (dry run)...");
            $this->listOrphanedFiles($disk, $days);
        } else {
            // Perform actual cleanup
            $this->info("\nCleaning up orphaned files...");
            $cleanedCount = $this->storageService->cleanupOrphanedFiles($disk, $days);
            $this->info("Cleaned up {$cleanedCount} orphaned files");
        }

        // Get disk usage after cleanup
        $usageAfter = $this->storageService->getDiskUsage($disk);
        $this->displayDiskUsage($usageAfter, 'After Cleanup');

        // Show difference
        $sizeDiff = $usageBefore['total_size'] - $usageAfter['total_size'];
        $fileDiff = $usageBefore['file_count'] - $usageAfter['file_count'];
        
        if ($sizeDiff > 0) {
            $this->info("\nStorage freed: " . $this->formatBytes($sizeDiff) . " ({$fileDiff} files)");
        } else {
            $this->info("\nNo storage freed during cleanup");
        }

        // Check for disks that need attention
        $this->checkDiskHealth();

        $this->info("\nStorage cleanup completed successfully!");
        return 0;
    }

    /**
     * Display disk usage information
     *
     * @param array $usage
     * @param string $title
     * @return void
     */
    private function displayDiskUsage(array $usage, string $title): void
    {
        $this->info("\n{$title}:");
        $this->line("  Total Size: {$usage['total_size_human']}");
        $this->line("  File Count: {$usage['file_count']}");
        
        if (!empty($usage['files_by_type'])) {
            $this->line("  Files by Type:");
            foreach ($usage['files_by_type'] as $type => $count) {
                $this->line("    .{$type}: {$count} files");
            }
        }
    }

    /**
     * List orphaned files without deleting them
     *
     * @param string $disk
     * @param int $daysOld
     * @return void
     */
    private function listOrphanedFiles(string $disk, int $daysOld): void
    {
        // This would need to be implemented in StorageService
        // For now, we'll just show a message
        $this->info("Scanning for orphaned files older than {$daysOld} days...");
        $this->info("(Actual orphaned file listing would be implemented here)");
    }

    /**
     * Check disk health and warn if needed
     *
     * @return void
     */
    private function checkDiskHealth(): void
    {
        $disks = ['organizations', 'activities', 'news', 'announcements'];
        $warningThreshold = 100 * 1024 * 1024; // 100MB

        foreach ($disks as $disk) {
            $usage = $this->storageService->getDiskUsage($disk);
            
            if ($usage['total_size'] > $warningThreshold) {
                $this->warn("\n⚠️  Warning: Disk '{$disk}' is using {$usage['total_size_human']}");
                $this->warn("   Consider running cleanup or increasing storage limits");
            }
        }
    }

    /**
     * Format bytes to human readable format
     *
     * @param int $bytes
     * @return string
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}