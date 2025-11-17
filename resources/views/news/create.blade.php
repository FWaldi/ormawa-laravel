@extends('layouts.app')

@section('title', 'Buat Berita Baru - Organisasi Mahasiswa UNP')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 font-lora">Buat Berita Baru</h1>
                    <p class="mt-2 text-gray-600">Bagikan informasi terbaru dari organisasi Anda</p>
                </div>
                <a href="{{ route('news.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-md p-6 md:p-8">
            <form method="POST" action="{{ route('news.store') }}" enctype="multipart/form-data" x-data="newsForm()">
                @csrf
                
                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Berita <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           required
                           value="{{ old('title') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[--primary-blue] focus:border-transparent"
                           placeholder="Masukkan judul berita yang menarik...">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Organization -->
                <div class="mb-6">
                    <label for="organization_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Organisasi <span class="text-red-500">*</span>
                    </label>
                    <select id="organization_id" 
                            name="organization_id" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[--primary-blue] focus:border-transparent">
                        <option value="">Pilih Organisasi</option>
                        @foreach($organizations as $organization)
                            <option value="{{ $organization->id }}" 
                                    @if(old('organization_id') == $organization->id) selected @endif>
                                {{ $organization->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('organization_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image Upload -->
                <div class="mb-6">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                        Gambar Cover
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors"
                         x-data="{ imagePreview: null }">
                        <div class="space-y-1 text-center">
                            <!-- Image Preview -->
                            <div x-show="imagePreview" class="mb-4">
                                <img :src="imagePreview" 
                                     alt="Preview" 
                                     class="mx-auto h-32 w-auto rounded-lg shadow-md">
                            </div>
                            
                            <!-- Upload Icon -->
                            <svg x-show="!imagePreview" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            
                            <div class="flex text-sm text-gray-600">
                                <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-[--primary-blue] hover:text-blue-700 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-[--primary-blue]">
                                    <span>Upload file</span>
                                    <input id="image" 
                                           name="image" 
                                           type="file" 
                                           accept="image/*"
                                           @change="handleImageUpload($event)"
                                           class="sr-only">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                PNG, JPG, GIF hingga 2MB
                            </p>
                        </div>
                    </div>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div class="mb-6">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                        Konten Berita <span class="text-red-500">*</span>
                    </label>
                    <div id="editor-container" class="border border-gray-300 rounded-lg overflow-hidden">
                        <textarea id="content" 
                                  name="content" 
                                  required
                                  rows="12"
                                  class="w-full px-4 py-3 border-0 focus:ring-0 resize-none"
                                  placeholder="Tulis konten berita di sini...">{{ old('content') }}</textarea>
                    </div>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Publish Options -->
                <div class="mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_published" 
                               name="is_published" 
                               value="1"
                               class="h-4 w-4 text-[--primary-blue] focus:ring-[--primary-blue] border-gray-300 rounded">
                        <label for="is_published" class="ml-2 block text-sm text-gray-700">
                            Publikasikan sekarang
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Jika tidak dicentang, berita akan disimpan sebagai draft.
                    </p>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('news.index') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-[--primary-blue] text-white rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-[--primary-blue] focus:ring-offset-2">
                        <span x-show="!isSubmitting">Simpan Berita</span>
                        <span x-show="isSubmitting" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rich Text Editor Script -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
<script>
function newsForm() {
    return {
        isSubmitting: false,
        editor: null,
        
        init() {
            // Initialize CKEditor
            ClassicEditor
                .create(document.querySelector('#content'), {
                    toolbar: {
                        items: [
                            'heading', '|',
                            'bold', 'italic', 'link', '|',
                            'bulletedList', 'numberedList', '|',
                            'outdent', 'indent', '|',
                            'imageUpload', 'blockQuote', 'insertTable', '|',
                            'undo', 'redo'
                        ]
                    },
                    image: {
                        toolbar: [
                            'imageTextAlternative',
                            'imageStyle:full',
                            'imageStyle:side'
                        ]
                    },
                    table: {
                        contentToolbar: [
                            'tableColumn',
                            'tableRow',
                            'mergeTableCells'
                        ]
                    },
                    placeholder: 'Tulis konten berita di sini...'
                })
                .then(editor => {
                    this.editor = editor;
                })
                .catch(error => {
                    console.error('CKEditor initialization error:', error);
                });
        },
        
        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        
        submit() {
            this.isSubmitting = true;
        }
    }
}

// Handle form submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            }
        });
    }
});
</script>
@endsection