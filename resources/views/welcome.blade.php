<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Lost & Found - UNU lampung</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Permanent+Marker&family=Figtree:wght@400;600&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50 text-gray-800">

        {{-- NAVBAR --}}
        <nav class="bg-white/80 backdrop-blur-md fixed w-full z-50 border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    {{-- Logo --}}
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('img/logo-unu.png') }}" alt="Logo" class="h-10 w-auto">
                        <div class="flex flex-col">
                            <span class="font-['Permanent_Marker'] text-xl text-gray-900 tracking-wider">LOST & FOUND</span>
                            <span class="text-[10px] font-bold text-blue-900 uppercase -mt-1 tracking-widest">UNU lampung</span>
                        </div>
                    </div>

                    {{-- Menu Login/Register --}}
                    <div class="flex items-center gap-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-blue-600 transition">Log in</a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full font-bold text-sm shadow-lg transition transform hover:scale-105">
                                        Daftar Sekarang
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        {{-- HERO SECTION --}}
        <div class="relative pt-32 pb-20 sm:pt-40 sm:pb-24 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                
                <span class="inline-block py-1 px-3 rounded-full bg-blue-50 text-blue-600 text-xs font-bold tracking-widest mb-6 border border-blue-100">
                    SISTEM INFORMASI BARANG TEMUAN KAMPUS
                </span>

                <h1 class="text-4xl sm:text-6xl font-extrabold text-gray-900 tracking-tight mb-6 leading-tight">
                    Kehilangan Barang di Kampus?</span> <br>
                    Atau Menemukan Sesuatu?
                </h1>
                
                <p class="mt-4 text-xl text-gray-500 max-w-2xl mx-auto mb-10">
                    Jangan panik. Laporkan kehilangan atau temuan barangmu di sini. 
                    Kami membantu menghubungkan pemilik dengan barangnya secara cepat dan aman.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    @auth
                        <a href="{{ route('lost.create') }}" class="w-full sm:w-auto px-8 py-4 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold text-lg shadow-xl shadow-red-200 transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                            🔍 Saya Kehilangan Barang
                        </a>
                        <a href="{{ route('found.create') }}" class="w-full sm:w-auto px-8 py-4 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold text-lg shadow-xl shadow-green-200 transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                            📦 Saya Menemukan Barang
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold text-lg shadow-xl shadow-red-200 transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                            🔍 Saya Kehilangan Barang
                        </a>
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold text-lg shadow-xl shadow-green-200 transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                            📦 Saya Menemukan Barang
                        </a>
                    @endauth
                </div>
            </div>

            {{-- Background Decoration --}}
            <div class="absolute top-0 left-1/2 w-full -translate-x-1/2 h-full z-0 pointer-events-none">
                <div class="absolute top-20 left-10 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
                <div class="absolute top-20 right-10 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            </div>
        </div>

        {{-- STATS SECTION --}}
        <div class="bg-white border-y border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div>
                        <div class="text-3xl font-bold text-blue-600">24/7</div>
                        <div class="text-sm text-gray-500 mt-1">Akses Sistem</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-blue-600">100+</div>
                        <div class="text-sm text-gray-500 mt-1">Mahasiswa Aktif</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-green-600">85%</div>
                        <div class="text-sm text-gray-500 mt-1">Barang Kembali</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-gray-800">UNU</div>
                        <div class="text-sm text-gray-500 mt-1">Lingkungan Kampus</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FEATURES / HOW IT WORKS --}}
        <div class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-gray-900">Bagaimana Cara Kerjanya?</h2>
                    <p class="text-gray-500 mt-2">Proses mudah untuk mengembalikan senyumanmu.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-10">
                    {{-- Step 1 --}}
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition text-center">
                        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-6">
                            📢
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">1. Laporkan</h3>
                        <p class="text-gray-500 leading-relaxed">
                            Kehilangan barang atau menemukan sesuatu? Segera login dan buat laporan baru dengan detail dan foto.
                        </p>
                    </div>

                    {{-- Step 2 --}}
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition text-center">
                        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-6">
                            🔍
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">2. Cocokkan</h3>
                        <p class="text-gray-500 leading-relaxed">
                            Sistem kami membantu mencocokkan barang hilang dengan barang temuan. Gunakan fitur pencarian canggih.
                        </p>
                    </div>

                    {{-- Step 3 --}}
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition text-center">
                        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-6">
                            🤝
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">3. Kembalikan</h3>
                        <p class="text-gray-500 leading-relaxed">
                            Ajukan klaim, verifikasi kepemilikan, dan atur pertemuan untuk serah terima barang di kampus.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <footer class="bg-white border-t border-gray-200 py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('img/logo-unu.png') }}" alt="Logo" class="h-8 grayscale opacity-50 hover:opacity-100 transition">
                    <span class="font-bold text-gray-400">Lost & Found UNU</span>
                </div>
                <div class="text-sm text-gray-400">
                    &copy; {{ date('Y') }} Universitas Nahdlatul Ulama lampung. All rights reserved.
                </div>
            </div>
        </footer>

    </body>
</html>