@extends('layouts.app')

@section('title', 'Profile Saya - Organisasi Mahasiswa')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-[--primary-blue] to-blue-600 p-6 text-white">
            <div class="flex items-center gap-4">
                <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold">{{ Auth::user()->name }}</h1>
                    <p class="text-blue-100">{{ Auth::user()->email }}</p>
                    <div class="mt-2 flex gap-2 items-center flex-wrap">
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            {{ strtoupper(Auth::user()->role) }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            ✓ Verified
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <!-- Profile Information -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">
                        Profile Information
                    </h2>
                    <button class="px-4 py-2 text-sm font-medium text-[--primary-blue] hover:bg-blue-50 rounded-md transition">
                        Edit Profile
                    </button>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-500">Full Name</label>
                        <p class="text-lg font-medium text-gray-900">
                            {{ Auth::user()->name }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Email Address</label>
                        <p class="text-lg font-medium text-gray-900">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Role</label>
                        <p class="text-lg font-medium text-gray-900">
                            {{ Auth::user()->role }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <hr class="border-gray-200" />

            <!-- Change Password Section -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">
                        Change Password
                    </h2>
                    <button class="px-4 py-2 text-sm font-medium text-[--primary-blue] hover:bg-blue-50 rounded-md transition">
                        Change Password
                    </button>
                </div>

                <p class="text-gray-600">
                    Keep your account secure by using a strong password.
                </p>
            </div>

            <!-- Divider -->
            <hr class="border-gray-200" />

            <!-- Account Actions -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    Account Actions
                </h2>
                <div class="flex gap-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection