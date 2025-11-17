<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Announcement;
use App\Models\User;
use App\Services\StorageService;
use Illuminate\Support\Facades\Route;

class DebugAnnouncements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:announcements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug the announcements page to find 500 error cause';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Announcements Page Debug');
        $this->newLine();

        // Test 1: Check if Announcement model exists and can be instantiated
        $this->info('Test 1: Announcement Model');
        try {
            $announcement = new Announcement();
            $this->info('✅ Announcement model can be instantiated');
        } catch (\Exception $e) {
            $this->error('❌ Announcement model error: ' . $e->getMessage());
            return 1;
        }

        // Test 2: Check database connection
        $this->info('Test 2: Database Connection');
        try {
            $pdo = new \PDO('sqlite:database/database.sqlite');
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->info('✅ Database connection successful');

            // Check if announcements table exists
            $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='announcements'");
            $table = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($table) {
                $this->info('✅ Announcements table exists');

                // Check table structure
                $stmt = $pdo->query("PRAGMA table_info(announcements)");
                $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $columnNames = array_column($columns, 'name');
                $this->info('📋 Table columns: ' . implode(', ', $columnNames));
            } else {
                $this->error('❌ Announcements table does not exist');
            }

        } catch (\Exception $e) {
            $this->error('❌ Database error: ' . $e->getMessage());
        }

        // Test 3: Check if StorageService can be resolved
        $this->info('Test 3: StorageService');
        try {
            $storageService = app(StorageService::class);
            $this->info('✅ StorageService can be resolved');
        } catch (\Exception $e) {
            $this->error('❌ StorageService error: ' . $e->getMessage());
        }

        // Test 4: Check if User model exists
        $this->info('Test 4: User Model');
        try {
            $user = new User();
            $this->info('✅ User model can be instantiated');
        } catch (\Exception $e) {
            $this->error('❌ User model error: ' . $e->getMessage());
        }

        // Test 5: Try to query announcements
        $this->info('Test 5: Query Announcements');
        try {
            $announcements = Announcement::with('creator')
                ->orderBy('is_pinned', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $this->info('✅ Query executed successfully');
            $this->info('📊 Found ' . $announcements->count() . ' announcements');

        } catch (\Exception $e) {
            $this->error('❌ Query error: ' . $e->getMessage());
            $this->error('❌ Error file: ' . $e->getFile() . ' (line ' . $e->getLine() . ')');
        }

        // Test 6: Check view files
        $this->info('Test 6: View Files');
        $viewFiles = [
            'resources/views/layouts/app.blade.php',
            'resources/views/announcements/index.blade.php'
        ];

        foreach ($viewFiles as $viewFile) {
            if (file_exists(base_path($viewFile))) {
                $this->info("✅ $viewFile exists");
            } else {
                $this->error("❌ $viewFile missing");
            }
        }

        // Test 7: Check route registration
        $this->info('Test 7: Route Registration');
        try {
            $routes = app('router')->getRoutes();
            $announcementsRoute = null;

            foreach ($routes as $route) {
                if ($route->getName() === 'announcements.index') {
                    $announcementsRoute = $route;
                    break;
                }
            }

            if ($announcementsRoute) {
                $this->info('✅ announcements.index route is registered');
                $this->info('📋 Route URI: ' . $announcementsRoute->uri());
                $this->info('📋 Route methods: ' . implode(', ', $announcementsRoute->methods()));
            } else {
                $this->error('❌ announcements.index route not found');
            }

        } catch (\Exception $e) {
            $this->error('❌ Route check error: ' . $e->getMessage());
        }

        // Test 8: Try to render the view
        $this->info('Test 8: View Rendering');
        try {
            $announcements = collect(); // Empty collection for testing
            $view = view('announcements.index', compact('announcements'));
            $this->info('✅ View renders successfully');
        } catch (\Exception $e) {
            $this->error('❌ View rendering error: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('Debug completed at: ' . now()->format('Y-m-d H:i:s'));

        return 0;
    }
}