@extends('layouts.app')

@section('title', 'Berita - Organisasi Mahasiswa UNP')

@section('content')
<div class="animate-fade-in">
    <!-- Highlighted Content - Full Width -->
    @if($news->count() > 0)
        @php
            $recentNews = $news->filter(function($article) {
                return $article->published_at && $article->published_at->greaterThanOrEqualTo(now()->subMonth());
            })->sortByDesc('published_at')->take(5);
        @endphp
        @if($recentNews->count() > 0)
            <div class="relative w-full h-[50vh] md:h-[65vh] text-white overflow-hidden mb-12">
                <div class="absolute inset-0 bg-gray-900">
                    @if($recentNews->first()->image)
                        <img src="{{ Storage::url($recentNews->first()->image) }}" alt="{{ $recentNews->first()->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-[--primary-blue] to-blue-600 flex items-center justify-center">
                            <div class="text-center">
                                <h2 class="text-4xl font-bold mb-4">Sorotan Berita</h2>
                                <p class="text-xl">Berita terbaru dari organisasi mahasiswa</p>
                            </div>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-black/20"></div>
                </div>

                <div class="absolute top-6 left-1/2 -translate-x-1/2 text-3xl font-bold font-lora text-shadow z-20">
                    Sorotan Berita
                </div>

                <div class="absolute inset-0 flex items-end justify-center text-center p-6 md:p-12 z-10">
                    <div class="max-w-4xl">
                        <span class="text-sm font-semibold bg-white/20 px-3 py-1 rounded-full text-shadow">
                            {{ $recentNews->first()->category ?? 'Berita' }}
                        </span>
                        <h3 class="text-3xl md:text-5xl font-bold font-lora mt-4 text-shadow animate-fade-in">
                            {{ $recentNews->first()->title }}
                        </h3>
                        <p class="text-md md:text-lg mt-2 text-gray-200 text-shadow animate-fade-in line-clamp-2">
                            {{ Str::limit(strip_tags($recentNews->first()->content), 150) }}
                        </p>
                        <a href="{{ route('news.show', $recentNews->first()) }}" class="inline-block mt-4 bg-white/20 hover:bg-white/30 px-6 py-3 rounded-md text-white font-semibold transition-colors">
                            Baca Selengkapnya
                        </a>
                    </div>
                </div>

                @if($recentNews->count() > 1)
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 w-11/12 max-w-xs flex items-center space-x-2 z-20">
                        @foreach($recentNews as $index => $article)
                            <button class="w-full h-1 bg-white/30 rounded-full cursor-pointer p-0 {{ $index === 0 ? 'bg-white' : '' }}"
                                    onclick="showSlide({{ $index }})">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    @endif

    <!-- Main Content with Container -->
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <!-- Filter Panel -->
        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg border border-[--border-color] mb-10 space-y-4">
            <div class="text-center mb-2">
                <h2 class="text-2xl font-bold font-lora text-gray-700">
                    Berita Organisasi Kemahasiswaan
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Filter per Organisasi
                    </label>
                    <select class="w-full p-2 border border-gray-300 rounded-md bg-white">
                        <option value="all">Semua Organisasi</option>
                        @foreach($organizations as $organization)
                            <option value="{{ $organization->id }}">{{ $organization->acronym }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Urutkan Berdasarkan
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
                        Reset Filter
                    </button>
                </div>
            </div>
            <div class="border-t pt-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Cari Berita
                </label>
                <input
                    type="text"
                    placeholder="Cari judul, konten, atau nama organisasi..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[--primary-blue]"
                />
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
                        <div class="p-5 flex flex-col flex-grow text-left">
                            <div class="flex-grow">
                                <div class="flex justify-between items-center text-sm mb-2">
                                    <span class="font-semibold text-indigo-800 bg-indigo-100 px-2.5 py-1 rounded-full">
                                        {{ $article->category ?? 'Berita' }}
                                    </span>
                                    <span class="text-[--text-secondary]">
                                        {{ $article->published_at->format('d M Y') }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold font-lora text-[--text-dark] leading-tight group-hover:text-[--primary-blue] transition-colors line-clamp-3">
                                    {{ $article->title }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-2 line-clamp-2">
                                    {{ Str::limit(strip_tags($article->content), 100) }}
                                </p>
                                @if($article->tags && count($article->tags) > 0)
                                    <div class="mt-3 flex flex-wrap gap-1.5">
                                        @foreach(array_slice($article->tags, 0, 3) as $tag)
                                            <span class="text-xs font-medium bg-gray-100 text-gray-700 px-2 py-1 rounded-full">
                                                #{{ $tag }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="mt-4 pt-4 border-t border-[--border-color] flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    @if($article->organization->logo)
                                        <img src="{{ Storage::url($article->organization->logo) }}" alt="{{ $article->organization->acronym }} logo" class="w-6 h-6 rounded-full object-cover">
                                    @endif
                                    <span class="text-sm font-semibold text-[--text-secondary]">
                                        {{ $article->organization->acronym }}
                                    </span>
                                </div>
                                <a href="{{ route('news.show', $article) }}" class="text-[--primary-blue] font-semibold text-sm group-hover:text-[--accent-orange] transition-colors">
                                    Baca Selengkapnya
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
                <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4 12H9m3 0a3 3 0 01-3-3V9a3 3 0 013-3h1m-1 12v-3.375A3.375 3.375 0 0112.375 12H15m0 0a3 3 0 013 3v3m0 0a3 3 0 01-3-3v-3m0 0h-2.625A3.375 3.375 0 019 12V9a3 3 0 013-3h1"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-600">
                    Tidak ada berita ditemukan.
                </h3>
                <p class="text-gray-500 mt-2">
                    Coba sesuaikan filter atau kata kunci pencarian Anda.
                </p>
            </div>
        @endif
    </div>

    <!-- Create News Button (for authenticated users) -->
    @auth
        @if(Auth::user()->is_admin || Auth::user()->organizations()->count() > 0)
            <div class="fixed bottom-8 right-8 z-30">
                <a href="{{ route('news.create') }}" 
                   class="bg-[--accent-orange] text-white p-4 rounded-full shadow-lg hover:bg-orange-500 transition-all duration-300 transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-[--accent-orange] focus:ring-offset-2"
                   title="Buat Berita Baru">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </a>
            </div>
        @endif
    @endauth
</div>

<script>
let currentSlide = 0;
const slides = @json($recentNews ?? collect());
const slideDuration = 7000; // 7 seconds

function showSlide(index) {
    currentSlide = index;
    const highlightedContent = document.querySelector('.relative.w-full.h-\\[50vh\\]');
    if (!highlightedContent || slides.length === 0) return;

    // Update background image
    const bgImage = highlightedContent.querySelector('img');
    if (bgImage) {
        bgImage.src = slides[index].image ? '/storage/' + slides[index].image : '';
        bgImage.alt = slides[index].title;
    }

    // Update content
    const categorySpan = highlightedContent.querySelector('span.font-semibold');
    const titleH3 = highlightedContent.querySelector('h3.text-3xl');
    const excerptP = highlightedContent.querySelector('p.text-md');
    const readMoreLink = highlightedContent.querySelector('a.inline-block');

    if (categorySpan) categorySpan.textContent = slides[index].category || 'Berita';
    if (titleH3) titleH3.textContent = slides[index].title;
    if (excerptP) excerptP.textContent = slides[index].content ? slides[index].content.replace(/<[^>]*>/g, '').substring(0, 150) + '...' : '';
    if (readMoreLink) readMoreLink.href = '/news/' + slides[index].id;

    // Update indicators
    const indicators = highlightedContent.querySelectorAll('button.w-full.h-1');
    indicators.forEach((indicator, i) => {
        indicator.classList.toggle('bg-white', i === index);
        indicator.classList.toggle('bg-white/30', i !== index);
    });
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
}

// Auto-play carousel
if (slides.length > 1) {
    setInterval(nextSlide, slideDuration);
}

// Initialize first slide
if (slides.length > 0) {
    showSlide(0);
}
</script>
@endsection