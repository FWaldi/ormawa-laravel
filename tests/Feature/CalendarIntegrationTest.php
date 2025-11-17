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

class CalendarIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Organization $organization;
    private Organization $organization2;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create();
        $this->organization2 = Organization::factory()->create();
    }

    /**
     * Test complete calendar data integration with activities and announcements.
     */
    public function test_complete_calendar_data_integration(): void
    {
        // Create diverse test data
        $activities = Activity::factory()->count(5)->create([
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => now()->addDays(rand(1, 30)),
            'end_date' => now()->addDays(rand(31, 60))
        ]);

        $announcements = Announcement::factory()->count(3)->create([
            'created_by' => $this->user->id
        ]);

        $startDate = now()->format('Y-m-d');
        $endDate = now()->addDays(90)->format('Y-m-d');

        $response = $this->getJson('/calendar/data', [
            'start' => $startDate,
            'end' => $endDate
        ]);

        $response->assertStatus(200);
        $events = $response->json();

        // Should have all activities and announcements
        $this->assertCount(8, $events);

        // Verify activities are properly formatted
        $activityEvents = collect($events)->filter(fn($event) => $event['type'] === 'activity');
        $this->assertCount(5, $activityEvents);

        foreach ($activityEvents as $event) {
            $this->assertArrayHasKey('id', $event);
            $this->assertArrayHasKey('title', $event);
            $this->assertArrayHasKey('description', $event);
            $this->assertArrayHasKey('start', $event);
            $this->assertArrayHasKey('end', $event);
            $this->assertArrayHasKey('type', $event);
            $this->assertArrayHasKey('color', $event);
            $this->assertArrayHasKey('textColor', $event);
            $this->assertArrayHasKey('extendedProps', $event);
            $this->assertEquals('activity', $event['type']);
            $this->assertEquals('#3B82F6', $event['color']);
            $this->assertEquals('#FFFFFF', $event['textColor']);
        }

        // Verify announcements are properly formatted
        $announcementEvents = collect($events)->filter(fn($event) => $event['type'] === 'announcement');
        $this->assertCount(3, $announcementEvents);

        foreach ($announcementEvents as $event) {
            $this->assertEquals('announcement', $event['type']);
            $this->assertEquals('#10B981', $event['color']);
            $this->assertEquals('#FFFFFF', $event['textColor']);
        }
    }

    /**
     * Test calendar caching integration with database queries.
     */
    public function test_calendar_caching_integration(): void
    {
        // Clear any existing cache
        Cache::flush();

        // Create test data
        $activities = Activity::factory()->count(10)->create([
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15'
        ]);

        $startDate = '2024-01-01';
        $endDate = '2024-01-31';

        // Track database queries
        DB::enableQueryLog();

        // First request - should hit database
        $response1 = $this->getJson('/calendar/data', [
            'start' => $startDate,
            'end' => $endDate
        ]);

        $queryCount1 = count(DB::getQueryLog());
        DB::flushQueryLog();

        // Second request - should hit cache
        $response2 = $this->getJson('/calendar/data', [
            'start' => $startDate,
            'end' => $endDate
        ]);

        $queryCount2 = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Both responses should be identical
        $this->assertEquals($response1->json(), $response2->json());

        // Second request should have fewer database queries (cached)
        $this->assertLessThan($queryCount1, $queryCount2);

        // Verify cache exists
        $cacheKey = 'calendar_data_' . md5(serialize([
            'start' => $startDate,
            'end' => $endDate,
            'organization_id' => null,
            'event_type' => 'all',
            'search' => null,
        ]));

        $this->assertNotNull(Cache::get($cacheKey));
    }

    /**
     * Test calendar cache invalidation with new data.
     */
    public function test_calendar_cache_invalidation(): void
    {
        Cache::flush();

        // Create initial data
        $activity1 = Activity::factory()->create([
            'title' => 'Initial Activity',
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $startDate = '2024-01-01';
        $endDate = '2024-01-31';

        // First request
        $response1 = $this->getJson('/calendar/data', [
            'start' => $startDate,
            'end' => $endDate
        ]);

        $this->assertCount(1, $response1->json());

        // Create new activity
        $activity2 = Activity::factory()->create([
            'title' => 'New Activity',
            'start_date' => '2024-01-20',
            'end_date' => '2024-01-20',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        // Clear cache to simulate new data
        Cache::flush();

        // Second request after cache clear
        $response2 = $this->getJson('/calendar/data', [
            'start' => $startDate,
            'end' => $endDate
        ]);

        $this->assertCount(2, $response2->json());
        $titles = collect($response2->json())->pluck('title');
        $this->assertContains('Initial Activity', $titles);
        $this->assertContains('New Activity', $titles);
    }

    /**
     * Test calendar performance with large datasets.
     */
    public function test_calendar_performance_with_large_datasets(): void
    {
        // Create large dataset
        Activity::factory()->count(100)->create([
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => now()->addDays(rand(1, 365)),
            'end_date' => now()->addDays(rand(366, 730))
        ]);

        Announcement::factory()->count(50)->create([
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
        $this->assertCount(150, $response->json());

        // Should complete within reasonable time (adjust threshold as needed)
        $this->assertLessThan(2.0, $executionTime, 'Calendar data retrieval took too long');
    }

    /**
     * Test calendar integration with complex filtering scenarios.
     */
    public function test_calendar_complex_filtering_scenarios(): void
    {
        // Create diverse test data
        $activitiesOrg1 = Activity::factory()->count(5)->create([
            'title' => 'Org1 Activity',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15'
        ]);

        $activitiesOrg2 = Activity::factory()->count(3)->create([
            'title' => 'Org2 Activity',
            'status' => 'published',
            'organization_id' => $this->organization2->id,
            'created_by' => $this->user->id,
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15'
        ]);

        $announcements = Announcement::factory()->count(4)->create([
            'title' => 'Test Announcement',
            'created_by' => $this->user->id
        ]);

        // Test multiple filters combined
        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'organization_id' => $this->organization->id,
            'event_type' => 'activity',
            'search' => 'Org1'
        ]);

        $response->assertStatus(200);
        $events = $response->json();

        // Should only return Org1 activities matching search
        $this->assertCount(5, $events);
        foreach ($events as $event) {
            $this->assertEquals('activity', $event['type']);
            $this->assertEquals('Org1 Activity', $event['title']);
        }
    }

    /**
     * Test calendar integration with database relationships.
     */
    public function test_calendar_database_relationships_integration(): void
    {
        $activity = Activity::factory()->create([
            'title' => 'Activity with Relationships',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15'
        ]);

        $announcement = Announcement::factory()->create([
            'title' => 'Announcement with Creator',
            'content' => 'Test content',
            'created_by' => $this->user->id
        ]);

        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31'
        ]);

        $response->assertStatus(200);
        $events = $response->json();

        // Verify activity relationships are loaded
        $activityEvent = collect($events)->firstWhere('type', 'activity');
        $this->assertNotNull($activityEvent);
        $this->assertEquals($this->organization->name, $activityEvent['extendedProps']['organization']);
        $this->assertEquals($this->user->name, $activityEvent['extendedProps']['creator']);

        // Verify announcement relationships are loaded
        $announcementEvent = collect($events)->firstWhere('type', 'announcement');
        $this->assertNotNull($announcementEvent);
        $this->assertEquals($this->user->name, $announcementEvent['extendedProps']['creator']);
    }

    /**
     * Test calendar integration with concurrent requests.
     */
    public function test_calendar_concurrent_requests(): void
    {
        // Create test data
        Activity::factory()->count(20)->create([
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15'
        ]);

        $startDate = '2024-01-01';
        $endDate = '2024-01-31';

        // Simulate concurrent requests
        $responses = [];
        for ($i = 0; $i < 5; $i++) {
            $responses[] = $this->getJson('/calendar/data', [
                'start' => $startDate,
                'end' => $endDate
            ]);
        }

        // All responses should be identical
        $firstResponse = $responses[0]->json();
        foreach ($responses as $response) {
            $response->assertStatus(200);
            $this->assertEquals($firstResponse, $response->json());
        }
    }

    /**
     * Test calendar integration with edge case dates.
     */
    public function test_calendar_edge_case_dates(): void
    {
        // Create activities with edge case dates
        $activities = [
            Activity::factory()->create([
                'title' => 'Leap Year Activity',
                'start_date' => '2024-02-29',
                'end_date' => '2024-02-29',
                'status' => 'published',
                'organization_id' => $this->organization->id,
                'created_by' => $this->user->id
            ]),
            Activity::factory()->create([
                'title' => 'Year End Activity',
                'start_date' => '2024-12-31',
                'end_date' => '2024-12-31',
                'status' => 'published',
                'organization_id' => $this->organization->id,
                'created_by' => $this->user->id
            ]),
            Activity::factory()->create([
                'title' => 'Year Start Activity',
                'start_date' => '2024-01-01',
                'end_date' => '2024-01-01',
                'status' => 'published',
                'organization_id' => $this->organization->id,
                'created_by' => $this->user->id
            ])
        ];

        // Test full year range
        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-12-31'
        ]);

        $response->assertStatus(200);
        $events = $response->json();
        $this->assertCount(3, $events);

        $titles = collect($events)->pluck('title');
        $this->assertContains('Leap Year Activity', $titles);
        $this->assertContains('Year End Activity', $titles);
        $this->assertContains('Year Start Activity', $titles);
    }

    /**
     * Test calendar integration with timezone handling.
     */
    public function test_calendar_timezone_handling(): void
    {
        // Create activity with specific time
        $activity = Activity::factory()->create([
            'title' => 'Timezone Test Activity',
            'start_date' => '2024-01-15 14:30:00',
            'end_date' => '2024-01-15 16:30:00',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31'
        ]);

        $response->assertStatus(200);
        $events = $response->json();
        $event = $events[0];

        // Verify ISO 8601 format
        $this->assertStringContains('T', $event['start']);
        $this->assertStringContains('T', $event['end']);
        
        // Verify timezone information is preserved
        $startDate = Carbon::parse($event['start']);
        $endDate = Carbon::parse($event['end']);
        
        $this->assertEquals('2024-01-15', $startDate->format('Y-m-d'));
        $this->assertEquals('2024-01-15', $endDate->format('Y-m-d'));
    }

    /**
     * Test calendar integration with cache key uniqueness.
     */
    public function test_calendar_cache_key_uniqueness(): void
    {
        Cache::flush();

        // Create test data
        Activity::factory()->create([
            'title' => 'Test Activity',
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        // Make requests with different parameters
        $response1 = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'organization_id' => $this->organization->id
        ]);

        $response2 = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'organization_id' => $this->organization2->id
        ]);

        $response3 = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'event_type' => 'activity'
        ]);

        // All should return different results
        $this->assertCount(1, $response1->json());
        $this->assertCount(0, $response2->json());
        $this->assertCount(1, $response3->json());

        // Verify different cache keys are used
        $cacheKeys = Cache::getMemory()?->getKeys() ?? [];
        $this->assertGreaterThan(0, count($cacheKeys));
    }
}