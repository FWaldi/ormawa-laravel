@extends('layouts.app')

@section('title', 'Edit Pengumuman - ' . $announcement->title . ' - Organisasi Mahasiswa UNP')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('announcements.show', $announcement) }}" class="text-gray-500 hover:text-gray-700 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Pengumuman</h1>
                    <p class="mt-2 text-gray-600">Perbarui informasi pengumuman yang ada</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form action="{{ route('announcements.update', $announcement) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Pengumuman <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $announcement->title) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[--primary-blue] focus:border-transparent"
                           placeholder="Masukkan judul pengumuman">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div class="mb-6">
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori
                    </label>
                    <input type="text" 
                           id="category" 
                           name="category" 
                           value="{{ old('category', $announcement->category) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[--primary-blue] focus:border-transparent"
                           placeholder="Contoh: Akademik, Kegiatan, Beasiswa">
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div class="mb-6">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                        Isi Pengumuman <span class="text-red-500">*</span>
                    </label>
                    <textarea id="content" 
                              name="content" 
                              rows="8" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[--primary-blue] focus:border-transparent"
                              placeholder="Tulis isi pengumuman di sini...">{{ old('content', $announcement->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Image -->
                @if($announcement->image)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Gambar Saat Ini
                        </label>
                        <div class="flex items-center space-x-4">
                            <img src="{{ asset('storage/' . $announcement->image) }}" 
                                 alt="{{ $announcement->title }}" 
                                 class="h-32 w-32 object-cover rounded-md border border-gray-200">
                            <div class="text-sm text-gray-500">
                                <p>Gambar saat ini akan diganti jika Anda mengunggah gambar baru</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- New Image -->
                <div class="mb-6">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                        Unggah Gambar Baru (Opsional)
                    </label>
                    <div class="flex items-center space-x-4">
                        <input type="file" 
                               id="image" 
                               name="image" 
                               accept="image/*"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[--primary-blue] focus:border-transparent">
                        <div id="imagePreview" class="hidden">
                            <img src="" alt="Preview" class="h-20 w-20 object-cover rounded-md">
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Format: JPEG, PNG, JPG, GIF. Maksimal: 2MB</p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Pinned -->
                <div class="mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_pinned" 
                               name="is_pinned" 
                               value="1"
                               {{ $announcement->is_pinned ? 'checked' : '' }}
                               class="h-4 w-4 text-[--primary-blue] focus:ring-[--primary-blue] border-gray-300 rounded">
                        <label for="is_pinned" class="ml-2 block text-sm text-gray-700">
                            Sematkan pengumuman ini
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Pengumuman yang disematkan akan muncul di bagian atas</p>
                </div>

                <!-- Meta Information -->
                <div class="mb-6 p-4 bg-gray-50 rounded-md">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Informasi Meta</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <span class="font-medium">Dibuat:</span> {{ $announcement->created_at->format('d F Y H:i') }}
                        </div>
                        <div>
                            <span class="font-medium">Terakhir diperbarui:</span> {{ $announcement->updated_at->format('d F Y H:i') }}
                        </div>
                        @if($announcement->creator)
                            <div>
                                <span class="font-medium">Pembuat:</span> {{ $announcement->creator->name }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex space-x-3">
                        <a href="{{ route('announcements.show', $announcement) }}" 
                           class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Batal
                        </a>
                        <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini? Tindakan ini tidak dapat dibatalkan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 text-red-700 bg-red-100 rounded-md hover:bg-red-200 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Hapus
                            </button>
                        </form>
                    </div>
                    <button type="submit" 
                            class="px-6 py-2 bg-[--primary-blue] text-white font-medium rounded-md hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[--primary-blue]">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Initialize CKEditor
let editor;

ClassicEditor
    .create(document.querySelector('#content'), {
        toolbar: {
            items: [
                'heading', '|',
                'bold', 'italic', 'underline', 'strikethrough', '|',
                'bulletedList', 'numberedList', '|',
                'outdent', 'indent', '|',
                'link', 'blockQuote', '|',
                'undo', 'redo'
            ]
        },
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
            ]
        },
        placeholder: 'Tulis isi pengumuman di sini...',
        language: 'id'
    })
    .then(newEditor => {
        editor = newEditor;
        
        // Update the hidden textarea when form is submitted
        const form = document.querySelector('form');
        form.addEventListener('submit', () => {
            const content = editor.getData();
            document.querySelector('#content').value = content;
        });
    })
    .catch(error => {
        console.error('CKEditor initialization error:', error);
    });

// Image preview functionality
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
});
</script>
@endsection