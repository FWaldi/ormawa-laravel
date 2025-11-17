<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Ormawa UNP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-blue': '#1e40af',
                        'accent-orange': '#fb923c',
                        'text-dark': '#1f2937',
                        'text-secondary': '#6b7280',
                        'bg-main': '#ffffff',
                        'error-red': '#dc2626',
                        'border-color': '#d1d5db'
                    },
                    fontFamily: {
                        'lora': ['Lora', 'serif']
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --primary-blue: #1e40af;
            --accent-orange: #fb923c;
            --text-dark: #1f2937;
            --text-secondary: #6b7280;
            --bg-main: #ffffff;
            --error-red: #dc2626;
            --border-color: #d1d5db;
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-[--bg-main]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 flex justify-center items-center animate-fade-in">
        <div class="w-full max-w-sm">
            <div class="bg-white p-8 rounded-lg shadow-xl border border-[--border-color]">
                <div class="text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <h2 class="mt-4 text-3xl font-bold font-lora text-[--primary-blue]">
                        Masuk ke Akun
                    </h2>
                    <p class="mt-2 text-[--text-secondary]">
                        Silakan masuk untuk mengakses platform ORMAWA UNP
                    </p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="mt-8 space-y-6">
                    @csrf

                    @if (session('success'))
                        <p class="text-sm text-center text-green-600 bg-green-50 p-2 rounded-md">
                            {{ session('success') }}
                        </p>
                    @endif

                    @if ($errors->any())
                        <p class="text-sm text-center text-[--error-red] bg-red-50 p-2 rounded-md">
                            @foreach ($errors->all() as $error)
                                {{ $error }}
                            @endforeach
                        </p>
                    @endif

                    <div class="relative">
                        <label for="email" class="sr-only">
                            Email
                        </label>
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[--primary-blue] focus:border-[--primary-blue]"
                            placeholder="Email"
                        />
                    </div>

                    <div class="relative">
                        <label for="password" class="sr-only">
                            Password
                        </label>
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[--primary-blue] focus:border-[--primary-blue]"
                            placeholder="Password"
                        />
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <a
                            href="{{ route('password.request') }}"
                            class="text-[--primary-blue] hover:underline"
                        >
                            Lupa password?
                        </a>
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-[--text-dark] bg-[--accent-orange] hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[--accent-orange] transition-all duration-300 transform hover:scale-105"
                        >
                            Log In
                        </button>
                    </div>
                </form>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300" />
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">
                                Atau login dengan
                            </span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a
                            href="{{ route('google.redirect') }}"
                            class="w-full flex items-center justify-center gap-3 py-3 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[--primary-blue] transition-all duration-300"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Login dengan Google
                        </a>
                    </div>
                </div>

                <p class="mt-6 text-center text-sm text-gray-600">
                    Belum punya akun?{" "}
                    <a
                        href="{{ route('register') }}"
                        class="font-medium text-[--primary-blue] hover:underline"
                    >
                        Daftar sekarang
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>