<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CalendarPerformanceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create();
    }

    /**
     * Test calendar performance with 1000 activities.
     */
    public function test_calendar_performance_with_1000_activities(): void
    {
        // Create large dataset
        Activity::factory()->count(1000)->create([
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => now()->addDays(rand(1, 365)),
            'end_date' => now()->addDays(rand(366, 730))
        ]);

        $startDate = now()->format('Y-m-d');
        $endDate = now()->addYears(2)->format('Y-m-d');

        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        $response = $this->getJson('/calendar/data', [
            'start' => $startDate,
            'end' => $endDate
        ]);

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $executionTime = $endTime - $startTime;
        $memoryUsed = $endMemory - $startMemory;

        $response->assertStatus(200);
        $this->assertCount(1000, $response->json());

        // Performance assertions
        $this->assertLessThan(3.0, $executionTime, 'Calendar query took too long with 1000 activities');
        $this->assertLessThan(50 * 1024 * 1024, $memoryUsed, 'Calendar query used too much memory'); // 50MB limit
    }

    /**
     * Test calendar performance with mixed large dataset.
     */
    public function test_calendar_performance_with_mixed_large_dataset(): void
    {
        // Create mixed large dataset
        Activity::factory()->count(500)->create([
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => now()->addDays(rand(1, 365)),
            'end_date' => now()->addDays(rand(366, 730))
        ]);

        Announcement::factory()->count(300)->create([
            'created_by' => $this->user->id
        ]);

        $startDate = now()->format('Y-m-d');
        $endDate = now()->addYears(2)->format('Y-m-d');

        $startTime = microtime(true);

        $response = $this->getJson('/calendar/data', [
            'start' => $startDate,
            'end' => $endDate
        ]);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $response->assertStatus(200);
        $this->assertCount(800, $response->json());

        $this->assertLessThan(4.0, $executionTime, 'Calendar query took too long with mixed dataset');
    }

    /**
     * Test calendar cache efficiency with large datasets.
     */
    public function test_calendar_cache_efficiency_with_large_datasets(): void
    {
        Cache::flush();

        // Create large dataset
        Activity::factory()->count(800)->create([
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15'
        ]);

        $startDate = '2024-01-01';
        $endDate = '2024-01-31';

        // First request - database hit
        DB::enableQueryLog();
        $startTime1 = microtime(true);

        $response1 = $this->getJson('/calendar/data', [
            'start' => $startDate,
            'end' => $endDate
        ]);

        $endTime1 = microtime(true);
        $queryCount1 = count(DB::getQueryLog());
        DB::flushQueryLog();

        // Second request - cache hit
        $startTime2 = microtime(true);

        $response2 = $this->getJson('/calendar/data', [
            'start' => $startDate,
            'end' => $endDate
        ]);

        $endTime2 = microtime(true);
        $queryCount2 = count(DB::getQueryLog());
        DB::disableQueryLog();

        $time1 = $endTime1 - $startTime1;
        $time2 = $endTime2 - $startTime2;

        // Both responses should be identical
        $this->assertEquals($response1->json(), $response2->json());

        // Cache should be significantly faster
        $this->assertLessThan($time1, $time2, 'Cached request should be faster than database request');
        $this->assertLessThan($queryCount1, $queryCount2, 'Cached request should have fewer database queries');

        // Cache should be at least 50% faster
        $speedImprovement = ($time1 - $time2) / $time1;
        $this->assertGreaterThan(0.5, $speedImprovement, 'Cache should provide at least 50% speed improvement');
    }

    /**
     * Test calendar performance with complex filtering.
     */
    public function test_calendar_performance_with_complex_filtering(): void
    {
        // Create diverse dataset
        Activity::factory()->count(600)->create([
            'title' => 'Test Activity',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => now()->addDays(rand(1, 365)),
            'end_date' => now()->addDays(rand(366, 730))
        ]);

        Announcement::factory()->count(200)->create([
            'title' => 'Test Announcement',
            'created_by' => $this->user->id
        ]);

        $startDate = now()->format('Y-m-d');
        $endDate = now()->addYears(2)->format('Y-m-d');

        $startTime = microtime(true);

        $response = $this->getJson('/calendar/data', [
            'start' => $startDate,
            'end' => $endDate,
            'organization_id' => $this->organization->id,
            'event_type' => 'activity',
            'search' => 'Test'
        ]);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $response->assertStatus(200);
        $this->assertCount(600, $response->json());

        $this->assertLessThan(2.0, $executionTime, 'Complex filtering took too long');
    }

    /**
     * Test calendar performance under concurrent load simulation.
     */
    public function test_calendar_performance_under_concurrent_load(): void
    {
        // Create test data
        Activity::factory()->count(300)->create([
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => now()->addDays(rand(1, 365)),
            'end_date' => now()->addDays(rand(366, 730))
        ]);

        $startDate = now()->format('Y-m-d');
        $endDate = now()->addYears(2)->format('Y-m-d');

        // Simulate concurrent requests
        $responses = [];
        $times = [];

        for ($i = 0; $i < 10; $i++) {
            $startTime = microtime(true);
            $responses[] = $this->getJson('/calendar/data', [
                'start' => $startDate,
                'end' => $endDate
            ]);
            $times[] = microtime(true) - $startTime;
        }

        // All responses should be successful
        foreach ($responses as $response) {
            $response->assertStatus(200);
            $this->assertCount(300, $response->json());
        }

        // Average response time should be reasonable
        $averageTime = array_sum($times) / count($times);
        $this->assertLessThan(1.5, $averageTime, 'Average response time under concurrent load too high');

        // Response times should be consistent (variance should be low)
        $variance = array_map(function($time) use ($averageTime) {
            return pow($time - $averageTime, 2);
        }, $times);
        $standardDeviation = sqrt(array_sum($variance) / count($variance));
        $this->assertLessThan(0.5, $standardDeviation, 'Response times too inconsistent under load');
    }

    /**
     * Test calendar memory efficiency with large datasets.
     */
    public function test_calendar_memory_efficiency_with_large_datasets(): void
    {
        // Create large dataset
        Activity::factory()->count(700)->create([
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => now()->addDays(rand(1, 365)),
            'end_date' => now()->addDays(rand(366, 730))
        ]);

        Announcement::factory()->count(400)->create([
            'created_by' => $this->user->id
        ]);

        $startDate = now()->format('Y-m-d');
        $endDate = now()->addYears(2)->format('Y-m-d');

        $startMemory = memory_get_usage(true);
        $peakMemoryBefore = memory_get_peak_usage(true);

        $response = $this->getJson('/calendar/data', [
            'start' => $startDate,
            'end' => $endDate
        ]);

        $endMemory = memory_get_usage(true);
        $peakMemoryAfter = memory_get_peak_usage(true);

        $memoryUsed = $endMemory - $startMemory;
        $peakMemoryIncrease = $peakMemoryAfter - $peakMemoryBefore;

        $response->assertStatus(200);
        $this->assertCount(1100, $response->json());

        // Memory efficiency assertions
        $this->assertLessThan(30 * 1024 * 1024, $memoryUsed, 'Too much memory used for calendar data');
        $this->assertLessThan(40 * 1024 * 1024, $peakMemoryIncrease, 'Peak memory increase too high');
    }

    /**
     * Test calendar database query efficiency.
     */
    public function test_calendar_database_query_efficiency(): void
    {
        // Create test data
        Activity::factory()->count(400)->create([
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => now()->addDays(rand(1, 365)),
            'end_date' => now()->addDays(rand(366, 730))
        ]);

        Announcement::factory()->count(200)->create([
            'created_by' => $this->user->id
        ]);

        $startDate = now()->format('Y-m-d');
        $endDate = now()->addYears(2)->format('Y-m-d');

        DB::enableQueryLog();

        $response = $this->getJson('/calendar/data', [
            'start' => $startDate,
            'end' => $endDate
        ]);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $response->assertStatus(200);
        $this->assertCount(600, $response->json());

        // Should use efficient queries (N+1 problem avoided)
        $this->assertLessThan(10, count($queries), 'Too many database queries executed');

        // Check for eager loading
        $hasEagerLoading = false;
        foreach ($queries as $query) {
            if (strpos($query['query'], 'inner join') !== false || 
                strpos($query['query'], 'left join') !== false) {
                $hasEagerLoading = true;
                break;
            }
        }
        $this->assertTrue($hasEagerLoading, 'No eager loading detected in calendar queries');
    }

    /**
     * Test calendar cache hit ratio over multiple requests.
     */
    public function test_calendar_cache_hit_ratio(): void
    {
        Cache::flush();

        // Create test data
        Activity::factory()->count(200)->create([
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15'
        ]);

        $startDate = '2024-01-01';
        $endDate = '2024-01-31';

        $totalRequests = 20;
        $cacheHits = 0;

        for ($i = 0; $i < $totalRequests; $i++) {
            DB::enableQueryLog();
            
            $this->getJson('/calendar/data', [
                'start' => $startDate,
                'end' => $endDate
            ]);

            $queryCount = count(DB::getQueryLog());
            DB::disableQueryLog();

            // If no queries executed, it was a cache hit
            if ($queryCount === 0) {
                $cacheHits++;
            }
        }

        $cacheHitRatio = $cacheHits / $totalRequests;
        $this->assertGreaterThan(0.8, $cacheHitRatio, 'Cache hit ratio too low');
    }

    /**
     * Test calendar performance with different date ranges.
     */
    public function test_calendar_performance_with_different_date_ranges(): void
    {
        // Create test data spanning multiple years
        Activity::factory()->count(500)->create([
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => now()->subYears(2)->addDays(rand(1, 730)),
            'end_date' => now()->subYears(2)->addDays(rand(731, 1460))
        ]);

        $dateRanges = [
            ['start' => now()->subYear()->format('Y-m-d'), 'end' => now()->format('Y-m-d')], // 1 year
            ['start' => now()->subMonths(6)->format('Y-m-d'), 'end' => now()->format('Y-m-d')], // 6 months
            ['start' => now()->subMonth()->format('Y-m-d'), 'end' => now()->format('Y-m-d')], // 1 month
            ['start' => now()->subWeek()->format('Y-m-d'), 'end' => now()->format('Y-m-d')], // 1 week
        ];

        foreach ($dateRanges as $range) {
            $startTime = microtime(true);

            $response = $this->getJson('/calendar/data', $range);

            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;

            $response->assertStatus(200);

            // Performance should be consistent regardless of date range
            $this->assertLessThan(2.0, $executionTime, 
                "Performance too slow for date range: {$range['start']} to {$range['end']}");
        }
    }

    /**
     * Test calendar performance degradation with increasing data size.
     */
    public function test_calendar_performance_degradation_with_increasing_data_size(): void
    {
        $dataSizes = [100, 300, 500, 800, 1000];
        $performanceData = [];

        foreach ($dataSizes as $size) {
            // Clean up previous data
            Activity::query()->delete();
            Announcement::query()->delete();

            // Create test data
            Activity::factory()->count($size)->create([
                'status' => 'published',
                'organization_id' => $this->organization->id,
                'created_by' => $this->user->id,
                'start_date' => now()->addDays(rand(1, 365)),
                'end_date' => now()->addDays(rand(366, 730))
            ]);

            $startDate = now()->format('Y-m-d');
            $endDate = now()->addYears(2)->format('Y-m-d');

            $startTime = microtime(true);

            $response = $this->getJson('/calendar/data', [
                'start' => $startDate,
                'end' => $endDate
            ]);

            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;

            $response->assertStatus(200);
            $this->assertCount($size, $response->json());

            $performanceData[$size] = $executionTime;
        }

        // Performance should not degrade exponentially
        $time100 = $performanceData[100];
        $time1000 = $performanceData[1000];
        $degradationRatio = $time1000 / $time100;

        // 10x data should not take more than 20x time
        $this->assertLessThan(20, $degradationRatio, 'Performance degradation too high');
    }

    /**
     * Test calendar cache memory usage.
     */
    public function test_calendar_cache_memory_usage(): void
    {
        Cache::flush();

        // Create test data
        Activity::factory()->count(400)->create([
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15'
        ]);

        $startDate = '2024-01-01';
        $endDate = '2024-01-31';

        // First request to populate cache
        $this->getJson('/calendar/data', [
            'start' => $startDate,
            'end' => $endDate
        ]);

        // Measure cache memory usage
        $cacheKey = 'calendar_data_' . md5(serialize([
            'start' => $startDate,
            'end' => $endDate,
            'organization_id' => null,
            'event_type' => 'all',
            'search' => null,
        ]));

        $cachedData = Cache::get($cacheKey);
        $cacheSize = strlen(serialize($cachedData));

        // Cache should not use excessive memory
        $this->assertLessThan(5 * 1024 * 1024, $cacheSize, 'Cache entry too large'); // 5MB limit
    }
}