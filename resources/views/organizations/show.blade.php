@extends('layouts.app')

@section('title', $organization->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $organization->name }}</h1>
        <div class="flex space-x-2">
            @if(auth()->user()->isAdmin() || auth()->user()->isOrgAdmin($organization->id))
                <a href="{{ route('organizations.edit', $organization->id) }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Edit Organization
                </a>
            @endif
            <a href="{{ route('organizations.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Organizations
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Organization Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-start space-x-6">
                    @if($organization->logo)
                        <img src="{{ Storage::url($organization->logo) }}" 
                             alt="{{ $organization->name }}" 
                             class="w-32 h-32 object-cover rounded-lg">
                    @else
                        <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                            <span class="text-gray-500 text-4xl">{{ substr($organization->name, 0, 1) }}</span>
                        </div>
                    @endif
                    
                    <div class="flex-1">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-2">{{ $organization->name }}</h2>
                        <p class="text-gray-600 mb-4">{{ $organization->type }}</p>
                        
                        @if($organization->description)
                            <p class="text-gray-700 mb-4">{{ $organization->description }}</p>
                        @endif
                        
                        @if($organization->contact)
                            <div class="mb-4">
                                <h3 class="font-semibold text-gray-900 mb-1">Contact Information</h3>
                                <p class="text-gray-700">{{ $organization->contact }}</p>
                            </div>
                        @endif

                        @if($organization->social_media)
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Social Media</h3>
                                <div class="flex space-x-4">
                                    @if($organization->social_media['facebook'] ?? null)
                                        <a href="{{ $organization->social_media['facebook'] }}" target="_blank" class="text-blue-600 hover:text-blue-800">Facebook</a>
                                    @endif
                                    @if($organization->social_media['twitter'] ?? null)
                                        <a href="{{ $organization->social_media['twitter'] }}" target="_blank" class="text-blue-400 hover:text-blue-600">Twitter</a>
                                    @endif
                                    @if($organization->social_media['instagram'] ?? null)
                                        <a href="{{ $organization->social_media['instagram'] }}" target="_blank" class="text-pink-600 hover:text-pink-800">Instagram</a>
                                    @endif
                                    @if($organization->social_media['linkedin'] ?? null)
                                        <a href="{{ $organization->social_media['linkedin'] }}" target="_blank" class="text-blue-700 hover:text-blue-900">LinkedIn</a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $organization->members->count() }}</div>
                    <div class="text-gray-600">Members</div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $organization->activities->count() }}</div>
                    <div class="text-gray-600">Activities</div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="text-3xl font-bold text-purple-600">{{ $organization->news->count() }}</div>
                    <div class="text-gray-600">News Articles</div>
                </div>
            </div>

            <!-- Recent Activities -->
            @if($organization->activities->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Recent Activities</h3>
                    <div class="space-y-3">
                        @foreach($organization->activities->take(3) as $activity)
                            <div class="border-l-4 border-blue-500 pl-4">
                                <h4 class="font-semibold text-gray-900">{{ $activity->title }}</h4>
                                <p class="text-gray-600 text-sm">{{ $activity->date->format('M d, Y') }}</p>
                                @if($activity->description)
                                    <p class="text-gray-700 mt-1">{{ Str::limit($activity->description, 100) }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if($organization->activities->count() > 3)
                        <a href="#" class="text-blue-600 hover:text-blue-800 mt-4 inline-block">View all activities →</a>
                    @endif
                </div>
            @endif

            <!-- Recent News -->
            @if($organization->news->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Recent News</h3>
                    <div class="space-y-3">
                        @foreach($organization->news->take(3) as $news)
                            <div class="border-l-4 border-purple-500 pl-4">
                                <h4 class="font-semibold text-gray-900">{{ $news->title }}</h4>
                                <p class="text-gray-600 text-sm">{{ $news->published_at->format('M d, Y') }}</p>
                                @if($news->content)
                                    <p class="text-gray-700 mt-1">{{ Str::limit($news->content, 100) }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if($organization->news->count() > 3)
                        <a href="#" class="text-purple-600 hover:text-purple-800 mt-4 inline-block">View all news →</a>
                    @endif
                </div>
            @endif
        </div>

        <!-- Members Section -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-900">Members</h3>
                    @if(auth()->user()->isAdmin() || auth()->user()->isOrgAdmin($organization->id))
                        <button onclick="showAddMemberModal()" class="text-blue-600 hover:text-blue-800">
                            + Add Member
                        </button>
                    @endif
                </div>

                @if($organization->members->count() > 0)
                    <div class="space-y-3">
                        @foreach($organization->members as $member)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ substr($member->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $member->name }}</div>
                                        <div class="text-sm text-gray-600">{{ $member->email }}</div>
                                        <div class="text-xs text-gray-500">{{ $member->role }}</div>
                                    </div>
                                </div>
                                @if(auth()->user()->isAdmin() || auth()->user()->isOrgAdmin($organization->id))
                                    @if($member->id !== auth()->id())
                                        <form action="{{ route('organizations.removeMember', [$organization->id, $member->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this member?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                Remove
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No members yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
@if(auth()->user()->isAdmin() || auth()->user()->isOrgAdmin($organization->id))
<div id="addMemberModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full" z-50>
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Member</h3>
            <form action="{{ route('organizations.addMember', $organization->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="user_id" class="block text-gray-700 text-sm font-bold mb-2">
                        User *
                    </label>
                    <select id="user_id" name="user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="">Select User</option>
                        @foreach(App\Models\User::whereNull('organization_id')->orWhere('organization_id', '!=', $organization->id)->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="role" class="block text-gray-700 text-sm font-bold mb-2">
                        Role *
                    </label>
                    <select id="role" name="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="member">Member</option>
                        <option value="org_admin">Organization Admin</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="hideAddMemberModal()" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showAddMemberModal() {
    document.getElementById('addMemberModal').classList.remove('hidden');
}

function hideAddMemberModal() {
    document.getElementById('addMemberModal').classList.add('hidden');
}
</script>
@endif
@endsection