<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class HomeUITest extends DuskTestCase
{
    /**
     * Test home page displays correctly with new UI.
     */
    public function test_home_page_displays_new_ui()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText('Selamat Datang di', 5)
                    ->assertSee('Selamat Datang di')
                    ->assertSee('Portal Ormawa')
                    ->assertSee('Universitas Negeri Padang')
                    ->assertSee('Jelajahi Ormawa')
                    ->assertSee('Tentang Platform')
                    ->screenshot('home_page_new_ui');
        });
    }

    /**
     * Test hero section displays correctly.
     */
    public function test_hero_section_displays()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText('Selamat Datang di', 5)
                    ->assertSee('Selamat Datang di')
                    ->assertSee('Portal Ormawa')
                    ->assertSee('Universitas Negeri Padang')
                    ->assertSee('Jelajahi Ormawa')
                    ->assertSee('Tentang Platform')
                    ->screenshot('hero_section');
        });
    }

    /**
     * Test navigation menu works correctly.
     */
    public function test_navigation_menu_works()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSeeIn('nav[role="navigation"]', 'Beranda')
                    ->assertSeeIn('nav[role="navigation"]', 'Pengumuman')
                    ->assertSeeIn('nav[role="navigation"]', 'Berita')
                    ->assertSeeIn('nav[role="navigation"]', 'Ormawa')
                    ->assertSeeIn('nav[role="navigation"]', 'Kalender')
                    ->assertPresent('nav[role="navigation"] a')
                    ->screenshot('navigation_menu');
        });
    }

    /**
     * Test mobile navigation toggle.
     */
    public function test_mobile_navigation_toggle()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(375, 667) // Mobile viewport
                    ->visit('/')
                    ->assertVisible('#mobileMenuToggle')
                    ->click('#mobileMenuToggle')
                    ->waitFor('#mobile-menu', 5)
                    ->assertVisible('#mobile-menu')
                    ->assertSeeIn('#mobile-menu', 'Beranda')
                    ->assertSeeIn('#mobile-menu', 'Pengumuman')
                    ->screenshot('mobile_navigation_toggle');
        });
    }

    /**
     * Test about section displays correctly.
     */
    public function test_about_section_displays()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText('Tentang Portal Ormawa', 5)
                    ->assertSee('Tentang Portal Ormawa')
                    ->assertSee('Platform terpadu yang dirancang khusus untuk memfasilitasi komunikasi, informasi, dan pengembangan kemahasiswaan di Universitas Negeri Padang')
                    ->assertSee('Komunitas')
                    ->assertSee('Informasi')
                    ->assertSee('Kalender')
                    ->screenshot('about_section');
        });
    }

    /**
     * Test featured organizations section displays.
     */
    public function test_featured_organizations_section()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText('Ormawa Unggulan', 5)
                    ->assertSee('Ormawa Unggulan')
                    ->assertSee('Kenali organisasi mahasiswa terbaik yang telah terbukti berkontribusi besar bagi kemajuan Universitas Negeri Padang')
                    ->assertSee('Badan Eksekutif Mahasiswa')
                    ->assertSee('Lihat Semua Ormawa')
                    ->screenshot('featured_organizations');
        });
    }

    /**
     * Test latest news section displays correctly.
     */
    public function test_latest_news_section()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText('Berita Terbaru', 5)
                    ->assertSee('Berita Terbaru')
                    ->assertSee('Update terkini dari kegiatan, pengumuman, dan informasi penting dari berbagai organisasi mahasiswa UNP')
                    ->assertSee('Pembukaan Masa Orientasi Mahasiswa Baru 2024')
                    ->assertSee('Lihat Semua Berita')
                    ->screenshot('latest_news');
        });
    }

    /**
     * Test call to action section displays.
     */
    public function test_call_to_action_section()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText('Bergabunglah dengan', 5)
                    ->assertSee('Bergabunglah dengan')
                    ->assertSee('Komunitas Kami')
                    ->assertSee('Daftar Sekarang')
                    ->assertSee('Lihat Pengumuman')
                    ->screenshot('call_to_action');
        });
    }







    /**
     * Test responsive design on mobile.
     */
    public function test_responsive_mobile()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(375, 667) // Mobile viewport
                    ->visit('/')
                    ->waitForText('Selamat Datang di', 5)
                    ->assertVisible('#mobileMenuToggle')
                    ->assertPresent('nav[role="navigation"]')
                    ->screenshot('responsive_mobile');
        });
    }

    /**
     * Test responsive design on tablet.
     */
    public function test_responsive_tablet()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(768, 1024) // Tablet viewport
                    ->visit('/')
                    ->waitForText('Selamat Datang di', 5)
                    ->assertVisible('#mobileMenuToggle')
                    ->assertPresent('nav[role="navigation"]')
                    ->screenshot('responsive_tablet');
        });
    }

    /**
     * Test responsive design on desktop.
     */
    public function test_responsive_desktop()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1200, 800) // Desktop viewport
                    ->visit('/')
                    ->waitForText('Selamat Datang di', 5)
                    ->assertMissing('#mobileMenuToggle')
                    ->assertPresent('nav[role="navigation"]')
                    ->screenshot('responsive_desktop');
        });
    }

    /**
     * Test accessibility features.
     */
    public function test_accessibility_features()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertPresent('header[role="banner"]')
                    ->assertPresent('nav[role="navigation"]')
                    ->assertPresent('main[role="main"]')
                    ->screenshot('accessibility_features');
        });
    }

    /**
     * Test keyboard navigation.
     */
    public function test_keyboard_navigation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->keys('', '{tab}') // Tab to first focusable element
                    ->screenshot('keyboard_navigation');
        });
    }

    /**
     * Test color scheme and design system.
     */
    public function test_color_scheme_and_design()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText('Selamat Datang di', 5)
                    ->assertPresent('header.header') // Should have primary blue background
                    ->screenshot('color_scheme_design');
        });
    }

    /**
     * Test typography system.
     */
    public function test_typography_system()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitFor('h1', 5)
                    ->assertPresent('h1') // Should use Lora font
                    ->assertPresent('h2') // Should use Lora font
                    ->assertPresent('h3') // Should use Lora font
                    ->assertPresent('p') // Should use Inter font
                    ->screenshot('typography_system');
        });
    }
}