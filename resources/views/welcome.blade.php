<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lost & Found Kampus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="antialiased bg-gray-50">

    {{-- NAVIGASI --}}
    <nav class="absolute w-full z-20 top-0 left-0 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-lg text-indigo-600 font-bold text-xl">
                    🔍
                </div>
                <span class="text-white font-bold text-xl tracking-tight hidden md:block">LNF Kampus</span>
            </div>
            
            <div class="flex space-x-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-2 bg-white/10 hover:bg-white/20 text-white rounded-full font-medium transition backdrop-blur-sm border border-white/20">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-2 text-white hover:text-indigo-100 font-medium transition">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-6 py-2 bg-white text-indigo-700 rounded-full font-bold shadow-lg hover:bg-indigo-50 transition transform hover:-translate-y-0.5">Daftar</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <div class="relative min-h-screen bg-gradient-to-br from-indigo-900 via-blue-800 to-indigo-900 flex items-center justify-center overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute top-[-10%] right-[-10%] w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-[-20%] left-[20%] w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-indigo-500/30 border border-indigo-400/30 text-indigo-100 text-sm font-semibold mb-6 backdrop-blur-md">
                ✨ Portal Barang Hilang & Temuan No.1 di Kampus
            </span>
            
            <h1 class="text-5xl md:text-7xl font-extrabold text-white tracking-tight leading-tight mb-6">
                Jangan Biarkan <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 to-purple-200">Barangmu Hilang</span> Selamanya.
            </h1>
            
            <p class="mt-4 max-w-2xl mx-auto text-lg md:text-xl text-indigo-100 mb-10 leading-relaxed">
                Platform pintar untuk melaporkan kehilangan dan penemuan barang di lingkungan kampus. Terintegrasi, transparan, dan mudah digunakan.
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                @auth
                    <a href="{{ route('lost.create') }}" class="px-8 py-4 bg-red-500 hover:bg-red-600 text-white rounded-2xl font-bold text-lg shadow-xl shadow-red-500/30 transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                        <span>📢</span> Lapor Kehilangan
                    </a>
                    <a href="{{ route('found.create') }}" class="px-8 py-4 bg-white text-indigo-900 rounded-2xl font-bold text-lg shadow-xl hover:bg-gray-50 transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                        <span>🤝</span> Lapor Penemuan
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-8 py-4 bg-white text-indigo-900 rounded-2xl font-bold text-lg shadow-xl hover:bg-gray-50 transition transform hover:-translate-y-1 w-full sm:w-auto">
                        Mulai Sekarang
                    </a>
                @endauth
            </div>

            {{-- MINI STATS --}}
            <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
                <div class="glass p-4 rounded-2xl text-center">
                    <div class="text-3xl font-bold text-white mb-1">24/7</div>
                    <div class="text-xs text-indigo-200 uppercase tracking-wider">Akses Sistem</div>
                </div>
                <div class="glass p-4 rounded-2xl text-center">
                    <div class="text-3xl font-bold text-white mb-1">100%</div>
                    <div class="text-xs text-indigo-200 uppercase tracking-wider">Gratis</div>
                </div>
                <div class="glass p-4 rounded-2xl text-center">
                    <div class="text-3xl font-bold text-white mb-1">Safe</div>
                    <div class="text-xs text-indigo-200 uppercase tracking-wider">Verifikasi</div>
                </div>
                <div class="glass p-4 rounded-2xl text-center">
                    <div class="text-3xl font-bold text-white mb-1">Fast</div>
                    <div class="text-xs text-indigo-200 uppercase tracking-wider">Notifikasi</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>