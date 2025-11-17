<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PortalOrmawaTest extends DuskTestCase
{
    /**
     * Test that home page loads with "Portal Ormawa" text and navigation links are present and clickable.
     */
    public function test_home_page_and_navigation()
    {
        $this->browse(function (Browser $browser) {
            // Visit home page and assert "Portal Ormawa" text
            $browser->visit('/')
                    ->pause(2000)
                    ->screenshot('home_page_load')
                    ->waitForText('Selamat Datang di', 10)
                    ->assertSee('Portal Ormawa');

            // Assert navigation links are present
            $browser->assertSeeLink('Pengumuman')
                    ->assertSeeLink('Berita')
                    ->assertSeeLink('Ormawa')
                    ->assertSeeLink('Kalender');

            // Click Pengumuman link and verify page loads
            $browser->clickLink('Pengumuman')
                    ->waitForLocation('/announcements', 10)
                    ->assertPathIs('/announcements')
                    ->assertPresent('body')
                    ->assertSeeIn('body', 'Pengumuman'); // Assuming the page has "Pengumuman" in title or content

            // Go back to home
            $browser->visit('/');

            // Click Berita link and verify page loads
            $browser->clickLink('Berita')
                    ->waitForLocation('/news', 10)
                    ->assertPathIs('/news')
                    ->assertPresent('body')
                    ->assertSeeIn('body', 'Berita'); // Assuming the page has "Berita" in title or content

            // Go back to home
            $browser->visit('/');

            // Click Ormawa link and verify page loads
            $browser->clickLink('Ormawa')
                    ->waitForLocation('/organizations', 10)
                    ->assertPathIs('/organizations')
                    ->assertPresent('body')
                    ->assertSeeIn('body', 'Ormawa'); // Assuming the page has "Ormawa" in title or content

            // Go back to home
            $browser->visit('/');

            // Click Kalender link and verify page loads
            $browser->clickLink('Kalender')
                    ->waitForLocation('/calendar', 10)
                    ->assertPathIs('/calendar')
                    ->assertPresent('body')
                    ->assertSeeIn('body', 'Kalender'); // Assuming the page has "Kalender" in title or content
        });
    }

    /**
     * Test that login page loads properly.
     */
    public function test_login_page_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitForText('Login', 10)
                    ->assertSee('Login')
                    ->assertPresent('form')
                    ->assertPresent('input[type="email"]')
                    ->assertPresent('input[type="password"]');
        });
    }

    /**
     * Test that no blank pages occur by ensuring body has content.
     */
    public function test_no_blank_pages()
    {
        $this->browse(function (Browser $browser) {
            // Check home page
            $browser->visit('/')
                    ->assertPresent('body')
                    ->assertSee('Portal Ormawa'); // Ensure content is there

            // Check announcements page
            $browser->visit('/announcements')
                    ->assertPresent('body')
                    ->assertSeeIn('body', 'Pengumuman'); // Assuming content

            // Check news page
            $browser->visit('/news')
                    ->assertPresent('body')
                    ->assertSeeIn('body', 'Berita'); // Assuming content

            // Check organizations page
            $browser->visit('/organizations')
                    ->assertPresent('body')
                    ->assertSeeIn('body', 'Ormawa'); // Assuming content

            // Check calendar page
            $browser->visit('/calendar')
                    ->assertPresent('body')
                    ->assertSeeIn('body', 'Kalender'); // Assuming content

            // Check login page
            $browser->visit('/login')
                    ->assertPresent('body')
                    ->assertSee('Login');
        });
    }
}