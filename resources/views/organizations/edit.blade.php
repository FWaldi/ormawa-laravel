@extends('layouts.app')

@section('title', 'Edit Organization')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Organization</h1>
        <a href="{{ route('organizations.show', $organization->id) }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Organization
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('organizations.update', $organization->id) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
        @csrf
        @method('PUT')
        
        <div class="mb-6">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                Organization Name *
            </label>
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ old('name', $organization->name) }}"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                   required>
        </div>

        <div class="mb-6">
            <label for="type" class="block text-gray-700 text-sm font-bold mb-2">
                Organization Type *
            </label>
            <select id="type" 
                    name="type" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
                <option value="">Select Type</option>
                <option value="Student Organization" {{ old('type', $organization->type) == 'Student Organization' ? 'selected' : '' }}>Student Organization</option>
                <option value="Academic Club" {{ old('type', $organization->type) == 'Academic Club' ? 'selected' : '' }}>Academic Club</option>
                <option value="Sports Club" {{ old('type', $organization->type) == 'Sports Club' ? 'selected' : '' }}>Sports Club</option>
                <option value="Arts & Culture" {{ old('type', $organization->type) == 'Arts & Culture' ? 'selected' : '' }}>Arts & Culture</option>
                <option value="Community Service" {{ old('type', $organization->type) == 'Community Service' ? 'selected' : '' }}>Community Service</option>
                <option value="Professional Association" {{ old('type', $organization->type) == 'Professional Association' ? 'selected' : '' }}>Professional Association</option>
            </select>
        </div>

        <div class="mb-6">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">
                Description
            </label>
            <textarea id="description" 
                      name="description" 
                      rows="4"
                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('description', $organization->description) }}</textarea>
        </div>

        <div class="mb-6">
            <label for="contact" class="block text-gray-700 text-sm font-bold mb-2">
                Contact Information
            </label>
            <input type="text" 
                   id="contact" 
                   name="contact" 
                   value="{{ old('contact', $organization->contact) }}"
                   placeholder="Email, phone, or office location"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-6">
            <label for="logo" class="block text-gray-700 text-sm font-bold mb-2">
                Organization Logo
            </label>
            <input type="file" 
                   id="logo" 
                   name="logo" 
                   accept="image/*"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <p class="text-gray-500 text-sm mt-1">Maximum file size: 2MB. Allowed formats: JPEG, PNG, JPG, GIF</p>
            
            @if($organization->logo)
                <div class="mt-3">
                    <p class="text-sm text-gray-600 mb-2">Current logo:</p>
                    <img src="{{ Storage::url($organization->logo) }}" alt="Current logo" class="h-20 w-20 object-cover rounded">
                </div>
            @endif
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Social Media Links</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="social_media[facebook]" class="block text-gray-700 text-sm font-bold mb-2">
                        Facebook
                    </label>
                    <input type="url" 
                           id="social_media[facebook]" 
                           name="social_media[facebook]" 
                           value="{{ old('social_media.facebook', $organization->social_media['facebook'] ?? '') }}"
                           placeholder="https://facebook.com/..."
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div>
                    <label for="social_media[twitter]" class="block text-gray-700 text-sm font-bold mb-2">
                        Twitter
                    </label>
                    <input type="url" 
                           id="social_media[twitter]" 
                           name="social_media[twitter]" 
                           value="{{ old('social_media.twitter', $organization->social_media['twitter'] ?? '') }}"
                           placeholder="https://twitter.com/..."
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div>
                    <label for="social_media[instagram]" class="block text-gray-700 text-sm font-bold mb-2">
                        Instagram
                    </label>
                    <input type="url" 
                           id="social_media[instagram]" 
                           name="social_media[instagram]" 
                           value="{{ old('social_media.instagram', $organization->social_media['instagram'] ?? '') }}"
                           placeholder="https://instagram.com/..."
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div>
                    <label for="social_media[linkedin]" class="block text-gray-700 text-sm font-bold mb-2">
                        LinkedIn
                    </label>
                    <input type="url" 
                           id="social_media[linkedin]" 
                           name="social_media[linkedin]" 
                           value="{{ old('social_media.linkedin', $organization->social_media['linkedin'] ?? '') }}"
                           placeholder="https://linkedin.com/..."
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('organizations.show', $organization->id) }}" class="text-gray-600 hover:text-gray-900">
                Cancel
            </a>
            <div class="space-x-2">
                @if(auth()->user()->isAdmin())
                    <form action="{{ route('organizations.destroy', $organization->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this organization? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Delete Organization
                        </button>
                    </form>
                @endif
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Organization
                </button>
            </div>
        </div>
    </form>
</div>
@endsection