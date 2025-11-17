<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalRoutesTest extends TestCase
{
    /**
     * Test that public routes load successfully without authentication.
     */
    public function test_public_routes_load_successfully()
    {
        // Test home page
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Portal Ormawa');

        // Test login page
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk');

        // Test register page
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Daftar');

        // Test public index routes (should work without auth)
        $response = $this->get('/organizations');
        $response->assertStatus(200);

        $response = $this->get('/announcements');
        $response->assertStatus(200);

        $response = $this->get('/news');
        $response->assertStatus(200);

        $response = $this->get('/calendar');
        $response->assertStatus(200);
    }

    /**
     * Test that protected routes redirect to login when not authenticated.
     */
    public function test_protected_routes_require_authentication()
    {
        // Test dashboard requires auth
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        // Test profile requires auth
        $response = $this->get('/profile');
        $response->assertRedirect('/login');

        // Test create routes require auth
        $response = $this->get('/organizations/create');
        $response->assertRedirect('/login');

        $response = $this->get('/announcements/create');
        $response->assertRedirect('/login');

        $response = $this->get('/news/create');
        $response->assertRedirect('/login');
    }

    /**
     * Test that pages contain expected navigation elements.
     */
    public function test_navigation_elements_present()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Beranda');
        $response->assertSee('Pengumuman');
        $response->assertSee('Berita');
        $response->assertSee('Ormawa');
        $response->assertSee('Kalender');
    }

    /**
     * Test that home page has scroll-snap functionality.
     */
    public function test_home_page_scroll_snap()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('scroll-snap-container');
        $response->assertSee('scroll-snap-section');
    }
}
