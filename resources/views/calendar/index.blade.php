@extends('layouts.app')

@section('title', 'Kalender Kegiatan - Organisasi Mahasiswa UNP')

@section('content')
<div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in" x-data="calendarApp()">
    <div class="flex items-center mb-8">
        <svg class="h-10 w-10 text-[--primary-blue] mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <div>
            <h1 class="text-4xl font-bold font-lora text-[--primary-blue]">
                Kalender Kegiatan
            </h1>
            <p class="text-lg text-[--text-secondary]">
                Semua jadwal kegiatan Ormawa dalam satu tempat.
            </p>
        </div>
    </div>

    <div class="bg-white p-3 rounded-t-lg shadow-lg border-b border-gray-200 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2 flex-wrap">
            <button @click="goToToday()" class="px-4 py-2 text-sm font-semibold border border-gray-300 rounded-md hover:bg-gray-100">
                Hari Ini
            </button>
            <button @click="previousPeriod()" class="p-2 border rounded-md hover:bg-gray-100">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="nextPeriod()" class="p-2 border rounded-md hover:bg-gray-100">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            <h2 class="text-lg font-bold text-gray-700 ml-2" x-text="currentPeriodTitle"></h2>
            <select x-model="currentYear" @change="changeYear($event.target.value)" class="text-lg font-bold text-gray-700 p-1 border-gray-300 rounded-md bg-white focus:ring-1 focus:ring-[--primary-blue]">
                <!-- Years will be populated by JS -->
            </select>
        </div>
        <div class="flex items-center gap-4">
            <select x-model="selectedOrg" @change="loadEvents()" class="text-sm border border-gray-300 rounded-md p-2 bg-white">
                <option value="all">Semua Organisasi</option>
                <option value="pusat">Kemahasiswaan Pusat</option>
                @foreach($organizations as $org)
                    <option value="{{ $org->id }}">{{ $org->acronym }}</option>
                @endforeach
            </select>
        </div>
    </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('activities.index') }}" 
               class="text-gray-600 hover:text-gray-800 px-3 py-2 rounded-md border border-gray-300 hover:bg-gray-50 transition-colors">
                Activities List
            </a>
            <a href="{{ route('announcements.index') }}" 
               class="text-gray-600 hover:text-gray-800 px-3 py-2 rounded-md border border-gray-300 hover:bg-gray-50 transition-colors">
                Announcements List
            </a>
            @if(auth()->check())
                <a href="{{ route('activities.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                    Create Activity
                </a>
                @if(auth()->user()->is_admin)
                    <a href="{{ route('announcements.create') }}" 
                       class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        Create Announcement
                    </a>
                @endif
            @endif
        </div>
    </div>

    <!-- Calendar Controls -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
            <!-- View Toggle -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">View</label>
                <div class="flex rounded-md shadow-sm">
                    <button @click="currentView = 'month'" 
                            :class="currentView === 'month' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border-gray-300'"
                            class="px-3 py-2 text-sm font-medium rounded-l-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Month
                    </button>
                    <button @click="currentView = 'week'" 
                            :class="currentView === 'week' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border-gray-300'"
                            class="px-3 py-2 text-sm font-medium border-t border-b border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Week
                    </button>
                    <button @click="currentView = 'day'" 
                            :class="currentView === 'day' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border-gray-300'"
                            class="px-3 py-2 text-sm font-medium rounded-r-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Day
                    </button>
                </div>
            </div>

            <!-- Organization Filter -->
            <div>
                <label for="org-filter" class="block text-sm font-medium text-gray-700 mb-2">Organization</label>
                <select id="org-filter" x-model="filters.organization_id" 
                        @change="loadEvents()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Organizations</option>
                    @foreach($organizations as $org)
                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Event Type Filter -->
            <div>
                <label for="event-type-filter" class="block text-sm font-medium text-gray-700 mb-2">Event Type</label>
                <select id="event-type-filter" x-model="filters.event_type" 
                        @change="loadEvents()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Events</option>
                    <option value="activity">Activities Only</option>
                    <option value="announcement">Announcements Only</option>
                </select>
            </div>

            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <div class="relative">
                    <input type="text" id="search" x-model="filters.search" 
                           @keyup.debounce.300ms="loadEvents()"
                           placeholder="Search events..." 
                           class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Controls -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <button @click="previousPeriod()" 
                        class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button @click="nextPeriod()" 
                        class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <button @click="goToToday()" 
                        class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-colors">
                    Today
                </button>
            </div>
            
            <h2 class="text-xl font-semibold text-gray-900" x-text="currentPeriodTitle"></h2>
            
            <div class="text-sm text-gray-500">
                <span x-show="loading">Loading...</span>
                <span x-show="!loading" x-text="eventCount + ' events'"></span>
            </div>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <!-- Legend -->
        <div class="flex items-center justify-center space-x-6 mb-6 pb-4 border-b border-gray-200">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                <span class="text-sm text-gray-700">Activities</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                <span class="text-sm text-gray-700">Announcements</span>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div id="calendar-container" class="min-h-96">
            <!-- Calendar will be rendered here by JavaScript -->
            <div class="flex items-center justify-center h-96 text-gray-500">
                <div class="text-center">
                    <svg class="animate-spin h-8 w-8 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <p>Loading calendar...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Upcoming Events</h2>
        <div id="upcoming-events" class="space-y-4">
            <!-- Upcoming events will be loaded here -->
            <div class="text-center py-8 text-gray-500">
                <p>Loading upcoming events...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Include FullCalendar CSS
document.head.insertAdjacentHTML('beforeend', '<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">');

// Include FullCalendar JS
const script = document.createElement('script');
script.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js';
document.head.appendChild(script);

script.onload = function() {
    function calendarApp() {
        return {
            currentView: 'month',
            currentDate: new Date(),
            events: [],
            loading: false,
            calendar: null,
            filters: {
                organization_id: '',
                event_type: 'all',
                search: ''
            },
            
            init() {
                this.$nextTick(() => {
                    this.initCalendar();
                    this.loadEvents();
                });
            },
            
            get currentPeriodTitle() {
                const options = { year: 'numeric', month: 'long' };
                if (this.currentView === 'day') {
                    options.day = 'numeric';
                } else if (this.currentView === 'week') {
                    // For week view, we'll show the week range
                    const weekStart = new Date(this.currentDate);
                    weekStart.setDate(weekStart.getDate() - weekStart.getDay());
                    const weekEnd = new Date(weekStart);
                    weekEnd.setDate(weekEnd.getDate() + 6);
                    return `${weekStart.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${weekEnd.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
                }
                return this.currentDate.toLocaleDateString('en-US', options);
            },
            
            get eventCount() {
                return this.events.length;
            },
            
            initCalendar() {
                const calendarEl = document.getElementById('calendar-container');
                
                this.calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: this.currentView === 'week' ? 'timeGridWeek' : 
                                   this.currentView === 'day' ? 'timeGridDay' : 'dayGridMonth',
                    initialDate: this.currentDate,
                    headerToolbar: false, // We use our own navigation
                    events: [],
                    eventClick: (info) => {
                        if (info.event.url) {
                            window.open(info.event.url, '_blank');
                        }
                    },
                    eventDidMount: (info) => {
                        // Add tooltips or custom styling
                        if (info.event.extendedProps.type === 'announcement') {
                            info.el.classList.add('announcement-event');
                        }
                    },
                    datesSet: (dateInfo) => {
                        this.currentDate = dateInfo.start;
                    }
                });
                
                this.calendar.render();
            },
            
            async loadEvents() {
                this.loading = true;
                
                try {
                    const startDate = this.calendar.view.activeStart;
                    const endDate = this.calendar.view.activeEnd;
                    
                    const params = new URLSearchParams({
                        start: startDate.toISOString().split('T')[0],
                        end: endDate.toISOString().split('T')[0],
                        ...this.filters
                    });
                    
                    const response = await fetch(`{{ route('calendar.data') }}?${params}`);
                    const events = await response.json();
                    
                    this.events = events;
                    this.calendar.removeAllEvents();
                    this.calendar.addEventSource(events);
                    
                    this.loadUpcomingEvents();
                } catch (error) {
                    console.error('Error loading events:', error);
                } finally {
                    this.loading = false;
                }
            },
            
            async loadUpcomingEvents() {
                try {
                    const upcomingEl = document.getElementById('upcoming-events');
                    const upcoming = this.events
                        .filter(event => new Date(event.start) > new Date())
                        .sort((a, b) => new Date(a.start) - new Date(b.start))
                        .slice(0, 5);
                    
                    if (upcoming.length === 0) {
                        upcomingEl.innerHTML = `
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No upcoming events</h3>
                                <p class="mt-1 text-sm text-gray-500">Check back later for new events.</p>
                            </div>
                        `;
                        return;
                    }
                    
                    upcomingEl.innerHTML = upcoming.map(event => `
                        <div class="flex items-start space-x-4 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer" onclick="window.open('${event.extendedProps.url}', '_blank')">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 ${event.color === '#3B82F6' ? 'bg-blue-100' : 'bg-green-100'} rounded-lg flex items-center justify-center">
                                    <span class="${event.color === '#3B82F6' ? 'text-blue-600' : 'text-green-600'} font-bold">
                                        ${new Date(event.start).getDate()}
                                    </span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">${event.title}</h3>
                                <p class="text-sm text-gray-600">
                                    ${event.extendedProps.organization || event.extendedProps.category || 'General'}
                                    ${event.extendedProps.creator ? ' • ' + event.extendedProps.creator : ''}
                                </p>
                                <div class="flex items-center mt-1 text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    ${new Date(event.start).toLocaleString()}
                                </div>
                                ${event.extendedProps.location ? `
                                    <div class="flex items-center mt-1 text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        ${event.extendedProps.location}
                                    </div>
                                ` : ''}
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${event.color === '#3B82F6' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}">
                                    ${event.type}
                                </span>
                            </div>
                        </div>
                    `).join('');
                    
                } catch (error) {
                    console.error('Error loading upcoming events:', error);
                }
            },
            
            previousPeriod() {
                this.calendar.prev();
                this.loadEvents();
            },
            
            nextPeriod() {
                this.calendar.next();
                this.loadEvents();
            },
            
            goToToday() {
                this.calendar.today();
                this.loadEvents();
            },
            
            changeView(view) {
                this.currentView = view;
                this.calendar.changeView(view === 'week' ? 'timeGridWeek' : 
                                         view === 'day' ? 'timeGridDay' : 'dayGridMonth');
                this.loadEvents();
            }
        };
    }
    
    // Initialize the Alpine.js component
    if (typeof Alpine !== 'undefined') {
        Alpine.data('calendarApp', calendarApp);
    }
};
</script>

<style>
.announcement-event {
    border-left: 4px solid #10B981 !important;
}

.fc-event-title {
    font-weight: 500;
}

@media (max-width: 768px) {
    .fc-toolbar {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .fc-header-toolbar {
        flex-wrap: wrap;
    }
    
    .fc-button-group {
        flex-wrap: wrap;
    }
}
</style>
@endsection