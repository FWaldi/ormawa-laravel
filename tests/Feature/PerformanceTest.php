<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_query_performance_with_large_dataset()
    {
        // Create test data
        $organizations = Organization::factory()->count(50)->create();
        $users = User::factory()->count(200)->create();
        $activities = Activity::factory()->count(500)->create();
        $announcements = Announcement::factory()->count(300)->create();
        $news = News::factory()->count(400)->create();

        // Test organizations index query performance
        $startTime = microtime(true);
        $organizations = Organization::with('members')->paginate(10);
        $organizationsTime = microtime(true) - $startTime;

        // Should complete within reasonable time (less than 1 second)
        $this->assertLessThan(1.0, $organizationsTime, 'Organizations index query too slow');

        // Test activities index query performance
        $startTime = microtime(true);
        $activities = Activity::with(['organization', 'creator'])
            ->orderBy('start_date', 'desc')
            ->paginate(12);
        $activitiesTime = microtime(true) - $startTime;

        $this->assertLessThan(1.0, $activitiesTime, 'Activities index query too slow');

        // Test announcements query performance
        $startTime = microtime(true);
        $announcements = Announcement::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $announcementsTime = microtime(true) - $startTime;

        $this->assertLessThan(1.0, $announcementsTime, 'Announcements query too slow');

        // Test news query performance
        $startTime = microtime(true);
        $news = News::with(['organization', 'creator'])
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->paginate(10);
        $newsTime = microtime(true) - $startTime;

        $this->assertLessThan(1.0, $newsTime, 'News query too slow');
    }

    public function test_n_plus_1_query_prevention()
    {
        // Create test data
        $organizations = Organization::factory()->count(10)->create();
        foreach ($organizations as $organization) {
            Activity::factory()->count(5)->create(['organization_id' => $organization->id]);
            News::factory()->count(3)->create(['organization_id' => $organization->id]);
        }

        // Test without eager loading (should trigger N+1 queries)
        DB::enableQueryLog();
        $startTime = microtime(true);
        
        $organizations = Organization::all();
        foreach ($organizations as $organization) {
            $organization->activities; // This would trigger N+1 queries
            $organization->news;     // This would trigger N+1 queries
        }
        
        $timeWithoutEagerLoading = microtime(true) - $startTime;
        $queryCountWithoutEagerLoading = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Test with eager loading (should be optimized)
        DB::enableQueryLog();
        $startTime = microtime(true);
        
        $organizations = Organization::with('activities', 'news')->get();
        foreach ($organizations as $organization) {
            $organization->activities; // Already loaded
            $organization->news;     // Already loaded
        }
        
        $timeWithEagerLoading = microtime(true) - $startTime;
        $queryCountWithEagerLoading = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Eager loading should use significantly fewer queries
        $this->assertLessThan(
            $queryCountWithoutEagerLoading,
            $queryCountWithEagerLoading,
            'Eager loading should reduce query count'
        );

        // Eager loading should be faster
        $this->assertLessThan(
            $timeWithoutEagerLoading,
            $timeWithEagerLoading,
            'Eager loading should be faster'
        );
    }

    public function test_pagination_performance()
    {
        // Create large dataset
        Activity::factory()->count(1000)->create();

        // Test first page
        $startTime = microtime(true);
        $page1 = Activity::with(['organization', 'creator'])
            ->orderBy('start_date', 'desc')
            ->paginate(12);
        $page1Time = microtime(true) - $startTime;

        // Test middle page
        $startTime = microtime(true);
        $page50 = Activity::with(['organization', 'creator'])
            ->orderBy('start_date', 'desc')
            ->paginate(12, ['*'], 'page', 50);
        $page50Time = microtime(true) - $startTime;

        // Test last page
        $startTime = microtime(true);
        $lastPage = Activity::with(['organization', 'creator'])
            ->orderBy('start_date', 'desc')
            ->paginate(12, ['*'], 'page', $page1->lastPage());
        $lastPageTime = microtime(true) - $startTime;

        // All pages should load within reasonable time
        $this->assertLessThan(0.5, $page1Time, 'First page pagination too slow');
        $this->assertLessThan(0.5, $page50Time, 'Middle page pagination too slow');
        $this->assertLessThan(0.5, $lastPageTime, 'Last page pagination too slow');
    }

    public function test_search_performance()
    {
        // Create test data
        Activity::factory()->count(500)->create();
        Announcement::factory()->count(300)->create();
        News::factory()->count(400)->create();

        // Test activity search
        $startTime = microtime(true);
        $activities = Activity::where('title', 'like', '%test%')
            ->orWhere('description', 'like', '%test%')
            ->with(['organization', 'creator'])
            ->paginate(12);
        $activitiesSearchTime = microtime(true) - $startTime;

        $this->assertLessThan(1.0, $activitiesSearchTime, 'Activity search too slow');

        // Test announcement search
        $startTime = microtime(true);
        $announcements = Announcement::where('title', 'like', '%test%')
            ->orWhere('content', 'like', '%test%')
            ->with('creator')
            ->paginate(10);
        $announcementsSearchTime = microtime(true) - $startTime;

        $this->assertLessThan(1.0, $announcementsSearchTime, 'Announcement search too slow');

        // Test news search
        $startTime = microtime(true);
        $news = News::where('title', 'like', '%test%')
            ->orWhere('content', 'like', '%test%')
            ->with(['organization', 'creator'])
            ->paginate(10);
        $newsSearchTime = microtime(true) - $startTime;

        $this->assertLessThan(1.0, $newsSearchTime, 'News search too slow');
    }

    public function test_file_upload_performance()
    {
        // Test multiple file upload performance
        $files = [];
        for ($i = 0; $i < 10; $i++) {
            $files[] = \Illuminate\Http\UploadedFile::fake()->image("test{$i}.jpg", 1920, 1080);
        }

        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $startTime = microtime(true);
        
        $response = $this->actingAs($user)->post('/activities', [
            'title' => 'Test Activity with Multiple Images',
            'description' => 'Test Description',
            'organization_id' => $organization->id,
            'start_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'location' => 'Test Location',
            'status' => 'draft',
            'images' => $files,
        ]);
        
        $uploadTime = microtime(true) - $startTime;

        // File upload should complete within reasonable time
        $this->assertLessThan(5.0, $uploadTime, 'Multiple file upload too slow');
        $response->assertRedirect('/activities');
    }

    public function test_calendar_performance()
    {
        // Create activities spanning a year
        Activity::factory()->count(365)->create([
            'start_date' => function () {
                return now()->subDays(rand(0, 365));
            },
            'status' => 'published'
        ]);

        // Test calendar data loading
        $startTime = microtime(true);
        $activities = Activity::with(['organization'])
            ->where('status', 'published')
            ->orderBy('start_date')
            ->get();
        $calendarTime = microtime(true) - $startTime;

        $this->assertLessThan(1.0, $calendarTime, 'Calendar data loading too slow');
        $this->assertCount(365, $activities);
    }

    public function test_dashboard_performance()
    {
        // Create realistic dataset
        $organizations = Organization::factory()->count(20)->create();
        $users = User::factory()->count(100)->create();
        $activities = Activity::factory()->count(50)->create();
        $announcements = Announcement::factory()->count(30)->create();
        $news = News::factory()->count(40)->create(['is_published' => true]);

        // Simulate dashboard data loading
        $startTime = microtime(true);
        
        // Recent activities
        $recentActivities = Activity::with(['organization', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent announcements
        $recentAnnouncements = Announcement::with('creator')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent news
        $recentNews = News::with(['organization', 'creator'])
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        // Statistics
        $stats = [
            'organizations_count' => Organization::count(),
            'users_count' => User::count(),
            'activities_count' => Activity::count(),
            'announcements_count' => Announcement::count(),
            'news_count' => News::where('is_published', true)->count(),
        ];
        
        $dashboardTime = microtime(true) - $startTime;

        // Dashboard should load quickly
        $this->assertLessThan(1.0, $dashboardTime, 'Dashboard loading too slow');
        $this->assertCount(5, $recentActivities);
        $this->assertCount(5, $recentAnnouncements);
        $this->assertCount(5, $recentNews);
    }

    public function test_concurrent_requests_simulation()
    {
        // Create test data
        Activity::factory()->count(100)->create();

        // Simulate multiple concurrent requests
        $startTime = microtime(true);
        
        $requests = [];
        for ($i = 0; $i < 10; $i++) {
            $requests[] = $this->get('/activities?page=' . ($i + 1));
        }
        
        $totalTime = microtime(true) - $startTime;

        // Average time per request should be reasonable
        $averageTime = $totalTime / 10;
        $this->assertLessThan(0.5, $averageTime, 'Average request time too high');

        // All requests should be successful
        foreach ($requests as $response) {
            $response->assertStatus(200);
        }
    }

    public function test_memory_usage()
    {
        $initialMemory = memory_get_usage();

        // Create and load large dataset
        Activity::factory()->count(1000)->create();
        
        $activities = Activity::with(['organization', 'creator'])
            ->orderBy('start_date', 'desc')
            ->paginate(50);

        $finalMemory = memory_get_usage();
        $memoryUsed = $finalMemory - $initialMemory;

        // Memory usage should be reasonable (less than 50MB for this operation)
        $this->assertLessThan(50 * 1024 * 1024, $memoryUsed, 'Memory usage too high');
    }

    public function test_database_connection_pooling()
    {
        // Test multiple database operations
        $startTime = microtime(true);
        
        for ($i = 0; $i < 50; $i++) {
            User::factory()->create();
            Organization::factory()->create();
            Activity::factory()->create();
        }
        
        $totalTime = microtime(true) - $startTime;

        // Database operations should be efficient
        $this->assertLessThan(5.0, $totalTime, 'Database operations too slow');
        
        // Verify data was created
        $this->assertDatabaseCount('users', 50);
        $this->assertDatabaseCount('organizations', 50);
        $this->assertDatabaseCount('activities', 50);
    }
}