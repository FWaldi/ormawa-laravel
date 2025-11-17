<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NavigationUITest extends DuskTestCase
{
    /**
     * Test main navigation links work correctly.
     */
    public function test_main_navigation_links()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Portal Ormawa')
                    ->assertPresent('.nav-link')
                    ->assertSeeIn('nav[role="navigation"]', 'Beranda')
                    ->assertSeeIn('nav[role="navigation"]', 'Pengumuman')
                    ->assertSeeIn('nav[role="navigation"]', 'Berita')
                    ->assertSeeIn('nav[role="navigation"]', 'Ormawa')
                    ->assertSeeIn('nav[role="navigation"]', 'Kalender')
                    ->clickLink('Beranda')
                    ->assertPathIs('/')
                    ->screenshot('navigation_main_links');
        });
    }

    /**
     * Test mobile navigation menu toggle.
     */
    public function test_mobile_navigation_toggle()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(375, 667) // Mobile viewport
                    ->visit('/')
                    ->assertMissing('.nav-menu.active')
                    ->click('#mobileMenuToggle')
                    ->waitFor('.nav-menu.active', 5)
                    ->assertVisible('.nav-menu.active')
                    ->assertSeeIn('.nav-menu.active', 'Beranda')
                    ->assertSeeIn('.nav-menu.active', 'Pengumuman')
                    ->assertSeeIn('.nav-menu.active', 'Berita')
                    ->assertSeeIn('.nav-menu.active', 'Ormawa')
                    ->assertSeeIn('.nav-menu.active', 'Kalender')
                    ->click('#mobileMenuToggle')
                    ->waitUntilMissing('.nav-menu.active')
                    ->screenshot('navigation_mobile_toggle');
        });
    }

    /**
     * Test user dropdown menu for authenticated users.
     */
    public function test_user_dropdown_menu()
    {
        $user = \App\Models\User::factory()->create(['name' => 'Test User']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertSee('Test User')
                    ->click('button[aria-label*="User menu"]')
                    ->waitFor('.dropdown-menu', 5)
                    ->assertSee('Profile Saya')
                    ->assertSee('Logout')
                    ->assertPresent('a[href="/profile"]')
                    ->assertPresent('form[action="/logout"]')
                    ->clickLink('Profile Saya')
                    ->assertPathIs('/profile')
                    ->screenshot('navigation_user_dropdown');
        });
    }

    /**
     * Test navigation accessibility features.
     */
    public function test_navigation_accessibility()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertPresent('a[href="#main-content"]') // Skip link
                    ->assertPresent('header[role="banner"]')
                    ->assertPresent('nav[role="navigation"]')
                    ->assertPresent('[aria-label="Main navigation"]')
                    ->assertPresent('button[aria-label="Toggle navigation menu"]')
                    ->assertPresent('.nav-link')
                    ->screenshot('navigation_accessibility');
        });
    }

    /**
     * Test keyboard navigation.
     */
    public function test_keyboard_navigation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->keys('body', '{tab}') // Tab to first focusable element
                    ->assertFocused('a[href="#main-content"]')
                    ->keys('body', '{tab}') // Tab to logo
                    ->assertFocused('.logo')
                    ->keys('body', '{tab}') // Tab to first navigation link
                    ->assertFocused('.nav-link')
                    ->keys('body', '{tab}') // Tab through navigation
                    ->assertFocused('.nav-link')
                    ->screenshot('navigation_keyboard');
        });
    }

    /**
     * Test navigation focus indicators.
     */
    public function test_navigation_focus_indicators()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->click('.nav-link')
                    ->assertFocused('.nav-link')
                    ->assertPresent('.nav-link:focus-visible')
                    ->screenshot('navigation_focus_indicators');
        });
    }

    /**
     * Test responsive navigation behavior.
     */
    public function test_responsive_navigation()
    {
        $this->browse(function (Browser $browser) {
            // Desktop view
            $browser->resize(1200, 800)
                    ->visit('/')
                    ->assertMissing('#mobileMenuToggle')
                    ->assertVisible('.nav-menu')
                    ->screenshot('navigation_desktop');

            // Tablet view
            $browser->resize(768, 1024)
                    ->visit('/')
                    ->assertVisible('#mobileMenuToggle')
                    ->assertVisible('.nav-menu')
                    ->screenshot('navigation_tablet');

            // Mobile view
            $browser->resize(375, 667)
                    ->visit('/')
                    ->assertVisible('#mobileMenuToggle')
                    ->assertVisible('.nav-menu')
                    ->screenshot('navigation_mobile');
        });
    }

    /**
     * Test login/logout navigation flow.
     */
    public function test_login_logout_navigation_flow()
    {
        $user = \App\Models\User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            // Start logged out
            $browser->visit('/')
                    ->assertSee('Log In')
                    ->assertPresent('a[href="/login"]')
                    ->clickLink('Log In')
                    ->assertPathIs('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('Log In')
                    ->assertPathIs('/dashboard')
                    ->assertSee($user->name)
                    ->click('button[aria-label*="User menu"]')
                    ->waitFor('.dropdown-menu', 5)
                    ->press('Logout')
                    ->assertPathIs('/')
                    ->assertSee('Log In')
                    ->screenshot('navigation_login_logout_flow');
        });
    }
}