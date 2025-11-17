<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Organisasi Mahasiswa - Universitas Negeri Padang')</title>
    <meta name="description" content="Portal Organisasi Mahasiswa Universitas Negeri Padang - Informasi kegiatan, pengumuman, dan berita ormawa">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('build/assets/app-COXlLQgO.css') }}">
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-blue': '#0D47A1',
                        'accent-orange': '#FF9800',
                        'text-dark': '#212121',
                        'text-secondary': '#616161',
                        'bg-main': '#FAFAFA',
                        'border-color': '#EEEEEE',
                        'success-green': '#4CAF50',
                        'error-red': '#D32F2F'
                    },
                    fontFamily: {
                        'lora': ['Lora', 'serif'],
                        'kalam': ['Kalam', 'cursive']
                    }
                }
            }
        }
    </script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:wght@400;500;600;700&family=Kalam:wght@400;700&display=swap" rel="stylesheet">
</head>
    <body class="bg-[--bg-main] text-[--text-dark] homepage-active">
    <!-- Skip to main content link for screen readers -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-[--primary-blue] text-white px-4 py-2 rounded-md z-50 focus:outline-none focus:ring-2 focus:ring-white">
        Skip to main content
    </a>

<!-- Header -->
      <header class="sticky top-0 z-40 bg-[--primary-blue] shadow-md" role="banner">
         <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
             <div class="flex items-center justify-between h-20">
                 <a href="{{ route('home') }}" class="flex items-center space-x-4">
                      <div class="text-white">
                          <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                              <path d="M12 2L1 9l3 2.25V21h14v-9.75L21 9l-9-7zm-2 15V13h4v4h-4zm6-8.75L12 5.5 8 8.25V11h8V8.25z" />
                          </svg>
                      </div>
                     <div>
                         <h1 class="text-xl md:text-2xl font-bold font-lora text-white">
                             Organisasi Mahasiswa
                         </h1>
                         <p class="text-sm text-gray-200 hidden sm:block">
                             Universitas Negeri Padang
                         </p>
                     </div>
                 </a>

                 <!-- Desktop Navigation -->
                 <nav class="hidden lg:flex items-center space-x-8" role="navigation" aria-label="Main navigation">
                     <a href="{{ route('home') }}" class="font-medium text-white hover:text-[--accent-orange] transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[--primary-blue] rounded-md px-2 py-1">
                         Beranda
                     </a>
                     <a href="{{ route('announcements.index') }}" class="font-medium text-white hover:text-[--accent-orange] transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[--primary-blue] rounded-md px-2 py-1">
                         Pengumuman
                     </a>
                     <a href="{{ route('news.index') }}" class="font-medium text-white hover:text-[--accent-orange] transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[--primary-blue] rounded-md px-2 py-1">
                         Berita
                     </a>
                     <a href="{{ route('organizations.index') }}" class="font-medium text-white hover:text-[--accent-orange] transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[--primary-blue] rounded-md px-2 py-1">
                         Ormawa
                     </a>
                     <a href="{{ route('calendar.index') }}" class="font-medium text-white hover:text-[--accent-orange] transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[--primary-blue] rounded-md px-2 py-1">
                         Kalender
                     </a>
                 </nav>

                 <!-- Desktop Auth Buttons -->
                 <div class="hidden lg:flex">
                     @guest
                         <a href="{{ route('login') }}" class="bg-[--accent-orange] text-[--text-dark] font-bold py-2 px-5 rounded-md hover:bg-orange-500 transition-all duration-300 transform hover:scale-105 shadow-sm">
                             Log In
                         </a>
                     @else
                         <div class="relative" x-data="{ open: false }">
                             <button @click="open = !open" @keydown.escape="open = false" class="flex items-center space-x-2 hover:opacity-80 transition-opacity focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[--primary-blue] rounded-md p-2" aria-label="User menu for {{ Auth::user()->name }}" aria-expanded="false" :aria-expanded="open" aria-haspopup="true">
                                 <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[--accent-orange] to-orange-600 flex items-center justify-center text-white font-bold shadow-md hover:shadow-lg transition-shadow" aria-hidden="true">
                                     {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                 </div>
                                 <span class="text-white font-medium hidden xl:block">
                                     {{ Auth::user()->name }}
                                 </span>
                                 <svg class="w-4 h-4 text-white transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                 </svg>
                             </button>

                             <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" @click.away="open = false" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-50" role="menu" aria-labelledby="user-menu-button" x-cloak>
                                 <div class="px-4 py-3 border-b border-gray-200" role="none">
                                     <p class="text-sm font-semibold text-gray-900" id="user-name">
                                         {{ Auth::user()->name }}
                                     </p>
                                     <p class="text-xs text-gray-500 mt-1" id="user-email">
                                         {{ Auth::user()->email }}
                                     </p>
                                 </div>

                                 <div class="py-1" role="none">
                                     <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:bg-gray-100" role="menuitem" tabindex="-1">
                                         <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                         </svg>
                                         Profile Saya
                                     </a>

                                     @if(Auth::user()->role !== 'user')
                                         <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:bg-gray-100" role="menuitem" tabindex="-1">
                                             <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"/>
                                             </svg>
                                             {{ __('Dashboard') }}
                                         </a>
                                     @endif
                                 </div>

                                 <div class="border-t border-gray-200 py-1" role="none">
                                     <form method="POST" action="{{ route('logout') }}">
                                         @csrf
                                         <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors focus:outline-none focus:bg-red-50" role="menuitem" tabindex="-1">
                                             <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                             </svg>
                                             Logout
                                         </button>
                                     </form>
                                 </div>
                             </div>
                         </div>
                     @endguest
                 </div>

                 <!-- Mobile Menu Button -->
                 <div class="lg:hidden flex items-center" x-data="{ mobileMenuOpen: false }">
                     <button @click="mobileMenuOpen = !mobileMenuOpen" @keydown.escape="mobileMenuOpen = false" class="text-white hover:text-[--accent-orange] focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[--primary-blue] rounded-md p-2" aria-label="Toggle mobile navigation menu" aria-expanded="false" :aria-expanded="mobileMenuOpen" aria-controls="mobile-menu" id="mobileMenuToggle">
                         <svg x-show="!mobileMenuOpen" class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                         </svg>
                         <svg x-show="mobileMenuOpen" class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                         </svg>
                     </button>
                 </div>
             </div>
         </div>

         <!-- Mobile Menu -->
         <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" x-cloak class="lg:hidden bg-white shadow-lg" id="mobile-menu">
             <nav class="px-2 pt-2 pb-3 space-y-1 sm:px-3" role="navigation" aria-label="Mobile navigation">
                 <a href="{{ route('home') }}" class="text-[--text-secondary] hover:bg-gray-100 hover:text-[--text-dark] block px-3 py-2 rounded-md text-base font-medium focus:outline-none focus:bg-gray-100 focus:text-[--text-dark]">
                     Beranda
                 </a>
                 <a href="{{ route('announcements.index') }}" class="text-[--text-secondary] hover:bg-gray-100 hover:text-[--text-dark] block px-3 py-2 rounded-md text-base font-medium focus:outline-none focus:bg-gray-100 focus:text-[--text-dark]">
                     Pengumuman
                 </a>
                 <a href="{{ route('news.index') }}" class="text-[--text-secondary] hover:bg-gray-100 hover:text-[--text-dark] block px-3 py-2 rounded-md text-base font-medium focus:outline-none focus:bg-gray-100 focus:text-[--text-dark]">
                     Berita
                 </a>
                 <a href="{{ route('organizations.index') }}" class="text-[--text-secondary] hover:bg-gray-100 hover:text-[--text-dark] block px-3 py-2 rounded-md text-base font-medium focus:outline-none focus:bg-gray-100 focus:text-[--text-dark]">
                     Ormawa
                 </a>
                 <a href="{{ route('calendar.index') }}" class="text-[--text-secondary] hover:bg-gray-100 hover:text-[--text-dark] block px-3 py-2 rounded-md text-base font-medium focus:outline-none focus:bg-gray-100 focus:text-[--text-dark]">
                     Kalender
                 </a>
             </nav>
             
             @guest
                 <div class="px-4 py-4">
                     <a href="{{ route('login') }}" class="w-full block text-center bg-[--accent-orange] text-[--text-dark] font-bold py-2.5 px-5 rounded-md hover:bg-orange-500 transition-all duration-300">
                         Log In
                     </a>
                 </div>
             @else
                 <div class="border-t border-gray-200">
                     <div class="px-4 py-3">
                         <div class="flex items-center space-x-3 mb-3">
                             <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[--accent-orange] to-orange-600 flex items-center justify-center text-white font-bold text-sm">
                                 {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                             </div>
                             <div>
                                 <p class="text-sm font-semibold text-gray-900">
                                     {{ Auth::user()->name }}
                                 </p>
                                 <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                             </div>
                         </div>

                         <a href="{{ route('profile') }}" class="flex items-center w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md transition-colors mb-1">
                             <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                             </svg>
                             Profile Saya
                         </a>

                         @if(Auth::user()->role !== 'user')
                             <a href="{{ route('dashboard') }}" class="flex items-center w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md transition-colors mb-1">
                                 <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"/>
                                 </svg>
                                 {{ __('Dashboard') }}
                             </a>
                         @endif

                         <form method="POST" action="{{ route('logout') }}">
                             @csrf
                             <button type="submit" class="flex items-center w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md transition-colors mt-2">
                                 <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                 </svg>
                                 Logout
                             </button>
                         </form>
                     </div>
                 </div>
             @endguest
          </div>
      </header>

    <!-- Main Content -->
    <main id="main-content" class="flex-grow relative" role="main">
        @yield('content')
    </main>

    <!-- Footer - hidden on home page -->
    @if(request()->route()->getName() !== 'home')
        <footer class="bg-[--primary-blue] text-gray-300 mt-auto">
            <div class="max-w-screen-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center text-sm">
                <p>&copy; {{ date('Y') }} Universitas Negeri Padang. Seluruh hak cipta dilindungi.</p>
                <p class="mt-2 text-gray-400 text-xs">
                    Dibangun oleh Unit Kegiatan Infinite Technology Universitas Negeri Padang (UK Infitech UNP).
                </p>
            </div>
        </footer>
    @endif

    <!-- Global Components -->
    <!-- Cookie Consent -->
    <div id="cookie-consent" class="fixed bottom-0 left-0 right-0 w-full bg-white p-4 shadow-2xl border-t border-[--border-color] z-50 animate-slide-up" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="cookie-consent-title" aria-describedby="cookie-consent-description">
        <div class="container mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-center sm:text-left">
                <h3 id="cookie-consent-title" class="font-bold font-lora text-[--primary-blue]">Persetujuan Cookie</h3>
                <p id="cookie-consent-description" class="text-sm text-[--text-secondary] mt-1 max-w-2xl">
                    Kami menggunakan cookie untuk menganalisis lalu lintas dan meningkatkan pengalaman Anda. Apakah Anda menyetujui penggunaan cookie ini?
                </p>
            </div>
            <div class="flex-shrink-0 flex gap-3">
                <button id="decline-cookies" class="bg-gray-200 text-gray-800 font-bold text-sm py-2 px-5 rounded-md hover:bg-gray-300 transition-all duration-300" aria-label="Tolak penggunaan cookie">
                    Tolak
                </button>
                <button id="accept-cookies" class="bg-[--accent-orange] text-[--text-dark] font-bold text-sm py-2 px-5 rounded-md hover:bg-orange-500 transition-all duration-300" aria-label="Terima penggunaan cookie">
                    Terima
                </button>
            </div>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <button id="scroll-to-top" class="fixed bottom-6 right-6 bg-[--primary-blue] text-white p-3 rounded-full shadow-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[--primary-blue] transition-all duration-300 z-30 animate-fade-in" style="display: none;" aria-label="Scroll to top">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
    </button>

    <!-- CKEditor (loaded only when needed) -->
    @if(request()->routeIs('announcements.create') || request()->routeIs('announcements.edit'))
        <script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
    @endif
    
    <!-- Alpine.js for reactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="{{ asset('build/assets/app-DBpKQrAd.js') }}"></script>

    <style>
        :root {
            /* "Modern Academia" Color Palette */
            --primary-blue: #0D47A1;
            --accent-orange: #FF9800;
            --text-dark: #212121;
            --text-secondary: #616161;
            --bg-main: #FAFAFA;
            --border-color: #EEEEEE;
            --success-green: #4CAF50;
            --error-red: #D32F2F;
        }
        html, body {
            height: 100%;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-dark);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            height: 100vh;
            overflow-y: auto;
        }
        /* Prevent body scroll ONLY on homepage */
        body.homepage-active {
            overflow: hidden;
        }
        .font-lora {
            font-family: 'Lora', serif;
        }
        .font-kalam {
            font-family: 'Kalam', cursive;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="url"], textarea, select {
            color: #374151;
            background-color: #ffffff;
        }
        .animate-fade-in { animation: fadeIn 0.5s ease-in-out; }
        .animate-fade-out { animation: fadeOut 0.5s ease-in-out forwards; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }

        @keyframes bounce { 0%, 20%, 50%, 80%, 100% { transform: translateY(0); } 40% { transform: translateY(-10px); } 60% { transform: translateY(-5px); } }
        .animate-bounce { animation: bounce 2s infinite; }

        /* Scroll Snap Container */
        .scroll-snap-container {
            scroll-snap-type: y mandatory;
            overflow-y: scroll;
            position: absolute;
            inset: 0;
        }

        /* Scroll Snap Sections */
        .scroll-snap-section {
            scroll-snap-align: start;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem 1rem;
            overflow: hidden;
            position: relative;
        }

        /* Allow content inside sections to scroll if needed, especially on mobile */
        .scroll-snap-section > div {
            height: 100%;
            width: 100%;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .scroll-snap-section.p-0 > div {
            padding: 0;
        }

        /* Scroll animation */
        .scroll-animate {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Infinite Carousel Slider */
        .slider {
            width: 100%;
            max-width: 90vw;
            overflow: hidden;
            position: relative;
            height: 450px; /* FIX: Enforce a fixed height for uniform cards */
            -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
            mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
        }

        @keyframes infinite-scroll {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }

        .slider-track {
            display: flex;
            animation: infinite-scroll 60s linear infinite;
            will-change: transform;
            height: 100%; /* Ensure track fills the slider height */
        }

        .slider-track:hover {
            animation-play-state: paused;
        }

        .slide {
            flex-shrink: 0;
            width: 300px; /* Adjust card width */
            margin: 0 1rem;
            height: 100%;
        }
        /* Ensure the card component inside the slide div takes full height */
        .slide > div {
            height: 100%;
        }

        /* Mobile Carousel: Touch-friendly scroll */
        @media (max-width: 767px) {
            h1 { font-size: 2.25rem; line-height: 2.5rem; } /* 36px */
            h2 { font-size: 1.5rem; line-height: 2rem; }   /* 24px */
            h3 { font-size: 1.25rem; line-height: 1.75rem; } /* 20px */

            .slider {
                overflow-x: auto;
                scroll-snap-type: x mandatory;
                -webkit-mask-image: none;
                mask-image: none;
                max-width: 100%; /* Take full container width */
                height: 420px; /* Adjust height for mobile */
            }

            .slider::-webkit-scrollbar {
                display: none; /* Hide scrollbar for a cleaner look */
            }
            .slider {
                -ms-overflow-style: none;  /* IE and Edge */
                scrollbar-width: none;  /* Firefox */
            }

            .slider-track {
                animation: none; /* Disable the infinite scroll animation on mobile */
            }

            .slide {
                width: 80vw;
                scroll-snap-align: center;
                margin: 0 10px; /* Adjust spacing between cards */
            }
        }

        /* Tablet Typography */
        @media (min-width: 768px) and (max-width: 1023px) {
            h1 { font-size: 2.75rem; line-height: 3rem; } /* 44px */
            h2 { font-size: 1.875rem; line-height: 2.25rem; }   /* 30px */
        }

        /* Progress Bar Animation for News Slider */
        @keyframes fill-progress {
            from { width: 0%; }
            to { width: 100%; }
        }
        .animate-fill-progress {
            animation: fill-progress 5s linear forwards;
        }

        /* Cookie Consent Animation */
        @keyframes slideUp { from { opacity: 0; transform: translateY(100px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slide-up { animation: slideUp 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards; }

        /* Mading / Bulletin Board Styles */
        .cork-board-bg {
            background-color: #d2b48c; /* Tan color */
            background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.1"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
            padding: 2rem;
            border-radius: 0.5rem;
            border: 10px solid #8B4513; /* SaddleBrown for frame */
            box-shadow: inset 0 0 15px rgba(0,0,0,0.5);
        }
        .info-post-card {
            position: relative;
            padding: 1.5rem;
            box-shadow: 5px 5px 10px rgba(0,0,0,0.3);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .info-post-card:hover {
            transform: scale(1.05) !important;
            z-index: 10;
            box-shadow: 10px 10px 20px rgba(0,0,0,0.4);
        }
        /* Pushpin */
        .info-post-card::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: radial-gradient(circle, #ff5252 40%, #c62828 100%);
            box-shadow: 2px 2px 5px rgba(0,0,0,0.5);
            border: 2px solid #fff;
        }

        /* Different paper colors and rotations */
        .paper-yellow { background-color: #fffacd; } /* LemonChiffon */
        .paper-white { background-color: #f8f8f8; }
        .paper-blue { background-color: #add8e6; } /* LightBlue */
        .rotate-1 { transform: rotate(1deg); }
        .rotate-neg-1 { transform: rotate(-1deg); }
        .rotate-2 { transform: rotate(2deg); }
        .rotate-neg-2 { transform: rotate(-2deg); }

        /* Calendar Logo Styles */
        .calendar-logo-container {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            padding-left: 2px;
            margin-top: 4px;
        }
        .calendar-logo {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #eee;
            background-color: white;
        }

        /* Copy Toast Animation */
        @keyframes toast-in-out {
            0% { opacity: 0; transform: translate(-50%, 20px); }
            15% { opacity: 1; transform: translate(-50%, 0); }
            85% { opacity: 1; transform: translate(-50%, 0); }
            100% { opacity: 0; transform: translate(-50%, 20px); }
        }
        .animate-toast {
            animation: toast-in-out 3s ease-in-out forwards;
        }

        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
        }
    </style>

    <!-- Global Components JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cookie Consent
            const cookieConsent = document.getElementById('cookie-consent');
            const acceptCookies = document.getElementById('accept-cookies');
            const declineCookies = document.getElementById('decline-cookies');

            // Check if user has already consented
            const consent = localStorage.getItem('cookieConsent');
            if (!consent) {
                // Show the banner after a short delay to not be too intrusive on load
                setTimeout(() => {
                    cookieConsent.style.display = 'block';
                }, 1500);
            }

            if (acceptCookies) {
                acceptCookies.addEventListener('click', function() {
                    localStorage.setItem('cookieConsent', 'accepted');
                    cookieConsent.style.display = 'none';
                });
            }

            if (declineCookies) {
                declineCookies.addEventListener('click', function() {
                    localStorage.setItem('cookieConsent', 'declined');
                    cookieConsent.style.display = 'none';
                });
            }

            // Scroll to Top Button
            const scrollToTopBtn = document.getElementById('scroll-to-top');
            const isHomePage = window.location.pathname === '/';

            function toggleScrollToTop() {
                const scrollTarget = isHomePage
                    ? document.querySelector('.scroll-snap-container')
                    : window;

                if (!scrollTarget) return;

                const scrollTop = isHomePage
                    ? scrollTarget.scrollTop
                    : window.scrollY;

                if (scrollTop > 300) {
                    scrollToTopBtn.style.display = 'block';
                } else {
                    scrollToTopBtn.style.display = 'none';
                }
            }

            function scrollToTop() {
                const scrollTarget = isHomePage
                    ? document.querySelector('.scroll-snap-container')
                    : window;

                if (scrollTarget) {
                    scrollTarget.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            }

            // Use the appropriate scroll target for listening
            const scrollTarget = isHomePage
                ? document.querySelector('.scroll-snap-container')
                : window;

            if (scrollTarget) {
                scrollTarget.addEventListener('scroll', toggleScrollToTop, { passive: true });
                toggleScrollToTop(); // Initial check
            }

            scrollToTopBtn.addEventListener('click', scrollToTop);

            // Scroll Animation for Cards
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.classList.add('is-visible');
                        }, index * 50); // Stagger animation
                    }
                });
            }, observerOptions);

            // Observe all scroll-animate elements
            document.querySelectorAll('.scroll-animate').forEach((element) => {
                observer.observe(element);
            });
        });
    </script>
</body>
</html>