<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\CalendarController;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Mockery;

class CalendarControllerTest extends TestCase
{
    private CalendarController $controller;
    private User $user;
    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new CalendarController();
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test index method returns correct view with data.
     */
    public function test_index_returns_view_with_organizations_and_current_date(): void
    {
        // Create additional organizations
        Organization::factory()->count(3)->create();
        
        $request = new Request([
            'date' => '2024-01-15',
            'view' => 'month'
        ]);

        $response = $this->controller->index($request);

        $this->assertEquals('calendar.index', $response->getName());
        $viewData = $response->getData();
        
        $this->assertArrayHasKey('organizations', $viewData);
        $this->assertArrayHasKey('currentDate', $viewData);
        $this->assertArrayHasKey('view', $viewData);
        
        $this->assertEquals('2024-01-15', $viewData['currentDate']);
        $this->assertEquals('month', $viewData['view']);
        $this->assertCount(4, $viewData['organizations']); // 1 from setUp + 3 created
    }

    /**
     * Test index method uses default values when no parameters provided.
     */
    public function test_index_uses_default_values(): void
    {
        $request = new Request();

        $response = $this->controller->index($request);
        $viewData = $response->getData();

        $this->assertEquals(now()->format('Y-m-d'), $viewData['currentDate']);
        $this->assertEquals('month', $viewData['view']);
    }

    /**
     * Test data method validation with valid input.
     */
    public function test_data_method_validates_request_successfully(): void
    {
        $request = new Request([
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'organization_id' => $this->organization->id,
            'event_type' => 'activity',
            'search' => 'test'
        ]);

        $this->expectNotToPerformAssertions();
        
        // This should not throw validation exception
        $response = $this->controller->data($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /**
     * Test data method validation failure with invalid input.
     */
    public function test_data_method_validation_fails_with_invalid_input(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = new Request([
            'start' => 'invalid-date',
            'end' => '2024-01-31',
            'organization_id' => 999, // Non-existent ID
            'event_type' => 'invalid-type'
        ]);

        $this->controller->data($request);
    }

    /**
     * Test data method generates correct cache key.
     */
    public function test_data_method_generates_correct_cache_key(): void
    {
        Cache::shouldReceive('get')->once()->andReturn(null);
        Cache::shouldReceive('put')->once();

        $request = new Request([
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'organization_id' => $this->organization->id,
            'event_type' => 'activity'
        ]);

        $this->controller->data($request);
    }

    /**
     * Test data method returns cached data when available.
     */
    public function test_data_method_returns_cached_data(): void
    {
        $cachedEvents = [
            ['id' => 'activity_1', 'title' => 'Cached Activity']
        ];

        Cache::shouldReceive('get')
            ->once()
            ->andReturn($cachedEvents);

        $request = new Request([
            'start' => '2024-01-01',
            'end' => '2024-01-31'
        ]);

        $response = $this->controller->data($request);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals($cachedEvents, $responseData);
    }

    /**
     * Test data method does not cache search queries.
     */
    public function test_data_method_does_not_cache_search_queries(): void
    {
        Cache::shouldReceive('get')->never();
        Cache::shouldReceive('put')->never();

        Activity::factory()->create([
            'title' => 'Test Activity',
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $request = new Request([
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'search' => 'Test Activity'
        ]);

        $response = $this->controller->data($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /**
     * Test filterByDateRange method reuses data method.
     */
    public function test_filter_by_date_range_reuses_data_method(): void
    {
        $request = new Request([
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31',
            'organization_id' => $this->organization->id,
            'event_type' => 'activity'
        ]);

        $response = $this->controller->filterByDateRange($request);
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Verify the request was modified correctly
        $this->assertEquals('2024-01-01', $request->get('start'));
        $this->assertEquals('2024-01-31', $request->get('end'));
    }

    /**
     * Test monthEvents method with specific year and month.
     */
    public function test_month_events_with_specific_year_month(): void
    {
        $request = new Request();

        $response = $this->controller->monthEvents($request, 2024, 1);
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Verify request was modified with correct date range
        $this->assertEquals('2024-01-01', $request->get('start'));
        $this->assertEquals('2024-01-31', $request->get('end'));
    }

    /**
     * Test monthEvents method with default values.
     */
    public function test_month_events_uses_default_values(): void
    {
        $request = new Request();

        $response = $this->controller->monthEvents($request);
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Should use current year and month
        $currentMonth = now()->format('Y-m-d');
        $expectedStart = now()->startOfMonth()->format('Y-m-d');
        $expectedEnd = now()->endOfMonth()->format('Y-m-d');

        $this->assertEquals($expectedStart, $request->get('start'));
        $this->assertEquals($expectedEnd, $request->get('end'));
    }

    /**
     * Test weekEvents method with specific year and week.
     */
    public function test_week_events_with_specific_year_week(): void
    {
        $request = new Request();

        $response = $this->controller->weekEvents($request, 2024, 1);
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Verify request was modified with correct week date range
        $startDate = Carbon::now()->setISODate(2024, 1)->startOfWeek();
        $endDate = Carbon::now()->setISODate(2024, 1)->endOfWeek();

        $this->assertEquals($startDate->format('Y-m-d'), $request->get('start'));
        $this->assertEquals($endDate->format('Y-m-d'), $request->get('end'));
    }

    /**
     * Test dayEvents method with specific year, month, and day.
     */
    public function test_day_events_with_specific_date(): void
    {
        $request = new Request();

        $response = $this->controller->dayEvents($request, 2024, 1, 15);
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Verify request was modified with correct day date range
        $this->assertEquals('2024-01-15', $request->get('start'));
        $this->assertEquals('2024-01-15', $request->get('end'));
    }

    /**
     * Test event data structure for activities.
     */
    public function test_activity_event_data_structure(): void
    {
        $activity = Activity::factory()->create([
            'title' => 'Test Activity',
            'description' => 'Test Description',
            'location' => 'Test Location',
            'start_date' => '2024-01-15 10:00:00',
            'end_date' => '2024-01-15 12:00:00',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $request = new Request([
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'event_type' => 'activity'
        ]);

        $response = $this->controller->data($request);
        $events = json_decode($response->getContent(), true);

        $this->assertCount(1, $events);
        $event = $events[0];

        $this->assertEquals('activity_' . $activity->id, $event['id']);
        $this->assertEquals('Test Activity', $event['title']);
        $this->assertEquals('Test Description', $event['description']);
        $this->assertEquals('activity', $event['type']);
        $this->assertEquals('#3B82F6', $event['color']);
        $this->assertEquals('#FFFFFF', $event['textColor']);
        $this->assertArrayHasKey('extendedProps', $event);
        $this->assertEquals('Test Location', $event['extendedProps']['location']);
        $this->assertEquals($this->organization->name, $event['extendedProps']['organization']);
    }

    /**
     * Test event data structure for announcements.
     */
    public function test_announcement_event_data_structure(): void
    {
        $announcement = Announcement::factory()->create([
            'title' => 'Test Announcement',
            'content' => 'Test Content',
            'category' => 'Test Category',
            'created_by' => $this->user->id
        ]);

        $request = new Request([
            'start' => $announcement->created_at->format('Y-m-d'),
            'end' => $announcement->created_at->addDay()->format('Y-m-d'),
            'event_type' => 'announcement'
        ]);

        $response = $this->controller->data($request);
        $events = json_decode($response->getContent(), true);

        $this->assertCount(1, $events);
        $event = $events[0];

        $this->assertEquals('announcement_' . $announcement->id, $event['id']);
        $this->assertEquals('Test Announcement', $event['title']);
        $this->assertEquals('Test Content', $event['description']);
        $this->assertEquals('announcement', $event['type']);
        $this->assertEquals('#10B981', $event['color']);
        $this->assertEquals('#FFFFFF', $event['textColor']);
        $this->assertArrayHasKey('extendedProps', $event);
        $this->assertEquals('Test Category', $event['extendedProps']['category']);
        $this->assertEquals($this->user->name, $event['extendedProps']['creator']);
    }

    /**
     * Test events are sorted by start date.
     */
    public function test_events_are_sorted_by_start_date(): void
    {
        // Create activities with different dates
        $activity1 = Activity::factory()->create([
            'title' => 'Later Activity',
            'start_date' => '2024-01-20',
            'end_date' => '2024-01-20',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $activity2 = Activity::factory()->create([
            'title' => 'Earlier Activity',
            'start_date' => '2024-01-10',
            'end_date' => '2024-01-10',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $request = new Request([
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'event_type' => 'activity'
        ]);

        $response = $this->controller->data($request);
        $events = json_decode($response->getContent(), true);

        $this->assertCount(2, $events);
        $this->assertEquals('Earlier Activity', $events[0]['title']);
        $this->assertEquals('Later Activity', $events[1]['title']);
    }
}