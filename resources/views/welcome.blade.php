@extends('layouts.app')

@section('title', 'Selamat Datang - Organisasi Mahasiswa Universitas Negeri Padang')

@section('content')
<div class="scroll-snap-container">
    <!-- Hero Section -->
    <div class="scroll-snap-section min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="relative overflow-hidden h-full">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 h-full flex items-center">
                <div class="text-center w-full">
                    <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                        Selamat Datang di
                        <span class="text-gradient">Portal Ormawa</span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                        Platform terpadu untuk organisasi mahasiswa Universitas Negeri Padang.
                        Kelola kegiatan, informasi, dan kolaborasi dengan lebih mudah.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @guest
                            <a href="{{ route('login') }}" class="btn-accent text-lg px-8 py-3">
                                Masuk Sekarang
                            </a>
                            <a href="{{ route('organizations.index') }}" class="btn-secondary text-lg px-8 py-3">
                                Jelajahi Organisasi
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn-primary text-lg px-8 py-3">
                                Dashboard Saya
                            </a>
                            <a href="{{ route('organizations.index') }}" class="btn-secondary text-lg px-8 py-3">
                                Lihat Organisasi
                            </a>
                        @endguest
                    </div>
                </div>
            </div>

            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 -translate-y-12 translate-x-12 opacity-20">
                <div class="w-64 h-64 bg-blue-300 rounded-full filter blur-3xl"></div>
            </div>
            <div class="absolute bottom-0 left-0 translate-y-12 -translate-x-12 opacity-20">
                <div class="w-96 h-96 bg-orange-300 rounded-full filter blur-3xl"></div>
            </div>
        </div>
    </div>

    <!-- About Ormawa Section -->
    <div class="scroll-snap-section bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 h-full flex items-center">
            <div class="text-center animate-fade-in w-full">
                <h2 class="text-3xl md:text-4xl font-bold font-lora text-[--primary-blue]">
                    Apa itu Ormawa?
                </h2>
                <p class="mt-4 text-lg text-[--text-secondary] max-w-3xl mx-auto leading-relaxed">
                    Organisasi Mahasiswa (Ormawa) adalah wadah bagi para mahasiswa untuk mengembangkan minat, bakat, dan potensi kepemimpinan di luar kegiatan akademik. Melalui Ormawa, Anda dapat membangun jaringan, meraih prestasi, serta berkontribusi secara nyata bagi lingkungan kampus dan masyarakat luas.
                </p>
            </div>
        </div>
    </div>

    <!-- Featured Organizations Section -->
    <div class="scroll-snap-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 h-full flex flex-col justify-center">
            <div class="w-full h-full flex flex-col justify-center items-center text-center">
                <div class="mb-8">
                    <h2 class="text-3xl md:text-4xl font-bold font-lora text-[--primary-blue]">
                        Ormawa-Ormawa UNP
                    </h2>
                    <p class="mt-4 text-lg text-[--text-secondary] max-w-2xl mx-auto">
                        Lihat beberapa organisasi mahasiswa yang paling aktif dan berprestasi di kampus.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10 mb-16">
                    <!-- Sample organizations - in real app this would come from database -->
                    <div class="bg-white rounded-lg shadow-lg border overflow-hidden group hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col border-[--border-color] animate-fade-in">
                        <div class="relative h-48 bg-gray-200 flex-shrink-0">
                            <div class="w-full h-full bg-gradient-to-br from-[--primary-blue] to-blue-600 flex items-center justify-center">
                                <span class="text-white text-4xl font-bold">B</span>
                            </div>
                        </div>
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex-grow cursor-pointer">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-bold font-lora text-[--primary-blue] leading-tight pr-2">
                                        Badan Eksekutif Mahasiswa
                                    </h3>
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-800 flex-shrink-0">
                                        BEM
                                    </span>
                                </div>
                                <p class="text-sm font-medium text-[--text-secondary] mb-3">
                                    Fakultas Ekonomi
                                </p>
                                <p class="text-[--text-secondary] text-sm line-clamp-3">
                                    Lembaga eksekutif tertinggi mahasiswa UNP yang mewakili aspirasi kemahasiswaan.
                                </p>
                            </div>
                            <div class="mt-auto pt-4 border-t border-[--border-color] flex justify-between items-center">
                                <div class="flex items-center gap-2 overflow-hidden">
                                    <div class="w-8 h-8 bg-gradient-to-br from-[--primary-blue] to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-bold text-sm">B</span>
                                    </div>
                                    <div class="flex-grow">
                                        <p class="font-semibold text-sm text-gray-700 truncate">
                                            BEM UNP
                                        </p>
                                    </div>
                                </div>
                                <div class="text-[--primary-blue] font-semibold text-sm flex items-center group-hover:text-[--accent-orange] transition-colors cursor-pointer">
                                    Detail
                                    <svg class="w-4 h-4 ml-1.5 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg border overflow-hidden group hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col border-[--border-color] animate-fade-in">
                        <div class="relative h-48 bg-gray-200 flex-shrink-0">
                            <div class="w-full h-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                                <span class="text-white text-4xl font-bold">S</span>
                            </div>
                        </div>
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex-grow cursor-pointer">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-bold font-lora text-[--primary-blue] leading-tight pr-2">
                                        UKM Paduan Suara Symphony
                                    </h3>
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-green-100 text-green-800 flex-shrink-0">
                                        UKM
                                    </span>
                                </div>
                                <p class="text-sm font-medium text-[--text-secondary] mb-3">
                                    Fakultas Ilmu Budaya
                                </p>
                                <p class="text-[--text-secondary] text-sm line-clamp-3">
                                    Unit Kegiatan Mahasiswa yang mengembangkan bakat vokal mahasiswa UNP.
                                </p>
                            </div>
                            <div class="mt-auto pt-4 border-t border-[--border-color] flex justify-between items-center">
                                <div class="flex items-center gap-2 overflow-hidden">
                                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-bold text-sm">S</span>
                                    </div>
                                    <div class="flex-grow">
                                        <p class="font-semibold text-sm text-gray-700 truncate">
                                            SYMPHONY
                                        </p>
                                    </div>
                                </div>
                                <div class="text-[--primary-blue] font-semibold text-sm flex items-center group-hover:text-[--accent-orange] transition-colors cursor-pointer">
                                    Detail
                                    <svg class="w-4 h-4 ml-1.5 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg border overflow-hidden group hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col border-[--border-color] animate-fade-in">
                        <div class="relative h-48 bg-gray-200 flex-shrink-0">
                            <div class="w-full h-full bg-gradient-to-br from-[--accent-orange] to-red-500 flex items-center justify-center">
                                <span class="text-white text-4xl font-bold">M</span>
                            </div>
                        </div>
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex-grow cursor-pointer">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-bold font-lora text-[--primary-blue] leading-tight pr-2">
                                        HIMA Matematika
                                    </h3>
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-800 flex-shrink-0">
                                        HIMA
                                    </span>
                                </div>
                                <p class="text-sm font-medium text-[--text-secondary] mb-3">
                                    FMIPA
                                </p>
                                <p class="text-[--text-secondary] text-sm line-clamp-3">
                                    Organisasi mahasiswa Program Studi Matematika FMIPA UNP.
                                </p>
                            </div>
                            <div class="mt-auto pt-4 border-t border-[--border-color] flex justify-between items-center">
                                <div class="flex items-center gap-2 overflow-hidden">
                                    <div class="w-8 h-8 bg-gradient-to-br from-[--accent-orange] to-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-bold text-sm">M</span>
                                    </div>
                                    <div class="flex-grow">
                                        <p class="font-semibold text-sm text-gray-700 truncate">
                                            HIMA MAT
                                        </p>
                                    </div>
                                </div>
                                <div class="text-[--primary-blue] font-semibold text-sm flex items-center group-hover:text-[--accent-orange] transition-colors cursor-pointer">
                                    Detail
                                    <svg class="w-4 h-4 ml-1.5 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('organizations.index') }}" class="group inline-flex items-center gap-3 bg-gradient-to-r from-[--primary-blue] to-blue-600 text-white font-bold py-4 px-10 rounded-2xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl shadow-lg">
                        <span>Lihat Semua Ormawa</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest News Section -->
    <div class="scroll-snap-section bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 h-full flex flex-col justify-center">
            <div class="w-full h-full flex flex-col justify-center items-center text-center">
                <div class="mb-8">
                    <h2 class="text-3xl md:text-4xl font-bold font-lora text-[--primary-blue]">
                        Berita Terbaru
                    </h2>
                    <p class="mt-4 text-lg text-[--text-secondary] max-w-2xl mx-auto">
                        Ikuti perkembangan dan kegiatan terbaru dari organisasi mahasiswa di kampus.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10 mb-16">
                    <!-- Sample news articles -->
                    <div class="bg-white rounded-lg shadow-md border border-[--border-color] overflow-hidden group hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer flex flex-col animate-fade-in">
                        <div class="relative h-56 bg-gray-200 flex-shrink-0">
                            <div class="w-full h-full bg-gradient-to-br from-[--primary-blue]/20 to-[--accent-orange]/20 flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white text-3xl"></i>
                            </div>
                        </div>
                        <div class="p-5 flex flex-col flex-grow text-left">
                            <div class="flex-grow">
                                <div class="flex justify-between items-center text-sm mb-2">
                                    <span class="font-semibold text-indigo-800 bg-indigo-100 px-2.5 py-1 rounded-full">
                                        Pengumuman
                                    </span>
                                    <span class="text-[--text-secondary]">
                                        2 hari lalu
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold font-lora text-[--text-dark] leading-tight group-hover:text-[--primary-blue] transition-colors line-clamp-3">
                                    Pembukaan Masa Orientasi Mahasiswa Baru 2024
                                </h3>
                                <p class="text-sm text-gray-500 mt-2 line-clamp-2">
                                    BEM UNP mengadakan acara orientasi untuk mahasiswa baru dengan berbagai kegiatan menarik.
                                </p>
                            </div>
                            <div class="mt-4 pt-4 border-t border-[--border-color] flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-gradient-to-br from-[--primary-blue] to-blue-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-xs">B</span>
                                    </div>
                                    <span class="text-sm font-semibold text-[--text-secondary]">
                                        BEM UNP
                                    </span>
                                </div>
                                <span class="text-[--primary-blue] font-semibold text-sm group-hover:text-[--accent-orange] transition-colors">
                                    Baca Selengkapnya
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md border border-[--border-color] overflow-hidden group hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer flex flex-col animate-fade-in">
                        <div class="relative h-56 bg-gray-200 flex-shrink-0">
                            <div class="w-full h-full bg-gradient-to-br from-green-500/20 to-emerald-500/20 flex items-center justify-center">
                                <i class="fas fa-music text-white text-3xl"></i>
                            </div>
                        </div>
                        <div class="p-5 flex flex-col flex-grow text-left">
                            <div class="flex-grow">
                                <div class="flex justify-between items-center text-sm mb-2">
                                    <span class="font-semibold text-green-800 bg-green-100 px-2.5 py-1 rounded-full">
                                        Kegiatan
                                    </span>
                                    <span class="text-[--text-secondary]">
                                        5 hari lalu
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold font-lora text-[--text-dark] leading-tight group-hover:text-[--primary-blue] transition-colors line-clamp-3">
                                    Festival Seni Mahasiswa UNP 2024
                                </h3>
                                <p class="text-sm text-gray-500 mt-2 line-clamp-2">
                                    UKM Symphony akan mengadakan festival seni tahunan dengan penampilan paduan suara.
                                </p>
                            </div>
                            <div class="mt-4 pt-4 border-t border-[--border-color] flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-xs">S</span>
                                    </div>
                                    <span class="text-sm font-semibold text-[--text-secondary]">
                                        SYMPHONY
                                    </span>
                                </div>
                                <span class="text-[--primary-blue] font-semibold text-sm group-hover:text-[--accent-orange] transition-colors">
                                    Baca Selengkapnya
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md border border-[--border-color] overflow-hidden group hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer flex flex-col animate-fade-in">
                        <div class="relative h-56 bg-gray-200 flex-shrink-0">
                            <div class="w-full h-full bg-gradient-to-br from-[--accent-orange]/20 to-red-500/20 flex items-center justify-center">
                                <i class="fas fa-users text-white text-3xl"></i>
                            </div>
                        </div>
                        <div class="p-5 flex flex-col flex-grow text-left">
                            <div class="flex-grow">
                                <div class="flex justify-between items-center text-sm mb-2">
                                    <span class="font-semibold text-yellow-800 bg-yellow-100 px-2.5 py-1 rounded-full">
                                        Rekrutmen
                                    </span>
                                    <span class="text-[--text-secondary]">
                                        1 minggu lalu
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold font-lora text-[--text-dark] leading-tight group-hover:text-[--primary-blue] transition-colors line-clamp-3">
                                    Rekrutmen Anggota Baru HIMA Matematika
                                </h3>
                                <p class="text-sm text-gray-500 mt-2 line-clamp-2">
                                    HIMA Matematika membuka kesempatan bergabung untuk mahasiswa matematika.
                                </p>
                            </div>
                            <div class="mt-4 pt-4 border-t border-[--border-color] flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-gradient-to-br from-[--accent-orange] to-red-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-xs">M</span>
                                    </div>
                                    <span class="text-sm font-semibold text-[--text-secondary]">
                                        HIMA MAT
                                    </span>
                                </div>
                                <span class="text-[--primary-blue] font-semibold text-sm group-hover:text-[--accent-orange] transition-colors">
                                    Baca Selengkapnya
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('news.index') }}" class="group inline-flex items-center gap-3 bg-gradient-to-r from-[--accent-orange] to-orange-500 text-white font-bold py-4 px-10 rounded-2xl hover:from-orange-500 hover:to-orange-600 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl shadow-lg">
                        <span>Lihat Semua Berita</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="scroll-snap-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 h-full flex flex-col justify-center">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Fitur Utama</h2>
                <p class="text-lg text-gray-600">Semua yang Anda butuhkan untuk mengelola organisasi mahasiswa</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Manajemen Organisasi</h3>
                    <p class="text-gray-600">Kelola profil organisasi, anggota, dan struktur kepengurusan dengan mudah.</p>
                </div>

                <!-- Feature 2 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Pengelolaan Kegiatan</h3>
                    <p class="text-gray-600">Buat, kelola, dan publikasikan kegiatan organisasi secara terpadu.</p>
                </div>

                <!-- Feature 3 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Pengumuman</h3>
                    <p class="text-gray-600">Sampaikan informasi penting kepada seluruh anggota dengan cepat.</p>
                </div>

                <!-- Feature 4 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Berita & Artikel</h3>
                    <p class="text-gray-600">Bagikan prestasi dan kegiatan organisasi melalui berita terkini.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="scroll-snap-section bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 h-full flex flex-col justify-center">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Statistik Ormawa UNP</h2>
                <p class="text-lg text-gray-600">Angka-angka prestasi organisasi mahasiswa kita</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold text-blue-600 mb-2">50+</div>
                    <p class="text-gray-600">Organisasi Aktif</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-green-600 mb-2">200+</div>
                    <p class="text-gray-600">Kegiatan Tahunan</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-yellow-600 mb-2">5000+</div>
                    <p class="text-gray-600">Mahasiswa Aktif</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-purple-600 mb-2">100+</div>
                    <p class="text-gray-600">Prestasi Tahun Ini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="scroll-snap-section bg-gradient-primary">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 h-full flex items-center">
            <div class="text-center w-full">
                <h2 class="text-3xl font-bold text-white mb-4">Siap Bergabung?</h2>
                <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                    Temukan organisasi yang sesuai dengan minat dan bakat Anda.
                    Kembangkan potensi diri dan kontribusi untuk kemajuan kampus.
                </p>
                @guest
                    <a href="{{ route('register') }}" class="bg-white text-blue-600 font-bold py-3 px-8 rounded-md hover:bg-gray-100 transition-colors text-lg">
                        Daftar Sekarang
                    </a>
                @else
                    <a href="{{ route('organizations.index') }}" class="bg-white text-blue-600 font-bold py-3 px-8 rounded-md hover:bg-gray-100 transition-colors text-lg">
                        Jelajahi Organisasi
                    </a>
                @endguest
            </div>
        </div>
    </div>
</div>
@endsection
