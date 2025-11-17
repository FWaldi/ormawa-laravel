<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ResponsiveUITest extends DuskTestCase
{
    /**
     * Test responsive layout on mobile devices.
     */
    public function test_mobile_responsive_layout()
    {
        $user = \App\Models\User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->resize(375, 667) // iPhone SE
                    ->visit('/dashboard')
                    ->waitForText('Dashboard Pengguna', 5)
                    ->assertMissing('nav.hidden.lg\\:flex') // Desktop nav hidden
                    ->assertVisible('button[aria-label="Toggle mobile navigation menu"]')
                    ->assertPresent('.grid.grid-cols-1') // Stats should be single column
                    ->assertPresent('.grid.grid-cols-1.lg\\:grid-cols-2') // Activities/announcements stacked
                    ->screenshot('responsive_mobile_dashboard');
        });
    }

    /**
     * Test responsive layout on tablet devices.
     */
    public function test_tablet_responsive_layout()
    {
        $user = \App\Models\User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->resize(768, 1024) // iPad
                    ->visit('/dashboard')
                    ->waitForText('Dashboard Pengguna', 5)
                    ->assertMissing('nav.hidden.lg\\:flex') // Desktop nav hidden
                    ->assertVisible('button[aria-label="Toggle mobile navigation menu"]')
                    ->assertPresent('.grid.grid-cols-1.md\\:grid-cols-2') // Stats 2 columns
                    ->assertPresent('.grid.grid-cols-1.lg\\:grid-cols-2') // Activities/announcements side by side
                    ->screenshot('responsive_tablet_dashboard');
        });
    }

    /**
     * Test responsive layout on desktop devices.
     */
    public function test_desktop_responsive_layout()
    {
        $user = \App\Models\User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->resize(1200, 800) // Desktop
                    ->visit('/dashboard')
                    ->waitForText('Dashboard Pengguna', 5)
                    ->assertVisible('nav.hidden.lg\\:flex') // Desktop nav visible
                    ->assertMissing('button[aria-label="Toggle mobile navigation menu"]')
                    ->assertPresent('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4') // Stats 4 columns
                    ->assertPresent('.grid.grid-cols-1.lg\\:grid-cols-2') // Activities/announcements side by side
                    ->screenshot('responsive_desktop_dashboard');
        });
    }

    /**
     * Test responsive navigation menu behavior.
     */
    public function test_responsive_navigation_menu()
    {
        $this->browse(function (Browser $browser) {
            // Mobile
            $browser->resize(375, 667)
                    ->visit('/')
                    ->assertMissing('nav.hidden.lg\\:flex')
                    ->assertVisible('button[aria-label="Toggle mobile navigation menu"]')
                    ->click('button[aria-label="Toggle mobile navigation menu"]')
                    ->waitFor('#mobile-menu', 5)
                    ->assertSeeIn('#mobile-menu', 'Beranda')
                    ->assertSeeIn('#mobile-menu', 'Pengumuman')
                    ->screenshot('responsive_mobile_menu');

            // Desktop
            $browser->resize(1200, 800)
                    ->visit('/')
                    ->assertVisible('nav.hidden.lg\\:flex')
                    ->assertMissing('button[aria-label="Toggle mobile navigation menu"]')
                    ->assertSeeIn('nav[role="navigation"]', 'Beranda')
                    ->assertSeeIn('nav[role="navigation"]', 'Pengumuman')
                    ->screenshot('responsive_desktop_menu');
        });
    }

    /**
     * Test responsive quick actions layout.
     */
    public function test_responsive_quick_actions()
    {
        $user = \App\Models\User::factory()->create(['role' => 'admin']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->resize(375, 667) // Mobile
                    ->visit('/dashboard')
                    ->waitForText('Aksi Cepat', 5)
                    ->assertPresent('.grid.grid-cols-1') // Single column on mobile
                    ->screenshot('responsive_quick_actions_mobile');

            $browser->resize(768, 1024) // Tablet
                    ->visit('/dashboard')
                    ->assertPresent('.grid.grid-cols-1.md\\:grid-cols-2') // 2 columns on tablet
                    ->screenshot('responsive_quick_actions_tablet');

            $browser->resize(1200, 800) // Desktop
                    ->visit('/dashboard')
                    ->assertPresent('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4') // 4 columns on desktop
                    ->screenshot('responsive_quick_actions_desktop');
        });
    }

    /**
     * Test responsive typography and spacing.
     */
    public function test_responsive_typography()
    {
        $user = \App\Models\User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->resize(375, 667) // Mobile
                    ->visit('/dashboard')
                    ->waitForText('Dashboard Pengguna', 5)
                    ->assertPresent('h1.text-3xl') // Main heading
                    ->assertPresent('p.text-sm') // Smaller text on mobile
                    ->screenshot('responsive_typography_mobile');

            $browser->resize(1200, 800) // Desktop
                    ->visit('/dashboard')
                    ->assertPresent('h1.text-3xl') // Same heading size
                    ->assertPresent('p.text-sm') // Consistent text sizing
                    ->screenshot('responsive_typography_desktop');
        });
    }

    /**
     * Test responsive organization overview.
     */
    public function test_responsive_organization_overview()
    {
        $organization = \App\Models\Organization::factory()->create();
        $user = \App\Models\User::factory()->create([
            'role' => 'ormawa',
            'organization_id' => $organization->id
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->resize(375, 667) // Mobile
                    ->visit('/dashboard')
                    ->waitForText('Organisasi Saya', 5)
                    ->assertPresent('.grid.grid-cols-1') // Single column on mobile
                    ->screenshot('responsive_org_overview_mobile');

            $browser->resize(1200, 800) // Desktop
                    ->visit('/dashboard')
                    ->assertPresent('.grid.grid-cols-1.md\\:grid-cols-3') // 3 columns on desktop
                    ->screenshot('responsive_org_overview_desktop');
        });
    }

    /**
     * Test responsive breakpoints consistency.
     */
    public function test_responsive_breakpoints()
    {
        $user = \App\Models\User::factory()->create();

        $breakpoints = [
            ['width' => 639, 'name' => 'sm-max'],
            ['width' => 640, 'name' => 'sm-min'],
            ['width' => 767, 'name' => 'md-max'],
            ['width' => 768, 'name' => 'md-min'],
            ['width' => 1023, 'name' => 'lg-max'],
            ['width' => 1024, 'name' => 'lg-min'],
        ];

        foreach ($breakpoints as $breakpoint) {
            $this->browse(function (Browser $browser) use ($user, $breakpoint) {
                $browser->loginAs($user)
                        ->resize($breakpoint['width'], 800)
                        ->visit('/dashboard')
                        ->waitForText('Dashboard Pengguna', 5)
                        ->screenshot("responsive_breakpoint_{$breakpoint['name']}_{$breakpoint['width']}");
            });
        }
    }
}