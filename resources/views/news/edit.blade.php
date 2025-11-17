@extends('layouts.app')

@section('title', 'Edit Berita - ' . $news->title . ' - Organisasi Mahasiswa UNP')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 font-lora">Edit Berita</h1>
                    <p class="mt-2 text-gray-600">Perbarui informasi berita yang ada</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('news.show', $news) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Lihat
                    </a>
                    <a href="{{ route('news.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-md p-6 md:p-8">
            <form method="POST" action="{{ route('news.update', $news) }}" enctype="multipart/form-data" x-data="newsEditForm()">
                @csrf
                @method('PUT')
                
                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Berita <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           required
                           value="{{ old('title', $news->title) }}"
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
                                    @if(old('organization_id', $news->organization_id) == $organization->id) selected @endif>
                                {{ $organization->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('organization_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Image & Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Gambar Cover
                    </label>
                    
                    @if($news->image)
                        <div class="mb-4">
                            <div class="relative inline-block">
                                <img src="{{ Storage::url($news->image) }}" 
                                     alt="Current image"
                                     class="h-32 w-auto rounded-lg shadow-md">
                                <div class="absolute top-2 right-2">
                                    <button type="button" 
                                            @click="removeCurrentImage()"
                                            class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors"
                                            title="Hapus gambar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-600">Gambar saat ini</p>
                        </div>
                    @endif

                    <!-- Upload New Image -->
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
                                    <span>Upload file baru</span>
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
                    
                    <!-- Remove Image Checkbox (hidden by default) -->
                    <input type="hidden" name="remove_image" id="remove_image" value="0">
                    
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
                                  placeholder="Tulis konten berita di sini...">{{ old('content', $news->content) }}</textarea>
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
                               @if(old('is_published', $news->is_published)) checked @endif
                               class="h-4 w-4 text-[--primary-blue] focus:ring-[--primary-blue] border-gray-300 rounded">
                        <label for="is_published" class="ml-2 block text-sm text-gray-700">
                            Publikasikan sekarang
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Jika tidak dicentang, berita akan disimpan sebagai draft.
                    </p>
                </div>

                <!-- Status Information -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Informasi Status:</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-700">
                                <li>Status saat ini: {{ $news->is_published ? 'Dipublikasikan' : 'Draft' }}</li>
                                <li>Dibuat: {{ $news->created_at->format('d F Y, H:i') }}</li>
                                @if($news->updated_at != $news->created_at)
                                    <li>Terakhir diperbarui: {{ $news->updated_at->format('d F Y, H:i') }}</li>
                                @endif
                                @if($news->published_at)
                                    <li>Dipublikasikan: {{ $news->published_at->format('d F Y, H:i') }}</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('news.show', $news) }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-[--primary-blue] text-white rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-[--primary-blue] focus:ring-offset-2">
                        <span x-show="!isSubmitting">Perbarui Berita</span>
                        <span x-show="isSubmitting" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memperbarui...
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
function newsEditForm() {
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
        
        removeCurrentImage() {
            if (confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
                document.getElementById('remove_image').value = '1';
                // Hide current image
                const currentImageContainer = document.querySelector('.relative.inline-block').parentElement.parentElement;
                if (currentImageContainer) {
                    currentImageContainer.style.display = 'none';
                }
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