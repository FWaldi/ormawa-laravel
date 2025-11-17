@extends('layouts.app')

@section('title', 'Berita ' . $organization->name . ' - Organisasi Mahasiswa UNP')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Organization Header -->
    <div class="bg-gradient-to-r from-[--primary-blue] to-blue-700 text-white py-12">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center mb-4">
                        <a href="{{ route('organizations.index') }}" 
                           class="text-blue-200 hover:text-white transition-colors mr-2">
                            Organisasi
                        </a>
                        <svg class="w-4 h-4 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-blue-200 ml-2">{{ $organization->name }}</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold font-lora mb-2">
                        {{ $organization->name }}
                    </h1>
                    <p class="text-blue-100 text-lg">
                        Berita dan informasi terbaru dari {{ $organization->name }}
                    </p>
                </div>
                <div class="hidden md:block">
                    <a href="{{ route('organizations.show', $organization) }}" 
                       class="inline-flex items-center px-6 py-3 bg-white text-[--primary-blue] rounded-lg hover:bg-gray-100 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Tentang Organisasi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="bg-white border-b">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-[--primary-blue]">{{ $news->total() }}</div>
                    <div class="text-sm text-gray-600">Total Berita</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $news->count() }}</div>
                    <div class="text-sm text-gray-600">Halaman Ini</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $news->lastPage() }}</div>
                    <div class="text-sm text-gray-600">Total Halaman</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $organization->members_count ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Anggota</div>
                </div>
            </div>
        </div>
    </div>

    <!-- News Grid -->
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($news->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($news as $article)
                    <article class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden group">
                        <!-- Image -->
                        @if($article->image)
                            <div class="aspect-w-16 aspect-h-9 overflow-hidden bg-gray-100">
                                <img src="{{ Storage::url($article->image) }}" 
                                     alt="{{ $article->title }}"
                                     class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-[--primary-blue] to-blue-600 flex items-center justify-center">
                                <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                </svg>
                            </div>
                        @endif

                        <!-- Content -->
                        <div class="p-6">
                            <!-- Date Badge -->
                            <div class="flex items-center justify-between mb-3">
                                <time datetime="{{ $article->published_at->format('Y-m-d') }}" 
                                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $article->published_at->format('d M Y') }}
                                </time>
                                @if($article->creator)
                                    <span class="text-xs text-gray-500">
                                        {{ $article->creator->name }}
                                    </span>
                                @endif
                            </div>

                            <!-- Title -->
                            <h3 class="text-xl font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-[--primary-blue] transition-colors">
                                <a href="{{ route('news.show', $article) }}" class="hover:underline">
                                    {{ $article->title }}
                                </a>
                            </h3>

                            <!-- Excerpt -->
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                {{ Str::limit(strip_tags($article->content), 120) }}
                            </p>

                            <!-- Read More -->
                            <div class="flex items-center justify-between">
                                <a href="{{ route('news.show', $article) }}" 
                                   class="text-[--primary-blue] hover:text-blue-700 font-medium text-sm flex items-center group">
                                    Baca Selengkapnya
                                    <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $news->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    Belum ada berita
                </h3>
                <p class="text-gray-600 mb-6">
                    {{ $organization->name }} belum mempublikasikan berita apa pun.
                </p>
                <div class="flex items-center justify-center space-x-4">
                    <a href="{{ route('news.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Lihat Semua Berita
                    </a>
                    <a href="{{ route('organizations.show', $organization) }}" 
                       class="inline-flex items-center px-4 py-2 bg-[--primary-blue] text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Tentang {{ $organization->name }}
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Back to Top Button -->
    <button onclick="scrollToTop()" 
            id="backToTop"
            class="fixed bottom-8 left-8 z-30 bg-[--primary-blue] text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-all duration-300 transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-[--primary-blue] focus:ring-offset-2 opacity-0 invisible">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
    </button>
</div>

<!-- Back to Top Script -->
<script>
// Show/hide back to top button based on scroll position
window.addEventListener('scroll', function() {
    const backToTopBtn = document.getElementById('backToTop');
    if (window.pageYOffset > 300) {
        backToTopBtn.classList.remove('opacity-0', 'invisible');
        backToTopBtn.classList.add('opacity-100', 'visible');
    } else {
        backToTopBtn.classList.add('opacity-0', 'invisible');
        backToTopBtn.classList.remove('opacity-100', 'visible');
    }
});

// Scroll to top function
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
</script>
@endsection