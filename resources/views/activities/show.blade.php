@extends('layouts.app')

@section('title', $activity->title)

@section('content')
<div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $activity->title }}</h1>
                <p class="text-gray-600">Activity Details</p>
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $activity->status == 'published' ? 'bg-green-100 text-green-800' : ($activity->status == 'draft' ? 'bg-yellow-100 text-yellow-800' : ($activity->status == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                        {{ ucfirst($activity->status) }}
                    </span>
                    <span>Created {{ $activity->created_at->format('M j, Y') }}</span>
                    @if($activity->updated_at != $activity->created_at)
                        <span>Updated {{ $activity->updated_at->format('M j, Y') }}</span>
                    @endif
                </div>
            </div>
            
            @if(auth()->check() && (auth()->id() == $activity->created_by || auth()->user()->is_admin))
                <div class="flex space-x-2">
                    <a href="{{ route('activities.edit', $activity) }}" 
                       class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Edit Activity
                    </a>
                    <form method="POST" action="{{ route('activities.destroy', $activity) }}" onsubmit="return confirm('Are you sure you want to delete this activity?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Delete
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Images -->
                @if($activity->images && count($activity->images) > 0)
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Gallery</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($activity->images as $index => $image)
                                <div class="relative group">
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         alt="{{ $activity->title }} - Image {{ $index + 1 }}" 
                                         class="w-full h-64 object-cover rounded-lg">
                                    
                                    @if(auth()->check() && (auth()->id() == $activity->created_by || auth()->user()->is_admin))
                                        <form method="POST" action="{{ route('activities.removeImage', $activity) }}" 
                                              class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            @csrf
                                            <input type="hidden" name="image_index" value="{{ $index }}">
                                            <button type="submit" 
                                                    class="p-2 bg-red-600 text-white rounded-full hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                                    onclick="return confirm('Remove this image?')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Description -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Description</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $activity->description }}</p>
                    </div>
                </div>

                <!-- Status Update (for creators/admins) -->
                @if(auth()->check() && (auth()->id() == $activity->created_by || auth()->user()->is_admin))
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Update Status</h2>
                        <form method="POST" action="{{ route('activities.updateStatus', $activity) }}">
                            @csrf
                            <div class="flex items-center space-x-4">
                                <select name="status" 
                                        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="draft" {{ $activity->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ $activity->status == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="cancelled" {{ $activity->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="completed" {{ $activity->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                <button type="submit" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Event Details -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Event Details</h2>
                    
                    <div class="space-y-4">
                        <!-- Organization -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Organization</h3>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <a href="{{ route('organizations.show', $activity->organization) }}" 
                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                    {{ $activity->organization->name }}
                                </a>
                            </div>
                        </div>

                        <!-- Date & Time -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Start Date & Time</h3>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-900">{{ $activity->start_date->format('F j, Y - g:i A') }}</span>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">End Date & Time</h3>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-900">{{ $activity->end_date->format('F j, Y - g:i A') }}</span>
                            </div>
                        </div>

                        <!-- Location -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Location</h3>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-gray-900">{{ $activity->location }}</span>
                            </div>
                        </div>

                        <!-- Created By -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Created By</h3>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-gray-900">{{ $activity->creator->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions</h2>
                    <div class="space-y-2">
                        <a href="{{ route('activities.index') }}" 
                           class="block w-full text-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Back to Activities
                        </a>
                        <a href="{{ route('activities.calendar') }}" 
                           class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            View Calendar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection