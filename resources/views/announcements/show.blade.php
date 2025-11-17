@extends('layouts.app')

@section('title', $announcement->title . ' - Organisasi Mahasiswa UNP')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('announcements.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Pengumuman
            </a>
        </div>

        <!-- Announcement Content -->
        <article class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 lg:p-8">
                <!-- Header -->
                <header class="mb-6">
                    @if($announcement->is_pinned)
                        <div class="mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/>
                                </svg>
                                Disematkan
                            </span>
                        </div>
                    @endif

                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $announcement->title }}</h1>
                    
                    <div class="flex flex-wrap items-center text-sm text-gray-500 space-y-2 sm:space-y-0 sm:space-x-6">
                        @if($announcement->creator)
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[--accent-orange] to-orange-600 flex items-center justify-center text-white font-bold text-sm mr-2">
                                    {{ strtoupper(substr($announcement->creator->name, 0, 1)) }}
                                </div>
                                <span>{{ $announcement->creator->name }}</span>
                            </div>
                        @endif
                        
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $announcement->created_at->format('d F Y') }}
                        </div>

                        @if($announcement->category)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $announcement->category }}
                            </span>
                        @endif
                    </div>
                </header>

                <!-- Image -->
                @if($announcement->image)
                    <div class="mb-6">
                        <img src="{{ asset('storage/' . $announcement->image) }}" 
                             alt="{{ $announcement->title }}" 
                             class="w-full h-auto max-h-96 object-cover rounded-lg">
                    </div>
                @endif

                <!-- Content -->
                <div class="prose prose-lg max-w-none">
                    <div class="text-gray-700 leading-relaxed">
                        {!! $announcement->content !!}
                    </div>
                </div>

                <!-- Footer with Actions -->
                @auth
                    @if(Auth::user()->role !== 'user')
                        <footer class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-500">
                                    Terakhir diperbarui: {{ $announcement->updated_at->format('d F Y H:i') }}
                                </div>
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('announcements.edit', $announcement) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </footer>
                    @endif
                @endauth
            </div>
        </article>

        <!-- Related Announcements (Optional) -->
        @if(isset($relatedAnnouncements) && $relatedAnnouncements->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Pengumuman Lainnya</h2>
                <div class="grid gap-6 md:grid-cols-2">
                    @foreach($relatedAnnouncements as $related)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                            @if($related->is_pinned)
                                <div class="mb-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/>
                                        </svg>
                                        Disematkan
                                    </span>
                                </div>
                            @endif
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                <a href="{{ route('announcements.show', $related) }}" class="hover:text-[--primary-blue] transition-colors">
                                    {{ $related->title }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-500 mb-3">{{ $related->created_at->format('d M Y') }}</p>
                            <div class="text-gray-700">
                                {!! Str::limit(strip_tags($related->content), 100) !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection