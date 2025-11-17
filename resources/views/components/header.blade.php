@props([
    'currentUser' => $currentUser,
    'onLogout' => $onLogout
])

@php
use App\Models\User;

<div class="sticky top-0 z-40 bg-[--primary-blue] shadow-md" role="banner">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Logo and Brand -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-[--primary-blue]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 2L2 7l10 5v10l-10-5z"/>
                            <path d="M12 22l10-5-10-5-10 5z" opacity="0.3"/>
                        </svg>
                    </div>
                    <div class="text-white">
                        <div class="text-xl font-bold">Ormawa</div>
                        <div class="text-xs text-gray-200">Universitas Negeri Padang</div>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center space-x-8" role="navigation" aria-label="Main navigation">
                @foreach([
                    ['name' => 'Beranda', 'route' => 'home', 'icon' => 'home'],
                    ['name' => 'Pengumuman', 'route' => 'announcements.index', 'icon' => 'announcement'],
                    ['name' => 'Berita', 'route' => 'news.index', 'icon' => 'news'],
                    ['name' => 'Ormawa', 'route' => 'organizations.index', 'icon' => 'organization'],
                    ['name' => 'Kalender', 'route' => 'calendar.index', 'icon' => 'calendar']
                ] as $navItem)
                    <a href="{{ route($navItem['route']) }}" 
                       class="text-white hover:text-[--accent-orange] px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center space-x-2"
                       x-data="{ tooltip: '{{ $navItem[\"name\"] }}' }"
                       x-tooltip="tooltip">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @switch($navItem['icon'])
                                @case('home')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 002-2H5a2 2 0 00-2-2V7a2 2 0 012-2h14a2 2 0 012 2v11a2 2 0 002-2z"/>
                                @case('announcement')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3.9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                @case('news')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2-2z"/>
                                @case('organization')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2a3 3 0 015.356-1.857M7 20v-2a3 3 0 015.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                @case('calendar')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m4 4h8M8 7v14m0-18h8m-9 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            @endswitch
                        </svg>
                        <span>{{ $navItem['name'] }}</span>
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-sm text-white bg-gray-900 rounded-lg shadow-lg opacity-0 pointer-events-none transition-opacity duration-300"
                             x-show="tooltip"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95">
                            {{ $navItem['name'] }}
                        </div>
                    </a>
                @endforeach
            </nav>

            <!-- Desktop Auth Section -->
            <div class="hidden lg:flex items-center space-x-4">
                @guest
                    <a href="{{ route('login') }}" 
                       class="bg-[--accent-orange] text-[--text-dark] font-bold py-2 px-4 rounded-md hover:bg-orange-600 transition-all duration-300 transform hover:scale-105 shadow-sm">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" 
                       class="text-white hover:text-[--accent-orange] px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        Daftar
                    </a>
                @else
                    <!-- User Menu Dropdown -->
                    <div class="relative" x-data="{ userMenuOpen: false }">
                        <button @click="userMenuOpen = !userMenuOpen" 
                                @keydown.escape="userMenuOpen = false"
                                class="flex items-center space-x-3 text-white hover:text-[--accent-orange] transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[--primary-blue] rounded-md p-2"
                                aria-label="User menu for {{ $currentUser->name }}"
                                aria-expanded="false"
                                :aria-expanded="userMenuOpen">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[--accent-orange] to-orange-600 flex items-center justify-center text-white font-bold shadow-md hover:shadow-lg transition-shadow duration-300"
                                 aria-hidden="true">
                                {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                            </div>
                            <div class="hidden xl:block text-left">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium">{{ $currentUser->name }}</span>
                                    <span class="text-xs text-gray-200">{{ $currentUser->email }}</span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-white transition-transform duration-200" 
                                 :class="userMenuOpen ? 'rotate-180' : ''" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7v11a2 2 0 002-2H5a2 2 0 002-2v11a2 2 0 002-2z"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="userMenuOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             @click.away="userMenuOpen = false"
                             class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-50"
                             role="menu"
                             aria-labelledby="user-menu-button"
                             x-cloak>
                            <div class="px-4 py-3 border-b border-gray-200">
                                <p class="text-sm font-semibold text-gray-900" id="user-name">
                                    {{ $currentUser->name }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1" id="user-email">
                                    {{ $currentUser->email }}
                                </p>
                            </div>
                            <div class="py-1" role="none">
                                <a href="{{ route('profile') }}" 
                                   class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:bg-gray-100"
                                   role="menuitem"
                                   tabindex="-1">
                                    <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Profile Saya
                                </a>
                            </div>
                            <div class="py-1" role="none">
                                <a href="{{ route('dashboard') }}" 
                                   class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:bg-gray-100"
                                   role="menuitem"
                                   tabindex="-1">
                                    <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"/>
                                    </svg>
                                    @php
                                        $dashboardLabel = match($currentUser->role) {
                                            'ADMIN' => 'Dashboard Admin',
                                            'KEMAHASISWAAN' => 'Dashboard Kemahasiswaan',
                                            'ORMAWA' => 'Dashboard Ormawa',
                                            default => 'Dashboard'
                                        };
                                    ?>
                                    {{ $dashboardLabel }}
                                </a>
                            </div>
                            <div class="py-1 border-t border-gray-200" role="none">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="flex items-center w-full px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:bg-gray-100"
                                            role="menuitem"
                                            tabindex="-1">
                                        <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4 4m0 0l-4 4M4 20h16M4 16h16m-4-4h.01M9 16h.01M4 16h.01"/>
                                        </svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endguest
            </div>

            <!-- Mobile Menu Button -->
            <div class="lg:hidden flex items-center" x-data="{ mobileMenuOpen: false }">
                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        @keydown.escape="mobileMenuOpen = false"
                        class="text-white hover:text-[--accent-orange] focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[--primary-blue] rounded-md p-2"
                        aria-label="Toggle mobile navigation menu"
                        aria-expanded="false"
                        :aria-expanded="mobileMenuOpen">
                    <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>