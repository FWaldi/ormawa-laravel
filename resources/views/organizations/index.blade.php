@extends('layouts.app')

@section('title', 'Pusat Informasi Ormawa')

@section('content')
<div class="animate-fade-in">
    <div class="max-w-screen-2xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-bold font-lora text-[--primary-blue]">
                Pusat Informasi Ormawa
            </h1>
            <p class="mt-3 text-lg text-[--text-secondary] max-w-3xl mx-auto">
                Jelajahi, saring, dan temukan komunitas yang tepat untuk Anda di
                antara beragam organisasi mahasiswa kami.
            </p>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg border border-[--border-color] mb-10 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <div class="md:col-span-2 lg:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Kategori
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button id="category-all" class="px-4 py-2 text-sm rounded-md transition-colors bg-[--primary-blue] text-white">
                            Semua
                        </button>
                        @foreach(['BEM', 'UKM', 'HIMA'] as $category)
                            <button id="category-{{ strtolower($category) }}" class="px-4 py-2 text-sm rounded-md transition-colors bg-gray-200 hover:bg-gray-300">
                                {{ $category }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="w-full">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Fakultas
                    </label>
                    <select id="faculty-filter" class="w-full p-2 border border-gray-300 rounded-md bg-white">
                        <option value="">Semua Fakultas</option>
                        @foreach($faculties ?? [] as $faculty)
                            <option value="{{ $faculty }}">{{ $faculty }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Urutkan
                    </label>
                    <select id="sort-filter" class="w-full p-2 border border-gray-300 rounded-md bg-white">
                        <option value="asc">Nama A-Z</option>
                        <option value="desc">Nama Z-A</option>
                    </select>
                </div>
                <div class="w-full">
                    <button id="reset-filters" class="w-full flex items-center justify-center gap-2 text-sm text-red-600 font-semibold bg-red-50 hover:bg-red-100 p-2.5 rounded-md">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reset Filter
                    </button>
                </div>
            </div>
            <div class="border-t pt-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Cari berdasarkan Kata Kunci
                </label>
                <input
                    id="search-input"
                    type="text"
                    placeholder="Cari di nama, deskripsi, visi, misi, divisi..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[--primary-blue]"
                />
            </div>
        </div>

        <div>
            @if($organizations->isEmpty())
                <div class="text-center py-16">
                    <h3 class="text-xl font-semibold text-gray-600">
                        Tidak ada organisasi ditemukan.
                    </h3>
                    <p class="text-gray-500 mt-2">
                        Coba sesuaikan filter atau kata kunci pencarian Anda.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
                    @foreach($organizations as $organization)
                        <div class="org-card bg-white rounded-lg shadow-lg border border-[--border-color] overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
                             data-category="{{ $organization->category ?? 'Unknown' }}"
                             data-faculty="{{ $organization->faculty ?? '' }}"
                             data-members="{{ $organization->members_count ?? 0 }}"
                             data-activities="{{ $organization->activities_count ?? 0 }}">
                            @if($organization->logo)
                                <img src="{{ Storage::url($organization->logo) }}" alt="{{ $organization->name }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gradient-to-br from-[--primary-blue] to-blue-600 flex items-center justify-center">
                                    <span class="text-white text-4xl font-bold">{{ substr($organization->name, 0, 1) }}</span>
                                </div>
                            @endif

                            <div class="p-6">
                             <div class="flex justify-between items-start mb-2">
                                     <h3 class="text-xl font-bold font-lora text-[--primary-blue]">{{ $organization->name }}</h3>
                                     <button class="compare-btn text-gray-400 hover:text-[--primary-blue] transition-colors" data-org-id="{{ $organization->id }}">
                                         <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                         </svg>
                                     </button>
                                 </div>
                                <p class="text-sm text-[--text-secondary] mb-3">{{ $organization->category }}</p>

                                @if($organization->description)
                                    <p class="text-gray-700 mb-4 line-clamp-3">{{ Str::limit($organization->description, 120) }}</p>
                                @endif

                                <div class="flex justify-between items-center text-sm text-[--text-secondary] mb-4">
                                    <span>{{ $organization->members_count ?? 0 }} Anggota</span>
                                    <span>{{ $organization->activities_count ?? 0 }} Kegiatan</span>
                                </div>

                                <a href="{{ route('organizations.show', $organization->id) }}"
                                   class="w-full block text-center bg-[--accent-orange] text-[--text-dark] font-bold py-3 px-4 rounded-md hover:bg-orange-500 transition-all duration-300 transform hover:scale-105">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($organizations->hasPages())
                    <div class="flex justify-center items-center mt-10 space-x-1 sm:space-x-2 flex-wrap">
                        @if($organizations->onFirstPage())
                            <button disabled class="px-3 py-1 rounded-md bg-gray-200 border border-gray-300 opacity-50 cursor-not-allowed">
                                &laquo;
                            </button>
                            <button disabled class="px-3 py-1 rounded-md bg-gray-200 border border-gray-300 opacity-50 cursor-not-allowed">
                                &lsaquo;
                            </button>
                        @else
                            <a href="{{ $organizations->url(1) }}" class="px-3 py-1 rounded-md bg-white border border-gray-300 hover:bg-gray-50">
                                &laquo;
                            </a>
                            <a href="{{ $organizations->previousPageUrl() }}" class="px-3 py-1 rounded-md bg-white border border-gray-300 hover:bg-gray-50">
                                &lsaquo;
                            </a>
                        @endif

                        <span class="px-3 py-1 text-sm">
                            Halaman {{ $organizations->currentPage() }} dari {{ $organizations->lastPage() }}
                        </span>

                        @if($organizations->hasMorePages())
                            <a href="{{ $organizations->nextPageUrl() }}" class="px-3 py-1 rounded-md bg-white border border-gray-300 hover:bg-gray-50">
                                &rsaquo;
                            </a>
                            <a href="{{ $organizations->url($organizations->lastPage()) }}" class="px-3 py-1 rounded-md bg-white border border-gray-300 hover:bg-gray-50">
                                &raquo;
                            </a>
                        @else
                            <button disabled class="px-3 py-1 rounded-md bg-gray-200 border border-gray-300 opacity-50 cursor-not-allowed">
                                &rsaquo;
                            </button>
                            <button disabled class="px-3 py-1 rounded-md bg-gray-200 border border-gray-300 opacity-50 cursor-not-allowed">
                                &raquo;
                            </button>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Comparison Bar -->
    <div id="comparison-bar" class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg p-4 hidden z-40">
        <div class="max-w-screen-2xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="font-semibold text-gray-700">Membandingkan:</span>
                <div id="compared-orgs" class="flex gap-2"></div>
            </div>
            <div class="flex gap-2">
                <button id="compare-btn" class="bg-[--primary-blue] text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Bandingkan
                </button>
                <button id="clear-compare" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors">
                    Hapus Semua
                </button>
            </div>
        </div>
    </div>

    <!-- Comparison Modal -->
    <div id="comparison-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-6xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Perbandingan Organisasi</h2>
                        <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div id="comparison-content">
                        <!-- Comparison content will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let comparedOrgs = [];
    const maxCompare = 3;

    // Filter functionality
    const categoryButtons = document.querySelectorAll('[id^="category-"]');
    const facultyFilter = document.getElementById('faculty-filter');
    const sortFilter = document.getElementById('sort-filter');
    const searchInput = document.getElementById('search-input');
    const resetFilters = document.getElementById('reset-filters');

    function applyFilters() {
        const selectedCategory = document.querySelector('[id^="category-"].bg-blue-600')?.id.replace('category-', '') || '';
        const selectedFaculty = facultyFilter.value;
        const selectedSort = sortFilter.value;
        const searchTerm = searchInput.value.toLowerCase();

        const orgCards = document.querySelectorAll('.org-card');

        orgCards.forEach(card => {
            const category = card.dataset.category;
            const faculty = card.dataset.faculty;
            const name = card.dataset.name.toLowerCase();
            const description = card.dataset.description.toLowerCase();

            const matchesCategory = !selectedCategory || category === selectedCategory;
            const matchesFaculty = !selectedFaculty || faculty === selectedFaculty;
            const matchesSearch = !searchTerm || name.includes(searchTerm) || description.includes(searchTerm);

            if (matchesCategory && matchesFaculty && matchesSearch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });

        // Sort cards
        const container = document.querySelector('.grid.grid-cols-1.sm\\:grid-cols-2.lg\\:grid-cols-3.xl\\:grid-cols-4');
        const cards = Array.from(container.children);

        cards.sort((a, b) => {
            const nameA = a.dataset.name.toLowerCase();
            const nameB = b.dataset.name.toLowerCase();

            if (selectedSort === 'asc') {
                return nameA.localeCompare(nameB);
            } else {
                return nameB.localeCompare(nameA);
            }
        });

        cards.forEach(card => container.appendChild(card));
    }

    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            categoryButtons.forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-200', 'hover:bg-gray-300');
            });
            this.classList.remove('bg-gray-200', 'hover:bg-gray-300');
            this.classList.add('bg-blue-600', 'text-white');
            applyFilters();
        });
    });

    facultyFilter.addEventListener('change', applyFilters);
    sortFilter.addEventListener('change', applyFilters);
    searchInput.addEventListener('input', applyFilters);

    resetFilters.addEventListener('click', function() {
        categoryButtons.forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white');
            btn.classList.add('bg-gray-200', 'hover:bg-gray-300');
        });
        document.getElementById('category-all').classList.add('bg-blue-600', 'text-white');
        document.getElementById('category-all').classList.remove('bg-gray-200', 'hover:bg-gray-300');
        facultyFilter.value = '';
        sortFilter.value = 'asc';
        searchInput.value = '';
        applyFilters();
    });

    // Comparison functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.compare-btn')) {
            const button = e.target.closest('.compare-btn');
            const orgId = button.dataset.orgId;
            const orgCard = button.closest('.org-card');
            const orgName = orgCard.dataset.name;
            const orgCategory = orgCard.dataset.category;

            if (comparedOrgs.find(org => org.id === orgId)) {
                comparedOrgs = comparedOrgs.filter(org => org.id !== orgId);
                button.classList.remove('text-blue-600');
            } else {
                if (comparedOrgs.length >= maxCompare) {
                    alert(`Anda hanya dapat membandingkan maksimal ${maxCompare} organisasi.`);
                    return;
                }
                comparedOrgs.push({ id: orgId, name: orgName, category: orgCategory });
                button.classList.add('text-blue-600');
            }

            updateComparisonBar();
        }
    });

    function updateComparisonBar() {
        const bar = document.getElementById('comparison-bar');
        const comparedOrgsDiv = document.getElementById('compared-orgs');

        if (comparedOrgs.length > 0) {
            bar.classList.remove('hidden');
            comparedOrgsDiv.innerHTML = comparedOrgs.map(org => `
                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm">
                    ${org.name}
                    <button class="ml-1 text-blue-600 hover:text-blue-800 remove-compare" data-org-id="${org.id}">×</button>
                </span>
            `).join('');
        } else {
            bar.classList.add('hidden');
        }
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-compare')) {
            const orgId = e.target.closest('.remove-compare').dataset.orgId;
            comparedOrgs = comparedOrgs.filter(org => org.id !== orgId);
            document.querySelector(`[data-org-id="${orgId}"]`).classList.remove('text-blue-600');
            updateComparisonBar();
        }
    });

    document.getElementById('clear-compare').addEventListener('click', function() {
        comparedOrgs.forEach(org => {
            document.querySelector(`[data-org-id="${org.id}"]`).classList.remove('text-blue-600');
        });
        comparedOrgs = [];
        updateComparisonBar();
    });

    document.getElementById('compare-btn').addEventListener('click', function() {
        showComparisonModal();
    });

    function showComparisonModal() {
        const modal = document.getElementById('comparison-modal');
        const content = document.getElementById('comparison-content');

        content.innerHTML = `
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-300 p-4 text-left">Kriteria</th>
                            ${comparedOrgs.map(org => `<th class="border border-gray-300 p-4 text-center">${org.name}</th>`).join('')}
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-gray-300 p-4 font-semibold">Kategori</td>
                            ${comparedOrgs.map(org => `<td class="border border-gray-300 p-4 text-center">${org.category}</td>`).join('')}
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="border border-gray-300 p-4 font-semibold">Anggota</td>
                            ${comparedOrgs.map(org => `<td class="border border-gray-300 p-4 text-center">${org.members || 0}</td>`).join('')}
                        </tr>
                        <tr>
                            <td class="border border-gray-300 p-4 font-semibold">Kegiatan</td>
                            ${comparedOrgs.map(org => `<td class="border border-gray-300 p-4 text-center">${org.activities || 0}</td>`).join('')}
                        </tr>
                    </tbody>
                </table>
            </div>
        `;

        modal.classList.remove('hidden');
    }

    document.getElementById('close-modal').addEventListener('click', function() {
        document.getElementById('comparison-modal').classList.add('hidden');
    });

    // Add data attributes to organization cards for filtering
    document.querySelectorAll('.org-card').forEach(card => {
        const name = card.querySelector('h3').textContent.toLowerCase();
        const category = card.dataset.category;
        const faculty = card.dataset.faculty || '';
        const description = card.querySelector('p.line-clamp-3')?.textContent.toLowerCase() || '';

        card.dataset.name = name;
        card.dataset.description = description;
    });
});
</script>
@endsection