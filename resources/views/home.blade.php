@extends('layouts.app')

@section('content')
<div class="scroll-snap-container">
    <!-- Hero Section -->
  <section class="scroll-snap-section relative overflow-hidden"
           style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2070&auto=format&fit=crop'); background-size: cover; background-position: center;">
  <div class="relative text-center text-white animate-fade-in">
    <div class="relative container mx-auto px-4">
      <h1 class="text-4xl md:text-6xl font-bold font-lora">
        Selamat Datang di Pusat Informasi
      </h1>
      <h2 class="mt-2 text-2xl md:text-4xl font-lora">
        Organisasi Mahasiswa Universitas Negeri Padang
      </h2>
    </div>
    <div class="absolute -bottom-24 md:-bottom-32 left-1/2 -translate-x-1/2">
      <svg class="h-10 w-10 text-white/70 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
      </svg>
    </div>
  </div>
</section>

<!-- About Ormawa Section -->
<section class="scroll-snap-section bg-gray-50">
  <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center animate-fade-in">
    <h2 class="text-3xl md:text-4xl font-bold font-lora text-[--primary-blue]">
      Apa itu Ormawa?
    </h2>
    <p class="mt-4 text-lg text-[--text-secondary] max-w-3xl mx-auto leading-relaxed">
      Organisasi Mahasiswa (Ormawa) adalah wadah bagi para mahasiswa untuk mengembangkan minat, bakat, dan potensi kepemimpinan di luar kegiatan akademik. Melalui Ormawa, Anda dapat membangun jaringan, meraih prestasi, serta berkontribusi secara nyata bagi lingkungan kampus dan masyarakat luas.
    </p>
  </div>
</section>

<!-- Announcements Section -->
<section class="scroll-snap-section bg-[--bg-main]">
  <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="w-full h-full flex flex-col justify-center items-center text-center">
      <div class="mb-8">
        <h2 class="text-3xl md:text-4xl font-bold font-lora text-[--primary-blue]">
          Pengumuman Terbaru
        </h2>
        <p class="mt-4 text-lg text-[--text-secondary] max-w-2xl mx-auto">
          Informasi penting dan pengumuman dari organisasi mahasiswa.
        </p>
      </div>

      <div class="cork-board-bg w-full max-w-6xl mx-auto">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($announcements ?? [] as $announcement)
          <div class="info-post-card paper-white rotate-1 scroll-animate">
            <div class="text-left">
              <div class="flex justify-between items-start mb-2">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-800">
                  {{ $announcement['category'] ?? 'Pengumuman' }}
                </span>
                <span class="text-xs text-[--text-secondary]">
                  {{ $announcement['published_at'] ?? now()->format('d M Y') }}
                </span>
              </div>
              <h3 class="text-lg font-bold font-lora text-[--primary-blue] mb-2">
                {{ $announcement['title'] ?? 'Judul Pengumuman' }}
              </h3>
              <p class="text-sm text-[--text-secondary] line-clamp-3">
                {{ Str::limit(strip_tags($announcement['content'] ?? 'Konten pengumuman...'), 100) }}
              </p>
            </div>
          </div>
          @endforeach
          @if(empty($announcements))
          <div class="info-post-card paper-yellow rotate-neg-1">
            <p class="text-center text-[--text-secondary]">Belum ada pengumuman terbaru.</p>
          </div>
          @endif
        </div>
      </div>

      <div class="text-center mt-16">
        <a href="{{ route('announcements.index') }}" class="group inline-flex items-center gap-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-bold py-4 px-10 rounded-2xl hover:from-purple-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl shadow-lg">
          <span>Lihat Semua Pengumuman</span>
          <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
          </svg>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Featured Organizations Section -->
<section class="scroll-snap-section bg-[--bg-main]">
  <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="w-full h-full flex flex-col justify-center items-center text-center">
      <div class="mb-8">
        <h2 class="text-3xl md:text-4xl font-bold font-lora text-[--primary-blue]">
          Ormawa-Ormawa UNP
        </h2>
        <p class="mt-4 text-lg text-[--text-secondary] max-w-2xl mx-auto">
          Lihat beberapa organisasi mahasiswa yang paling aktif dan berprestasi di kampus.
        </p>
      </div>

      <div class="slider mx-auto">
        <div class="slider-track">
          @foreach($organizations as $organization)
          <div class="slide">
            <div class="bg-white rounded-lg shadow-md border overflow-hidden group hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col border-[--border-color] h-full scroll-animate">
                <div class="relative h-48 bg-gray-200 cursor-pointer">
                    @if($organization['logo'])
                        <img src="{{ Storage::url($organization['logo']) }}" alt="{{ $organization['name'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br
                            @if($organization['category'] === 'BEM') from-[--primary-blue] to-blue-600
                            @elseif($organization['category'] === 'UKM') from-green-500 to-emerald-600
                            @elseif($organization['category'] === 'HIMA') from-[--accent-orange] to-red-500
                            @else from-[--primary-blue] to-blue-600
                            @endif flex items-center justify-center">
                            <span class="text-white text-4xl font-bold">{{ substr($organization['name'], 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div class="p-5 flex flex-col flex-grow">
                    <div class="flex-grow cursor-pointer">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-bold font-lora text-[--primary-blue] leading-tight pr-2">
                                {{ $organization['name'] }}
                            </h3>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                @if($organization['category'] === 'BEM') bg-indigo-100 text-indigo-800
                                @elseif($organization['category'] === 'UKM') bg-green-100 text-green-800
                                @elseif($organization['category'] === 'HIMA') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif flex-shrink-0">
                                {{ $organization['category'] }}
                            </span>
                        </div>
                        <p class="text-sm font-medium text-[--text-secondary] mb-3">
                            {{ $organization['faculty'] }}
                        </p>
                        <p class="text-[--text-secondary] text-sm line-clamp-3">
                            {{ $organization['description'] }}
                        </p>
                    </div>
                    <div class="mt-auto pt-4 border-t border-[--border-color] flex justify-between items-center">
                        <div class="flex items-center gap-2 overflow-hidden">
                            @if($organization['logo'])
                                <img src="{{ Storage::url($organization['logo']) }}" alt="{{ $organization['acronym'] }} logo" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                            @else
                                <div class="w-8 h-8 bg-gradient-to-br
                                    @if($organization['category'] === 'BEM') from-[--primary-blue] to-blue-600
                                    @elseif($organization['category'] === 'UKM') from-green-500 to-emerald-600
                                    @elseif($organization['category'] === 'HIMA') from-[--accent-orange] to-red-500
                                    @else from-[--primary-blue] to-blue-600
                                    @endif rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-bold text-sm">{{ substr($organization['name'], 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="flex-grow">
                                <p class="font-semibold text-sm text-gray-700 truncate">
                                    {{ $organization['acronym'] }}
                                </p>
                            </div>
                        </div>

                        <div class="text-[--primary-blue] font-semibold text-sm flex items-center group-hover:text-[--accent-orange] transition-colors cursor-pointer">
                            Detail
                            <svg class="h-4 w-4 ml-1.5 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
          </div>
          @endforeach
          <!-- Duplicate for infinite scroll -->
          @foreach($organizations as $organization)
          <div class="slide">
            <div class="bg-white rounded-lg shadow-md border overflow-hidden group hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col border-[--border-color] h-full scroll-animate">
                <div class="relative h-48 bg-gray-200 cursor-pointer">
                    @if($organization['logo'])
                        <img src="{{ Storage::url($organization['logo']) }}" alt="{{ $organization['name'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br
                            @if($organization['category'] === 'BEM') from-[--primary-blue] to-blue-600
                            @elseif($organization['category'] === 'UKM') from-green-500 to-emerald-600
                            @elseif($organization['category'] === 'HIMA') from-[--accent-orange] to-red-500
                            @else from-[--primary-blue] to-blue-600
                            @endif flex items-center justify-center">
                            <span class="text-white text-4xl font-bold">{{ substr($organization['name'], 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div class="p-5 flex flex-col flex-grow">
                    <div class="flex-grow cursor-pointer">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-bold font-lora text-[--primary-blue] leading-tight pr-2">
                                {{ $organization['name'] }}
                            </h3>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                @if($organization['category'] === 'BEM') bg-indigo-100 text-indigo-800
                                @elseif($organization['category'] === 'UKM') bg-green-100 text-green-800
                                @elseif($organization['category'] === 'HIMA') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif flex-shrink-0">
                                {{ $organization['category'] }}
                            </span>
                        </div>
                        <p class="text-sm font-medium text-[--text-secondary] mb-3">
                            {{ $organization['faculty'] }}
                        </p>
                        <p class="text-[--text-secondary] text-sm line-clamp-3">
                            {{ $organization['description'] }}
                        </p>
                    </div>
                    <div class="mt-auto pt-4 border-t border-[--border-color] flex justify-between items-center">
                        <div class="flex items-center gap-2 overflow-hidden">
                            @if($organization['logo'])
                                <img src="{{ Storage::url($organization['logo']) }}" alt="{{ $organization['acronym'] }} logo" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                            @else
                                <div class="w-8 h-8 bg-gradient-to-br
                                    @if($organization['category'] === 'BEM') from-[--primary-blue] to-blue-600
                                    @elseif($organization['category'] === 'UKM') from-green-500 to-emerald-600
                                    @elseif($organization['category'] === 'HIMA') from-[--accent-orange] to-red-500
                                    @else from-[--primary-blue] to-blue-600
                                    @endif rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-bold text-sm">{{ substr($organization['name'], 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="flex-grow">
                                <p class="font-semibold text-sm text-gray-700 truncate">
                                    {{ $organization['acronym'] }}
                                </p>
                            </div>
                        </div>

                        <div class="text-[--primary-blue] font-semibold text-sm flex items-center group-hover:text-[--accent-orange] transition-colors cursor-pointer">
                            Detail
                            <svg class="h-4 w-4 ml-1.5 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>

      <div class="text-center mt-16">
        <a href="{{ route('organizations.index') }}" class="group inline-flex items-center gap-3 bg-gradient-to-r from-[--primary-blue] to-blue-600 text-white font-bold py-4 px-10 rounded-2xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl shadow-lg">
          <span>Lihat Semua Ormawa</span>
          <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
          </svg>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Latest News Section -->
<section class="scroll-snap-section bg-gray-50">
  <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="w-full h-full flex flex-col justify-center items-center text-center">
      <div class="mb-8">
        <h2 class="text-3xl md:text-4xl font-bold font-lora text-[--primary-blue]">
          Berita Terbaru
        </h2>
        <p class="mt-4 text-lg text-[--text-secondary] max-w-2xl mx-auto">
          Ikuti perkembangan dan kegiatan terbaru dari organisasi mahasiswa di kampus.
        </p>
      </div>

      <div class="slider mx-auto">
        <div class="slider-track">
          @foreach($news as $article)
          <div class="slide">
            <div class="bg-white rounded-lg shadow-md border border-[--border-color] overflow-hidden group hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer flex flex-col h-full scroll-animate">
                <div class="relative h-56 bg-gray-200 flex-shrink-0">
                    @if($article['image'])
                        <img src="{{ Storage::url($article['image']) }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br
                            @if($article['category'] === 'Pengumuman') from-[--primary-blue]/20 to-[--accent-orange]/20
                            @elseif($article['category'] === 'Kegiatan') from-green-500/20 to-emerald-500/20
                            @elseif($article['category'] === 'Rekrutmen') from-[--accent-orange]/20 to-red-500/20
                            @else from-[--primary-blue]/20 to-[--accent-orange]/20
                            @endif flex items-center justify-center">
                            @if($article['category'] === 'Pengumuman')
                                <i class="fas fa-graduation-cap text-white text-3xl"></i>
                            @elseif($article['category'] === 'Kegiatan')
                                <i class="fas fa-music text-white text-3xl"></i>
                            @elseif($article['category'] === 'Rekrutmen')
                                <i class="fas fa-users text-white text-3xl"></i>
                            @else
                                <i class="fas fa-newspaper text-white text-3xl"></i>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="p-5 flex flex-col flex-grow text-left">
                    <div class="flex-grow">
                        <div class="flex justify-between items-center text-sm mb-2">
                            <span class="font-semibold
                                @if($article['category'] === 'Pengumuman') text-indigo-800 bg-indigo-100
                                @elseif($article['category'] === 'Kegiatan') text-green-800 bg-green-100
                                @elseif($article['category'] === 'Rekrutmen') text-yellow-800 bg-yellow-100
                                @else text-gray-800 bg-gray-100
                                @endif px-2.5 py-1 rounded-full">
                                {{ $article['category'] }}
                            </span>
                            <span class="text-[--text-secondary]">
                                {{ $article['published_at'] }}
                            </span>
                        </div>
                        <h3 class="text-lg font-bold font-lora text-[--text-dark] leading-tight group-hover:text-[--primary-blue] transition-colors line-clamp-3">
                            {{ $article['title'] }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-2 line-clamp-2">
                            {{ Str::limit(strip_tags($article['content']), 100) }}
                        </p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-[--border-color] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-gradient-to-br
                                @if($article['organization_acronym'] === 'BEM UNP') from-[--primary-blue] to-blue-600
                                @elseif($article['organization_acronym'] === 'SYMPHONY') from-green-500 to-emerald-600
                                @elseif($article['organization_acronym'] === 'HIMA MAT') from-[--accent-orange] to-red-500
                                @else from-[--primary-blue] to-blue-600
                                @endif rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-xs">{{ substr($article['organization_acronym'], 0, 1) }}</span>
                            </div>
                            <span class="text-sm font-semibold text-[--text-secondary]">
                                {{ $article['organization_acronym'] }}
                            </span>
                        </div>
                        <span class="text-[--primary-blue] font-semibold text-sm group-hover:text-[--accent-orange] transition-colors">
                            Baca Selengkapnya
                        </span>
                    </div>
                </div>
            </div>
          </div>
          @endforeach
          <!-- Duplicate for infinite scroll -->
          @foreach($news as $article)
          <div class="slide">
            <div class="bg-white rounded-lg shadow-md border border-[--border-color] overflow-hidden group hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer flex flex-col h-full scroll-animate">
                <div class="relative h-56 bg-gray-200 flex-shrink-0">
                    @if($article['image'])
                        <img src="{{ Storage::url($article['image']) }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br
                            @if($article['category'] === 'Pengumuman') from-[--primary-blue]/20 to-[--accent-orange]/20
                            @elseif($article['category'] === 'Kegiatan') from-green-500/20 to-emerald-500/20
                            @elseif($article['category'] === 'Rekrutmen') from-[--accent-orange]/20 to-red-500/20
                            @else from-[--primary-blue]/20 to-[--accent-orange]/20
                            @endif flex items-center justify-center">
                            @if($article['category'] === 'Pengumuman')
                                <i class="fas fa-graduation-cap text-white text-3xl"></i>
                            @elseif($article['category'] === 'Kegiatan')
                                <i class="fas fa-music text-white text-3xl"></i>
                            @elseif($article['category'] === 'Rekrutmen')
                                <i class="fas fa-users text-white text-3xl"></i>
                            @else
                                <i class="fas fa-newspaper text-white text-3xl"></i>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="p-5 flex flex-col flex-grow text-left">
                    <div class="flex-grow">
                        <div class="flex justify-between items-center text-sm mb-2">
                            <span class="font-semibold
                                @if($article['category'] === 'Pengumuman') text-indigo-800 bg-indigo-100
                                @elseif($article['category'] === 'Kegiatan') text-green-800 bg-green-100
                                @elseif($article['category'] === 'Rekrutmen') text-yellow-800 bg-yellow-100
                                @else text-gray-800 bg-gray-100
                                @endif px-2.5 py-1 rounded-full">
                                {{ $article['category'] }}
                            </span>
                            <span class="text-[--text-secondary]">
                                {{ $article['published_at'] }}
                            </span>
                        </div>
                        <h3 class="text-lg font-bold font-lora text-[--text-dark] leading-tight group-hover:text-[--primary-blue] transition-colors line-clamp-3">
                            {{ $article['title'] }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-2 line-clamp-2">
                            {{ Str::limit(strip_tags($article['content']), 100) }}
                        </p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-[--border-color] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-gradient-to-br
                                @if($article['organization_acronym'] === 'BEM UNP') from-[--primary-blue] to-blue-600
                                @elseif($article['organization_acronym'] === 'SYMPHONY') from-green-500 to-emerald-600
                                @elseif($article['organization_acronym'] === 'HIMA MAT') from-[--accent-orange] to-red-500
                                @else from-[--primary-blue] to-blue-600
                                @endif rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-xs">{{ substr($article['organization_acronym'], 0, 1) }}</span>
                            </div>
                            <span class="text-sm font-semibold text-[--text-secondary]">
                                {{ $article['organization_acronym'] }}
                            </span>
                        </div>
                        <span class="text-[--primary-blue] font-semibold text-sm group-hover:text-[--accent-orange] transition-colors">
                            Baca Selengkapnya
                        </span>
                    </div>
                </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>

      <div class="text-center mt-16">
        <a href="{{ route('news.index') }}" class="group inline-flex items-center gap-3 bg-gradient-to-r from-[--accent-orange] to-orange-500 text-white font-bold py-4 px-10 rounded-2xl hover:from-orange-500 hover:to-orange-600 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl shadow-lg">
          <span>Lihat Semua Berita</span>
          <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
          </svg>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Call to Action Section -->
<section class="scroll-snap-section p-0">
  <div class="w-full h-full flex flex-col justify-center items-center bg-cover bg-center bg-fixed"
        style="background-image: url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070&auto=format&fit=crop');">
    <div class="bg-[--primary-blue] bg-opacity-80 text-center text-white py-20 px-6 rounded-lg container mx-auto shadow-xl">
      <h2 class="text-3xl md:text-4xl font-bold font-lora">
        Siap Menjelajah?
      </h2>
      <p class="mt-4 text-lg max-w-2xl mx-auto">
        Dunia kemahasiswaan yang dinamis menanti Anda. Temukan minat dan bakat Anda sekarang!
      </p>
      <div class="mt-8">
        <button class="bg-[--accent-orange] text-[--text-dark] font-bold py-3 px-8 rounded-md hover:bg-orange-500 transition-all duration-300 transform hover:scale-105 shadow-lg">
          Mulai Cari Ormawa
        </button>
      </div>
    </div>
  </div>
    <footer class="bg-[--primary-blue] text-gray-300 mt-auto">
      <div class="max-w-screen-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center text-sm">
        <p>&copy; {{ date('Y') }} Universitas Negeri Padang. Seluruh hak cipta dilindungi.</p>
        <p class="mt-2 text-gray-400 text-xs">
          Dibangun oleh Unit Kegiatan Infinite Technology Universitas Negeri Padang (UK Infitech UNP).
        </p>
      </div>
    </footer>
  </div>
</section>
</div>
@endsection

<style>


/* Custom animations */
@keyframes gradient-x {
    0%, 100% {
        background-size: 200% 200%;
        background-position: left center;
    }
    50% {
        background-size: 200% 200%;
        background-position: right center;
    }
}

.animate-gradient-x {
    animation: gradient-x 3s ease infinite;
    background-size: 200% 200%;
}

.animate-fade-in {
    animation: fadeIn 1s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Glassmorphism effect */
.backdrop-blur-lg {
    backdrop-filter: blur(16px);
}

/* Enhanced hover effects */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

.group:hover .group-hover\:rotate-12 {
    transform: rotate(12deg);
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, var(--primary-blue), var(--accent-orange));
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, var(--accent-orange), var(--primary-blue));
}
</style>