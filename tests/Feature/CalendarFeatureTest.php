<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CalendarFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $admin;
    private Organization $organization;
    private Organization $organization2;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->organization = Organization::factory()->create();
        $this->organization2 = Organization::factory()->create();
    }

    /**
     * Test calendar index page loads successfully.
     */
    public function test_calendar_index_page_loads_successfully(): void
    {
        $response = $this->get(route('calendar.index'));

        $response->assertStatus(200);
        $response->assertViewIs('calendar.index');
        $response->assertViewHas('organizations');
        $response->assertViewHas('currentDate');
        $response->assertViewHas('view');
    }

    /**
     * Test calendar index page displays organizations.
     */
    public function test_calendar_index_displays_organizations(): void
    {
        Organization::factory()->count(3)->create();

        $response = $this->get(route('calendar.index'));

        $response->assertViewHas('organizations', function ($organizations) {
            return $organizations->count() === 5; // 2 from setUp + 3 created
        });
    }

    /**
     * Test calendar data endpoint returns activities.
     */
    public function test_calendar_data_returns_activities(): void
    {
        $activity = Activity::factory()->create([
            'title' => 'Test Activity',
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31'
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment([
            'id' => 'activity_' . $activity->id,
            'title' => 'Test Activity',
            'type' => 'activity'
        ]);
    }

    /**
     * Test calendar data endpoint returns announcements.
     */
    public function test_calendar_data_returns_announcements(): void
    {
        $announcement = Announcement::factory()->create([
            'title' => 'Test Announcement',
            'content' => 'Test Content',
            'created_by' => $this->user->id
        ]);

        $response = $this->getJson('/calendar/data', [
            'start' => $announcement->created_at->format('Y-m-d'),
            'end' => $announcement->created_at->addDay()->format('Y-m-d')
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment([
            'id' => 'announcement_' . $announcement->id,
            'title' => 'Test Announcement',
            'type' => 'announcement'
        ]);
    }

    /**
     * Test calendar filtering by organization.
     */
    public function test_calendar_filtering_by_organization(): void
    {
        // Create activities for different organizations
        $activity1 = Activity::factory()->create([
            'title' => 'Activity Org 1',
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $activity2 = Activity::factory()->create([
            'title' => 'Activity Org 2',
            'start_date' => '2024-01-16',
            'end_date' => '2024-01-16',
            'status' => 'published',
            'organization_id' => $this->organization2->id,
            'created_by' => $this->user->id
        ]);

        // Filter by first organization
        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'organization_id' => $this->organization->id
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['title' => 'Activity Org 1']);
        $response->assertJsonMissing(['title' => 'Activity Org 2']);
    }

    /**
     * Test calendar filtering by event type.
     */
    public function test_calendar_filtering_by_event_type(): void
    {
        $activity = Activity::factory()->create([
            'title' => 'Test Activity',
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $announcement = Announcement::factory()->create([
            'title' => 'Test Announcement',
            'created_by' => $this->user->id
        ]);

        // Filter by activities only
        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'event_type' => 'activity'
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['type' => 'activity']);
        $response->assertJsonMissing(['type' => 'announcement']);

        // Filter by announcements only
        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'event_type' => 'announcement'
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['type' => 'announcement']);
        $response->assertJsonMissing(['type' => 'activity']);
    }

    /**
     * Test calendar search functionality.
     */
    public function test_calendar_search_functionality(): void
    {
        $activity1 = Activity::factory()->create([
            'title' => 'Important Meeting',
            'description' => 'Team sync meeting',
            'location' => 'Conference Room A',
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $activity2 = Activity::factory()->create([
            'title' => 'Casual Gathering',
            'description' => 'Team lunch',
            'location' => 'Cafeteria',
            'start_date' => '2024-01-16',
            'end_date' => '2024-01-16',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        // Search by title
        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'search' => 'Important'
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['title' => 'Important Meeting']);

        // Search by description
        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'search' => 'sync'
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['title' => 'Important Meeting']);

        // Search by location
        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'search' => 'Conference'
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['title' => 'Important Meeting']);
    }

    /**
     * Test calendar date range filtering.
     */
    public function test_calendar_date_range_filtering(): void
    {
        $activity1 = Activity::factory()->create([
            'title' => 'January Activity',
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $activity2 = Activity::factory()->create([
            'title' => 'February Activity',
            'start_date' => '2024-02-15',
            'end_date' => '2024-02-15',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        // Filter for January only
        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31'
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['title' => 'January Activity']);
        $response->assertJsonMissing(['title' => 'February Activity']);
    }

    /**
     * Test calendar caching functionality.
     */
    public function test_calendar_caching_functionality(): void
    {
        $activity = Activity::factory()->create([
            'title' => 'Cached Activity',
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        // First request - should cache the result
        $response1 = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31'
        ]);

        $response1->assertStatus(200);
        $response1->assertJsonCount(1);

        // Verify cache exists
        $cacheKey = 'calendar_data_' . md5(serialize([
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'organization_id' => null,
            'event_type' => 'all',
            'search' => null,
        ]));

        $this->assertNotNull(Cache::get($cacheKey));

        // Second request - should return cached result
        $response2 = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31'
        ]);

        $response2->assertStatus(200);
        $response2->assertJsonCount(1);
        $response2->assertJsonFragment(['title' => 'Cached Activity']);
    }

    /**
     * Test calendar search queries are not cached.
     */
    public function test_calendar_search_queries_not_cached(): void
    {
        $activity = Activity::factory()->create([
            'title' => 'Searchable Activity',
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        // Search request - should not cache
        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'search' => 'Searchable'
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);

        // Verify no cache exists for search queries
        $cacheKey = 'calendar_data_' . md5(serialize([
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'organization_id' => null,
            'event_type' => 'all',
            'search' => 'Searchable',
        ]));

        $this->assertNull(Cache::get($cacheKey));
    }

    /**
     * Test calendar month events endpoint.
     */
    public function test_calendar_month_events_endpoint(): void
    {
        $activity = Activity::factory()->create([
            'title' => 'January Activity',
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $response = $this->getJson('/calendar/month/2024/1');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['title' => 'January Activity']);
    }

    /**
     * Test calendar week events endpoint.
     */
    public function test_calendar_week_events_endpoint(): void
    {
        $activity = Activity::factory()->create([
            'title' => 'Week Activity',
            'start_date' => '2024-01-15', // Monday of week 3 2024
            'end_date' => '2024-01-15',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $response = $this->getJson('/calendar/week/2024/3');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['title' => 'Week Activity']);
    }

    /**
     * Test calendar day events endpoint.
     */
    public function test_calendar_day_events_endpoint(): void
    {
        $activity = Activity::factory()->create([
            'title' => 'Day Activity',
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $response = $this->getJson('/calendar/day/2024/1/15');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['title' => 'Day Activity']);
    }

    /**
     * Test calendar validation with invalid input.
     */
    public function test_calendar_validation_with_invalid_input(): void
    {
        // Invalid date format
        $response = $this->getJson('/calendar/data', [
            'start' => 'invalid-date',
            'end' => '2024-01-31'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['start']);

        // End date before start date
        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-31',
            'end' => '2024-01-01'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['end']);

        // Invalid organization ID
        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'organization_id' => 999
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['organization_id']);

        // Invalid event type
        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'event_type' => 'invalid-type'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['event_type']);
    }

    /**
     * Test calendar with unpublished activities.
     */
    public function test_calendar_excludes_unpublished_activities(): void
    {
        $publishedActivity = Activity::factory()->create([
            'title' => 'Published Activity',
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $draftActivity = Activity::factory()->create([
            'title' => 'Draft Activity',
            'start_date' => '2024-01-16',
            'end_date' => '2024-01-16',
            'status' => 'draft',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31'
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['title' => 'Published Activity']);
        $response->assertJsonMissing(['title' => 'Draft Activity']);
    }

    /**
     * Test calendar with complex date ranges.
     */
    public function test_calendar_with_complex_date_ranges(): void
    {
        // Activity that starts before range but ends within range
        $activity1 = Activity::factory()->create([
            'title' => 'Overlapping Activity',
            'start_date' => '2023-12-25',
            'end_date' => '2024-01-05',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        // Activity that starts and ends within range
        $activity2 = Activity::factory()->create([
            'title' => 'Within Range Activity',
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-20',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        // Activity that starts within range but ends after range
        $activity3 = Activity::factory()->create([
            'title' => 'Extended Activity',
            'start_date' => '2024-01-25',
            'end_date' => '2024-02-05',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        // Activity that completely encompasses the range
        $activity4 = Activity::factory()->create([
            'title' => 'Encompassing Activity',
            'start_date' => '2023-12-01',
            'end_date' => '2024-02-29',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31'
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(4);
        $response->assertJsonFragment(['title' => 'Overlapping Activity']);
        $response->assertJsonFragment(['title' => 'Within Range Activity']);
        $response->assertJsonFragment(['title' => 'Extended Activity']);
        $response->assertJsonFragment(['title' => 'Encompassing Activity']);
    }
}