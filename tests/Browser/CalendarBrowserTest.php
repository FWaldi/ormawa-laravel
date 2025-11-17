<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CalendarBrowserTest extends DuskTestCase
{
    use RefreshDatabase;

    private User $user;
    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create();

        // Create test data
        Activity::factory()->count(5)->create([
            'title' => 'Test Activity',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => now()->addDays(rand(1, 30)),
            'end_date' => now()->addDays(rand(31, 60))
        ]);

        Announcement::factory()->count(3)->create([
            'title' => 'Test Announcement',
            'created_by' => $this->user->id
        ]);
    }

    /**
     * Test calendar page loads and displays correctly.
     */
    public function test_calendar_page_loads_and_displays_correctly(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->assertPathIs('/calendar')
                    ->assertSee('Calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->assertPresent('.calendar-container')
                    ->assertPresent('#calendar')
                    ->assertPresent('.calendar-controls');
        });
    }

    /**
     * Test calendar displays events correctly.
     */
    public function test_calendar_displays_events_correctly(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->waitFor('.fc-event', 10000) // Wait for events to load
                    ->assertPresent('.fc-event')
                    ->assertSeeIn('.fc-event', 'Test Activity')
                    ->assertSeeIn('.fc-event', 'Test Announcement');
        });
    }

    /**
     * Test calendar filtering by organization.
     */
    public function test_calendar_filtering_by_organization(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->waitFor('#organization-filter', 5000)
                    ->select('organization_id', $this->organization->id)
                    ->waitFor('.fc-event', 5000)
                    ->assertPresent('.fc-event');
        });
    }

    /**
     * Test calendar filtering by event type.
     */
    public function test_calendar_filtering_by_event_type(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->waitFor('#event-type-filter', 5000)
                    ->select('event_type', 'activity')
                    ->waitFor('.fc-event', 5000)
                    ->assertPresent('.fc-event');
        });
    }

    /**
     * Test calendar search functionality.
     */
    public function test_calendar_search_functionality(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->waitFor('#search-input', 5000)
                    ->type('search', 'Test Activity')
                    ->pause(1000) // Wait for debounced search
                    ->waitFor('.fc-event', 5000)
                    ->assertSeeIn('.fc-event', 'Test Activity');
        });
    }

    /**
     * Test calendar date navigation.
     */
    public function test_calendar_date_navigation(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->waitFor('.fc-next-button', 5000)
                    ->click('.fc-next-button')
                    ->pause(1000) // Wait for calendar to update
                    ->assertPresent('.fc-day-grid')
                    ->click('.fc-prev-button')
                    ->pause(1000)
                    ->assertPresent('.fc-day-grid');
        });
    }

    /**
     * Test calendar view switching (month, week, day).
     */
    public function test_calendar_view_switching(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->waitFor('.fc-month-button', 5000)
                    ->click('.fc-month-button')
                    ->pause(1000)
                    ->assertPresent('.fc-day-grid')
                    ->click('.fc-week-button')
                    ->pause(1000)
                    ->assertPresent('.fc-time-grid')
                    ->click('.fc-day-button')
                    ->pause(1000)
                    ->assertPresent('.fc-time-grid');
        });
    }

    /**
     * Test calendar event click interactions.
     */
    public function test_calendar_event_click_interactions(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->waitFor('.fc-event', 10000)
                    ->click('.fc-event')
                    ->pause(1000)
                    ->assertPresent('.event-popup') // Assuming event popup/modal
                    ->assertSee('Test Activity');
        });
    }

    /**
     * Test calendar responsive design on mobile.
     */
    public function test_calendar_responsive_design_on_mobile(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(375, 667) // iPhone 6/7/8 dimensions
                    ->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->assertPresent('.calendar-container')
                    ->assertPresent('.fc-toolbar')
                    ->assertPresent('.fc-view-container')
                    ->assertCssClass('.calendar-container', 'responsive-calendar');
        });
    }

    /**
     * Test calendar responsive design on tablet.
     */
    public function test_calendar_responsive_design_on_tablet(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(768, 1024) // iPad dimensions
                    ->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->assertPresent('.calendar-container')
                    ->assertPresent('.fc-toolbar')
                    ->assertPresent('.fc-view-container');
        });
    }

    /**
     * Test calendar responsive design on desktop.
     */
    public function test_calendar_responsive_design_on_desktop(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080) // Full HD desktop
                    ->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->assertPresent('.calendar-container')
                    ->assertPresent('.fc-toolbar')
                    ->assertPresent('.fc-view-container')
                    ->assertPresent('.calendar-sidebar'); // Assuming sidebar on desktop
        });
    }

    /**
     * Test calendar loading states.
     */
    public function test_calendar_loading_states(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->assertPresent('.calendar-loading')
                    ->waitFor('.calendar-container', 5000)
                    ->assertMissing('.calendar-loading')
                    ->assertPresent('.calendar-loaded');
        });
    }

    /**
     * Test calendar error handling.
     */
    public function test_calendar_error_handling(): void
    {
        $this->browse(function (Browser $browser) {
            // Simulate network error by visiting invalid endpoint
            $browser->visit('/calendar/data?start=invalid&end=invalid')
                    ->waitFor('.calendar-error', 5000)
                    ->assertPresent('.calendar-error')
                    ->assertSee('Error loading calendar data');
        });
    }

    /**
     * Test calendar accessibility features.
     */
    public function test_calendar_accessibility_features(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->assertPresent('h1[aria-label]') // Main heading with aria-label
                    ->assertPresent('button[aria-label]') // Navigation buttons with aria-label
                    ->assertPresent('.fc-event[role="button"]') // Events as buttons
                    ->assertPresent('.fc-event[aria-describedby]') // Events with descriptions
                    ->assertAttribute('.fc-next-button', 'aria-label', 'next')
                    ->assertAttribute('.fc-prev-button', 'aria-label', 'previous');
        });
    }

    /**
     * Test calendar keyboard navigation.
     */
    public function test_calendar_keyboard_navigation(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->tab() // Navigate to first focusable element
                    ->assertFocused('.fc-next-button')
                    ->press('ArrowRight') // Navigate to next button
                    ->click()
                    ->pause(1000)
                    ->assertPresent('.fc-day-grid');
        });
    }

    /**
     * Test calendar performance with many events.
     */
    public function test_calendar_performance_with_many_events(): void
    {
        // Create many events
        Activity::factory()->count(100)->create([
            'title' => 'Performance Test Activity',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => now()->addDays(rand(1, 365)),
            'end_date' => now()->addDays(rand(366, 730))
        ]);

        $this->browse(function (Browser $browser) {
            $startTime = microtime(true);
            
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->waitFor('.fc-event', 15000); // Longer wait for many events
            
            $endTime = microtime(true);
            $loadTime = $endTime - $startTime;
            
            // Should load within reasonable time (adjust threshold as needed)
            $this->assertLessThan(5.0, $loadTime, 'Calendar took too long to load with many events');
            
            $browser->assertPresent('.fc-event');
        });
    }

    /**
     * Test calendar event tooltips.
     */
    public function test_calendar_event_tooltips(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->waitFor('.fc-event', 10000)
                    ->mouseover('.fc-event')
                    ->waitFor('.event-tooltip', 2000)
                    ->assertPresent('.event-tooltip')
                    ->assertSeeIn('.event-tooltip', 'Test Activity');
        });
    }

    /**
     * Test calendar print functionality.
     */
    public function test_calendar_print_functionality(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->click('.print-calendar-btn') // Assuming print button exists
                    ->pause(1000)
                    ->assertPresent('.print-calendar-styles');
        });
    }

    /**
     * Test calendar export functionality.
     */
    public function test_calendar_export_functionality(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->waitFor('.export-calendar-btn', 5000)
                    ->click('.export-calendar-btn')
                    ->waitFor('.export-options', 2000)
                    ->assertPresent('.export-options')
                    ->click('.export-ics-btn') // Assuming ICS export option
                    ->pause(1000);
        });
    }

    /**
     * Test calendar real-time updates.
     */
    public function test_calendar_real_time_updates(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->waitFor('.fc-event', 10000)
                    ->assertPresent('.fc-event')
                    ->pause(2000); // Wait for potential real-time updates
                    
            // Test that calendar remains responsive
            $browser->click('.fc-next-button')
                    ->pause(1000)
                    ->assertPresent('.fc-day-grid');
        });
    }

    /**
     * Test calendar with different date formats.
     */
    public function test_calendar_with_different_date_formats(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar?date=2024-01-15')
                    ->waitFor('.calendar-container', 5000)
                    ->assertPresent('.fc-day-header')
                    ->assertSee('January')
                    ->assertSee('2024');
        });
    }

    /**
     * Test calendar event drag and drop (if implemented).
     */
    public function test_calendar_event_drag_and_drop(): void
    {
        // This test assumes drag and drop is implemented
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->waitFor('.fc-event', 10000)
                    ->drag('.fc-event', '.fc-day-future') // Drag to future day
                    ->pause(1000)
                    ->assertPresent('.fc-event'); // Event should still be present
        });
    }

    /**
     * Test calendar event resize (if implemented).
     */
    public function test_calendar_event_resize(): void
    {
        // This test assumes event resizing is implemented
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->waitFor('.fc-event', 10000)
                    ->drag('.fc-event .fc-resizer', '.fc-day-future') // Resize event
                    ->pause(1000)
                    ->assertPresent('.fc-event'); // Event should still be present
        });
    }

    /**
     * Test calendar with RTL languages.
     */
    public function test_calendar_with_rtl_languages(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar?lang=ar') // Arabic RTL language
                    ->waitFor('.calendar-container', 5000)
                    ->assertAttribute('html', 'dir', 'rtl')
                    ->assertCssClass('.calendar-container', 'rtl-calendar');
        });
    }

    /**
     * Test calendar with high contrast mode.
     */
    public function test_calendar_with_high_contrast_mode(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->click('.high-contrast-toggle') // Assuming high contrast toggle
                    ->pause(1000)
                    ->assertCssClass('.calendar-container', 'high-contrast');
        });
    }

    /**
     * Test calendar with reduced motion.
     */
    public function test_calendar_with_reduced_motion(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/calendar')
                    ->waitFor('.calendar-container', 5000)
                    ->assertAttribute('html', 'data-reduced-motion', 'true')
                    ->assertCssClass('.calendar-container', 'reduced-motion');
        });
    }
}