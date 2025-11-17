<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DashboardUITest extends DuskTestCase
{
    /**
     * Test dashboard displays correctly for authenticated users.
     */
    public function test_dashboard_displays_for_authenticated_user()
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'user'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->waitForText('Dashboard Pengguna', 5)
                    ->assertSee('Selamat Datang, ' . $user->name . '!')
                    ->assertPresent('main[role="main"]')
                    ->assertPresent('header[role="banner"]')
                    ->assertPresent('nav[role="navigation"]')
                    ->assertPresent('[aria-label="Main navigation"]')
                    ->screenshot('dashboard_authenticated_user');
        });
    }

    /**
     * Test dashboard displays correctly for admin users.
     */
    public function test_dashboard_displays_for_admin_user()
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->waitForText('Dashboard Administrator', 5)
                    ->assertSee('Aksi Cepat')
                    ->assertSee('Tambah Organisasi')
                    ->assertSee('Tambah Kegiatan')
                    ->assertSee('Tambah Pengumuman')
                    ->assertSee('Tambah Berita')
                    ->screenshot('dashboard_admin_user');
        });
    }

    /**
     * Test dashboard statistics display correctly.
     */
    public function test_dashboard_statistics_display()
    {
        // Create test data
        \App\Models\Organization::factory()->count(5)->create();
        \App\Models\Activity::factory()->count(10)->create();
        \App\Models\Announcement::factory()->count(3)->create();
        \App\Models\News::factory()->count(7)->create();

        $user = \App\Models\User::factory()->create(['role' => 'admin']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->waitFor('[role="region"]', 5)
                    ->assertSeeIn('[aria-labelledby="stats-organizations"]', '5')
                    ->assertSeeIn('[aria-labelledby="stats-activities"]', '10')
                    ->assertSeeIn('[aria-labelledby="stats-announcements"]', '3')
                    ->assertSeeIn('[aria-labelledby="stats-news"]', '7')
                    ->screenshot('dashboard_statistics');
        });
    }

    /**
     * Test dashboard handles empty data gracefully.
     */
    public function test_dashboard_handles_empty_data()
    {
        $user = \App\Models\User::factory()->create(['role' => 'user']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->waitForText('Belum ada kegiatan', 5)
                    ->assertSee('Belum ada kegiatan')
                    ->assertSee('Belum ada pengumuman')
                    ->assertPresent('[role="status"]')
                    ->screenshot('dashboard_empty_data');
        });
    }

    /**
     * Test dashboard displays organization overview for ormawa users.
     */
    public function test_dashboard_organization_overview_for_ormawa()
    {
        $organization = \App\Models\Organization::factory()->create();
        $user = \App\Models\User::factory()->create([
            'role' => 'ormawa',
            'organization_id' => $organization->id
        ]);

        // Add some activities and users to the organization
        \App\Models\Activity::factory()->count(3)->create(['organization_id' => $organization->id]);
        \App\Models\User::factory()->count(5)->create(['organization_id' => $organization->id]);

        $this->browse(function (Browser $browser) use ($user, $organization) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->waitForText('Organisasi Saya', 5)
                    ->assertSee('Organisasi Saya')
                    ->assertSee($organization->name)
                    ->assertSee('3')
                    ->assertSee('5')
                    ->assertPresent('[role="region"][aria-labelledby="org-overview"]')
                    ->screenshot('dashboard_organization_overview');
        });
    }

    /**
     * Test dashboard responsive behavior on mobile.
     */
    public function test_dashboard_responsive_mobile()
    {
        $user = \App\Models\User::factory()->create(['role' => 'user']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->resize(375, 667) // Mobile viewport
                    ->visit('/dashboard')
                    ->waitForText('Dashboard Pengguna', 5)
                    ->assertSee('Selamat Datang, ' . $user->name . '!')
                    ->assertPresent('button[aria-label="Toggle mobile navigation menu"]')
                    ->screenshot('dashboard_mobile');
        });
    }

    /**
     * Test dashboard accessibility features.
     */
    public function test_dashboard_accessibility_features()
    {
        $user = \App\Models\User::factory()->create(['role' => 'user']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->waitForText('Dashboard Pengguna', 5)
                    ->assertPresent('a[href="#main-content"]') // Skip link
                    ->assertPresent('main[id="main-content"]')
                    ->assertPresent('[role="banner"]')
                    ->assertPresent('[role="main"]')
                    ->assertPresent('[role="navigation"]')
                    ->assertPresent('[aria-label]')
                    ->screenshot('dashboard_accessibility');
        });
    }
}