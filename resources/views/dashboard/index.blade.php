@extends('layouts.app')

@section('title', 'Dashboard Admin - Organisasi Mahasiswa')

@section('content')
<div class="animate-fade-in">
    <div class="max-w-screen-2xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold font-lora text-gray-800 mb-2">
            Selamat Datang, Admin!
        </h1>
        <p class="text-gray-600 mb-8">
            Pilih menu di bawah untuk mulai mengelola Portal Ormawa Universitas.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="#" class="bg-white p-6 rounded-lg shadow-md border border-gray-200 text-left hover:shadow-xl hover:-translate-y-1 transition-all duration-300 w-full">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-[--primary-blue]/10 mr-4">
                        <svg class="h-8 w-8 text-[--primary-blue]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold font-lora text-gray-800">Manajemen Ormawa</h3>
                </div>
                <p class="text-gray-600">Tambah, edit, hapus, dan kelola semua data organisasi mahasiswa.</p>
            </a>

            <a href="#" class="bg-white p-6 rounded-lg shadow-md border border-gray-200 text-left hover:shadow-xl hover:-translate-y-1 transition-all duration-300 w-full">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-[--primary-blue]/10 mr-4">
                        <svg class="h-8 w-8 text-[--primary-blue]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold font-lora text-gray-800">Laporan & Statistik</h3>
                </div>
                <p class="text-gray-600">Lihat statistik, demografi, dan laporan aktivitas dari seluruh ormawa.</p>
            </a>

            <a href="#" class="bg-white p-6 rounded-lg shadow-md border border-gray-200 text-left hover:shadow-xl hover:-translate-y-1 transition-all duration-300 w-full">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-[--primary-blue]/10 mr-4">
                        <svg class="h-8 w-8 text-[--primary-blue]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold font-lora text-gray-800">Pengawasan Aktivitas</h3>
                </div>
                <p class="text-gray-600">Lacak log perubahan dan aktivitas yang dilakukan oleh admin ormawa.</p>
            </a>

            <a href="#" class="bg-white p-6 rounded-lg shadow-md border border-gray-200 text-left hover:shadow-xl hover:-translate-y-1 transition-all duration-300 w-full">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-[--primary-blue]/10 mr-4">
                        <svg class="h-8 w-8 text-[--primary-blue]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold font-lora text-gray-800">Manajemen Data Master</h3>
                </div>
                <p class="text-gray-600">Kelola data referensi seperti daftar fakultas dan kategori organisasi.</p>
            </a>
        </div>
    </div>
</div>
@endsection

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @try
            <div class="bg-white rounded-lg shadow p-6" role="region" aria-labelledby="stats-organizations">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600" id="stats-organizations">Total Organisasi</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ App\Models\Organization::count() ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6" role="region" aria-labelledby="stats-activities">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600" id="stats-activities">Total Kegiatan</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ App\Models\Activity::count() ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6" role="region" aria-labelledby="stats-announcements">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600" id="stats-announcements">Pengumuman</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ App\Models\Announcement::count() ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6" role="region" aria-labelledby="stats-news">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600" id="stats-news">Berita</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ App\Models\News::count() ?? 0 }}</p>
                    </div>
                </div>
            </div>
            @catch(Exception $e)
            <div class="col-span-full bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-yellow-800">Statistik tidak tersedia saat ini. Silakan coba lagi nanti.</p>
            </div>
            @endtry
        </div>

        <!-- Quick Actions -->
        @if(Auth::user()->role !== 'user')
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Aksi Cepat</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kemahasiswaan')
                        <a href="{{ route('organizations.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Organisasi
                        </a>
                    @endif

                    @if(Auth::user()->role !== 'user')
                        <a href="{{ route('activities.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Kegiatan
                        </a>
                    @endif

                    @if(Auth::user()->role !== 'user')
                        <a href="{{ route('announcements.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Pengumuman
                        </a>
                    @endif

                    @if(Auth::user()->role !== 'user')
                        <a href="{{ route('news.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Berita
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Activities -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Activities -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Kegiatan Terbaru</h2>
                </div>
                <div class="p-6">
                    @try
                        @php
                            $recentActivities = App\Models\Activity::latest()->take(5)->get();
                        @endphp
                        @if($recentActivities->count() > 0)
                            <div class="space-y-4" role="list">
                                @foreach($recentActivities as $activity)
                                    <div class="flex items-start space-x-3" role="listitem">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">{{ $activity->title ?? 'Kegiatan tanpa judul' }}</p>
                                            <p class="text-sm text-gray-500">{{ $activity->organization->name ?? 'Tidak ada organisasi' }}</p>
                                            <p class="text-xs text-gray-400">{{ $activity->created_at?->format('d M Y') ?? 'Tanggal tidak tersedia' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4" role="status">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Belum ada kegiatan</p>
                            </div>
                        @endif
                    @catch(Exception $e)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4" role="alert">
                            <p class="text-sm text-red-800">Tidak dapat memuat kegiatan terbaru. Silakan coba lagi nanti.</p>
                        </div>
                    @endtry
                </div>
            </div>

            <!-- Recent Announcements -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Pengumuman Terbaru</h2>
                </div>
                <div class="p-6">
                    @try
                        @php
                            $recentAnnouncements = App\Models\Announcement::latest()->take(5)->get();
                        @endphp
                        @if($recentAnnouncements->count() > 0)
                            <div class="space-y-4" role="list">
                                @foreach($recentAnnouncements as $announcement)
                                    <div class="flex items-start space-x-3" role="listitem">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">{{ $announcement->title ?? 'Pengumuman tanpa judul' }}</p>
                                            <p class="text-sm text-gray-500 truncate">{{ Str::limit($announcement->content ?? '', 50) }}</p>
                                            <p class="text-xs text-gray-400">{{ $announcement->created_at?->format('d M Y') ?? 'Tanggal tidak tersedia' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4" role="status">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Belum ada pengumuman</p>
                            </div>
                        @endif
                    @catch(Exception $e)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4" role="alert">
                            <p class="text-sm text-red-800">Tidak dapat memuat pengumuman terbaru. Silakan coba lagi nanti.</p>
                        </div>
                    @endtry
                </div>
            </div>
        </div>

        <!-- Organization Overview for org_admin users -->
        @if(Auth::user()->role === 'ormawa' && Auth::user()->organization)
        @try
        <div class="mt-8 bg-white rounded-lg shadow" role="region" aria-labelledby="org-overview">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900" id="org-overview">Organisasi Saya</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Nama Organisasi</p>
                        <p class="text-lg font-semibold text-gray-900">{{ Auth::user()->organization->name ?? 'Tidak diketahui' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Kegiatan</p>
                        <p class="text-lg font-semibold text-gray-900">{{ Auth::user()->organization->activities()?->count() ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Anggota</p>
                        <p class="text-lg font-semibold text-gray-900">{{ Auth::user()->organization->users()?->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
        @catch(Exception $e)
        <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4" role="alert">
            <p class="text-sm text-yellow-800">Informasi organisasi tidak tersedia saat ini.</p>
        </div>
        @endtry
        @endif
    </div>
</div>
@endsection