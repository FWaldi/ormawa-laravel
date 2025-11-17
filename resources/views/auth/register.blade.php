<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Ormawa UNP</title>
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
        <div class="w-full max-w-md">
            <div class="bg-white p-8 rounded-lg shadow-xl border border-[--border-color]">
                <div class="text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <h2 class="mt-4 text-3xl font-bold font-lora text-[--primary-blue]">
                        Daftar Akun Baru
                    </h2>
                    <p class="mt-2 text-[--text-secondary]">
                        Bergabung dengan platform ORMAWA UNP
                    </p>
                </div>

                <form action="{{ route('register') }}" method="POST" class="mt-8 space-y-4">
                    @csrf

                    @if ($errors->any())
                        <p class="text-sm text-center text-[--error-red] bg-red-50 p-2 rounded-md">
                            @foreach ($errors->all() as $error)
                                {{ $error }}
                            @endforeach
                        </p>
                    @endif

                    <div>
                        <label
                            for="name"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Nama Lengkap
                        </label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[--primary-blue] focus:border-[--primary-blue]"
                            placeholder="Masukkan nama lengkap"
                        />
                    </div>

                    <div>
                        <label
                            for="email"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Email
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[--primary-blue] focus:border-[--primary-blue]"
                            placeholder="email@example.com"
                        />
                    </div>

                    <div>
                        <label
                            for="password"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Password
                        </label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[--primary-blue] focus:border-[--primary-blue]"
                            placeholder="Minimal 8 karakter"
                        />
                    </div>

                    <div>
                        <label
                            for="password_confirmation"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Konfirmasi Password
                        </label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[--primary-blue] focus:border-[--primary-blue]"
                            placeholder="Ulangi password"
                        />
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-[--primary-blue] hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[--primary-blue] transition-all duration-300 transform hover:scale-105"
                        >
                            Daftar
                        </button>
                    </div>

                    {/* Divider */}
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">
                                Atau daftar dengan
                            </span>
                        </div>
                    </div>

                    {/* Google Sign Up Button */}
                    <div>
                        <a
                            href="{{ route('google.redirect') }}"
                            class="w-full flex items-center justify-center gap-3 py-3 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[--primary-blue] transition-all duration-300"
                        >
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span>Daftar dengan Google</span>
                        </a>
                    </div>
                </form>

                <p class="mt-6 text-center text-sm text-gray-600">
                    Sudah punya akun?{" "}
                    <a
                        href="{{ route('login') }}"
                        class="font-medium text-[--primary-blue] hover:underline"
                    >
                        Login di sini
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>