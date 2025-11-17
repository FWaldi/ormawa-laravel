<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Ormawa UNP</title>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <h2 class="mt-4 text-3xl font-bold font-lora text-[--primary-blue]">
                        Verifikasi Email
                    </h2>
                    <p class="mt-2 text-[--text-secondary]">
                        Kami telah mengirimkan link verifikasi ke email Anda. Silakan periksa inbox Anda dan klik link untuk mengaktifkan akun.
                    </p>
                </div>

                <div class="mt-8 space-y-4">
                    <p class="text-sm text-[--text-secondary] text-center">
                        Belum menerima email? Periksa folder spam atau junk.
                    </p>

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-[--text-dark] bg-[--accent-orange] hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[--accent-orange] transition-all duration-300 transform hover:scale-105">
                            Kirim Ulang Email Verifikasi
                        </button>
                    </form>

                    <div class="text-center">
                        <a href="{{ route('logout') }}" class="text-sm text-[--primary-blue] hover:underline">
                            Keluar dan masuk dengan akun lain
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>