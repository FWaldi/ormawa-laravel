@extends('layouts.app')

@section('title', 'Pengumuman - Organisasi Mahasiswa UNP')

@section('content')
<div class="animate-fade-in">
    <!-- Highlighted Content - Full Width -->
    @if($announcements->count() > 0)
        <div class="mb-12">
            <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold font-lora text-gray-700 mb-6">Sorotan Pengumuman</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($announcements->take(3) as $announcement)
                        <div class="bg-white rounded-lg shadow-lg border border-[--border-color] overflow-hidden hover:shadow-xl transition-all duration-300">
                            @if($announcement->image)
                                <img src="{{ asset('storage/' . $announcement->image) }}" alt="{{ $announcement->title }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gradient-to-br from-[--primary-blue] to-blue-600 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="p-4">
                                <h3 class="text-lg font-bold font-lora text-[--text-dark] line-clamp-2">{{ $announcement->title ?? 'Untitled Announcement' }}</h3>
                                <p class="text-sm text-gray-500 mt-2">{{ Str::limit(strip_tags($announcement->content ?? ''), 100) }}</p>
                                <a href="{{ route('announcements.show', $announcement) }}" class="text-[--primary-blue] font-semibold text-sm mt-2 inline-block">Baca Selengkapnya</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content with Container -->
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <!-- Filter Panel -->
        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg border border-[--border-color] mb-10 space-y-4">
            <div class="text-center mb-2">
                <h2 class="text-2xl font-bold font-lora text-gray-700">
                    Pengumuman Direktorat Kemahasiswaan
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div class="lg:col-span-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Kategori
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button class="px-4 py-2 text-sm rounded-md transition-colors bg-[--primary-blue] text-white">
                            Semua
                        </button>
                        <button class="px-4 py-2 text-sm rounded-md transition-colors bg-gray-200 hover:bg-gray-300">
                            Akademik
                        </button>
                        <button class="px-4 py-2 text-sm rounded-md transition-colors bg-gray-200 hover:bg-gray-300">
                            Beasiswa
                        </button>
                        <button class="px-4 py-2 text-sm rounded-md transition-colors bg-gray-200 hover:bg-gray-300">
                            Lomba
                        </button>
                        <button class="px-4 py-2 text-sm rounded-md transition-colors bg-gray-200 hover:bg-gray-300">
                            Umum
                        </button>
                    </div>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Sumber
                    </label>
                    <select class="w-full p-2 border border-gray-300 rounded-md bg-white">
                        <option value="Semua">Semua</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Urutkan
                    </label>
                    <select class="w-full p-2 border border-gray-300 rounded-md bg-white">
                        <option value="newest">Terbaru</option>
                        <option value="oldest">Terlama</option>
                    </select>
                </div>
                <div>
                    <button class="w-full flex items-center justify-center gap-2 text-sm text-red-600 font-semibold bg-red-50 hover:bg-red-100 p-2.5 rounded-md">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reset
                    </button>
                </div>
            </div>
            <div class="border-t pt-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Cari Pengumuman
                </label>
                <input
                    type="text"
                    placeholder="Cari berdasarkan judul atau konten..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[--primary-blue]"
                />
            </div>
        </div>

        <!-- Content -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($announcements as $announcement)
                <article class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden group hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer flex flex-col">
                    <div class="relative h-48 bg-gray-200">
                        @if($announcement->image)
                            <img src="{{ asset('storage/' . $announcement->image) }}" alt="{{ $announcement->title }}" class="w-full h-full object-cover" loading="lazy" decoding="async">
                        @else
                            <img src="https://picsum.photos/seed/{{ $announcement->id }}/400/300" alt="{{ $announcement->title }}" class="w-full h-full object-cover" loading="lazy" decoding="async">
                        @endif
                    </div>
                    <div class="p-5 flex flex-col flex-grow">
                        <div class="flex justify-between items-center text-xs mb-3">
                            <span class="font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-800">
                                {{ $announcement->category ?? 'Umum' }}
                            </span>
                            <span class="text-gray-500">{{ $announcement->created_at ? $announcement->created_at->format('d M Y') : 'No date' }}</span>
                        </div>
                        <h2 class="text-lg font-bold font-lora text-gray-800 leading-tight group-hover:text-[--primary-blue] transition-colors flex-grow line-clamp-3">
                            <a href="{{ route('announcements.show', $announcement) }}">{{ $announcement->title }}</a>
                        </h2>
                        <div class="mt-4 pt-4 border-t border-gray-200 text-sm font-semibold text-[--primary-blue] group-hover:text-[--accent-orange] flex items-center justify-between">
                            <span>Baca Selengkapnya</span>
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full text-center py-16 px-4 bg-gray-50 rounded-lg">
                    <svg class="h-16 w-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                    <h3 class="text-2xl font-semibold font-lora text-gray-600">
                        Tidak Ada Pengumuman Ditemukan
                    </h3>
                    <p class="text-gray-500 mt-2">
                        Coba sesuaikan filter atau kata kunci pencarian Anda.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection