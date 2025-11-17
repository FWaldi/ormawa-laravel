@extends('layouts.app')

@section('title', $news->title . ' - Organisasi Mahasiswa UNP')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Image -->
    @if($news->image)
        <div class="relative h-96 bg-gray-900">
            <img src="{{ Storage::url($news->image) }}" 
                 alt="{{ $news->title }}"
                 class="w-full h-full object-cover opacity-90">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            
            <!-- Breadcrumb -->
            <div class="absolute top-0 left-0 right-0 z-10">
                <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2 text-white/80 text-sm">
                            <li>
                                <a href="{{ route('home') }}" class="hover:text-white transition-colors">
                                    Beranda
                                </a>
                            </li>
                            <li>
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </li>
                            <li>
                                <a href="{{ route('news.index') }}" class="hover:text-white transition-colors">
                                    Berita
                                </a>
                            </li>
                            <li>
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </li>
                            <li class="text-white font-medium truncate max-w-xs">
                                {{ Str::limit($news->title, 50) }}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    @endif

    <!-- Article Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20">
        <article class="bg-white rounded-xl shadow-xl overflow-hidden">
            <!-- Article Header -->
            <header class="p-8 pb-6">
                <!-- Organization Badge -->
                <div class="flex items-center justify-between mb-4">
                    <a href="{{ route('organizations.show', $news->organization) }}" 
                       class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-[--primary-blue] text-white hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        {{ $news->organization->name }}
                    </a>
                    
                    <!-- Actions (for author/admin) -->
                    @auth
                        @if(Auth::user()->is_admin || Auth::id() === $news->created_by)
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('news.edit', $news) }}" 
                                   class="inline-flex items-center px-3 py-1 text-sm border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('news.destroy', $news) }}" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus berita ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-1 text-sm border border-red-300 rounded-lg text-red-600 bg-white hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>

                <!-- Title -->
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-lora mb-4">
                    {{ $news->title }}
                </h1>

                <!-- Meta Information -->
                <div class="flex flex-wrap items-center text-sm text-gray-600 space-y-2 sm:space-y-0 sm:space-x-6">
                    <!-- Author -->
                    @if($news->creator)
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[--accent-orange] to-orange-600 flex items-center justify-center text-white font-bold text-xs mr-2">
                                {{ strtoupper(substr($news->creator->name, 0, 1)) }}
                            </div>
                            <span>
                                oleh <span class="font-medium text-gray-900">{{ $news->creator->name }}</span>
                            </span>
                        </div>
                    @endif

                    <!-- Published Date -->
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $news->published_at->format('d F Y') }}
                    </div>

                    <!-- Reading Time (estimated) -->
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ ceil(str_word_count(strip_tags($news->content)) / 200) }} menit baca
                    </div>
                </div>
            </header>

            <!-- Article Body -->
            <div class="px-8 pb-8">
                <div class="prose prose-lg max-w-none">
                    {!! $news->content !!}
                </div>
            </div>

            <!-- Article Footer -->
            <footer class="px-8 py-6 bg-gray-50 border-t">
                <!-- Share Section -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Bagikan artikel ini:</h3>
                    <div class="flex items-center space-x-3">
                        <!-- Copy Link -->
                        <button onclick="copyToClipboard()" 
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Salin Link
                        </button>
                        
                        <!-- WhatsApp Share -->
                        <a href="https://wa.me/?text={{ urlencode(route('news.show', $news)) }}" 
                           target="_blank"
                           class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.149-.67.149-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.123-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                            WhatsApp
                        </a>
                    </div>
                </div>

                <!-- Organization Link -->
                <div class="text-center">
                    <p class="text-sm text-gray-600 mb-3">
                        Artikel ini dipublikasikan oleh:
                    </p>
                    <a href="{{ route('organizations.show', $news->organization) }}" 
                       class="inline-flex items-center px-6 py-3 bg-[--primary-blue] text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Lihat {{ $news->organization->name }}
                    </a>
                </div>
            </footer>
        </article>

        <!-- Related News -->
        @if($relatedNews->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 font-lora mb-6">Berita Terkait</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($relatedNews as $related)
                        <article class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                            @if($related->image)
                                <div class="aspect-w-16 aspect-h-9">
                                    <img src="{{ Storage::url($related->image) }}" 
                                         alt="{{ $related->title }}"
                                         class="w-full h-32 object-cover">
                                </div>
                            @else
                                <div class="w-full h-32 bg-gradient-to-br from-[--primary-blue] to-blue-600 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                    </svg>
                                </div>
                            @endif
                            
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs text-[--primary-blue] font-medium">
                                        {{ $related->organization->name }}
                                    </span>
                                    <time class="text-xs text-gray-500">
                                        {{ $related->published_at->format('d M Y') }}
                                    </time>
                                </div>
                                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                                    <a href="{{ route('news.show', $related) }}" 
                                       class="hover:text-[--primary-blue] transition-colors">
                                        {{ $related->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600 line-clamp-2">
                                    {{ Str::limit(strip_tags($related->content), 80) }}
                                </p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Copy to Clipboard Function -->
<script>
function copyToClipboard() {
    const url = window.location.href;
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
            showNotification('Link berhasil disalin!');
        }).catch(() => {
            fallbackCopyTextToClipboard(url);
        });
    } else {
        fallbackCopyTextToClipboard(url);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showNotification('Link berhasil disalin!');
    } catch (err) {
        showNotification('Gagal menyalin link', 'error');
    }
    
    document.body.removeChild(textArea);
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>
@endsection