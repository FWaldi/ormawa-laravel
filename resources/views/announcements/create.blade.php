@extends('layouts.app')

@section('title', 'Buat Pengumuman - Organisasi Mahasiswa UNP')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('announcements.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Buat Pengumuman Baru</h1>
                    <p class="mt-2 text-gray-600">Buat dan publikasikan pengumuman untuk komunitas</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form action="{{ route('announcements.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf

                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Pengumuman <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}"
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
                           value="{{ old('category') }}"
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
                              placeholder="Tulis isi pengumuman di sini...">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image -->
                <div class="mb-6">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                        Gambar (Opsional)
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
                               class="h-4 w-4 text-[--primary-blue] focus:ring-[--primary-blue] border-gray-300 rounded">
                        <label for="is_pinned" class="ml-2 block text-sm text-gray-700">
                            Sematkan pengumuman ini
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Pengumuman yang disematkan akan muncul di bagian atas</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('announcements.index') }}" 
                       class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-[--primary-blue] text-white font-medium rounded-md hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[--primary-blue]">
                        Publikasikan Pengumuman
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