<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- 1. HERO SECTION (Banner Selamat Datang) --}}
            <div class="relative bg-gradient-to-br from-indigo-700 via-purple-700 to-indigo-900 rounded-3xl p-8 mb-10 shadow-2xl overflow-hidden text-white transform transition hover:scale-[1.01] duration-500">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center">
                    <div class="space-y-2">
                        <h3 class="text-4xl font-extrabold tracking-tight">Halo, {{ Auth::user()->name }}! </h3>
                        <p class="text-indigo-100 text-lg font-light max-w-xl">
                            Selamat datang di pusat kontrol <span class="font-semibold text-white">Lost & Found</span>. Pantau semua aktivitas barang di sini.
                        </p>
                    </div>
                    <div class="mt-6 md:mt-0 backdrop-blur-sm bg-white/10 p-4 rounded-2xl border border-white/20 shadow-inner">
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-xs text-indigo-200 uppercase tracking-widest font-semibold">Hari ini</p>
                                <p class="text-lg font-bold">{{ date('d F Y') }}</p>
                            </div>
                            <div class="h-10 w-1 bg-white/30 rounded-full"></div>
                            <div class="text-3xl">🗓️</div>
                        </div>
                    </div>
                </div>
                
                {{-- Ornamen Dekorasi --}}
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            </div>

            {{-- 2. STATISTIK CARDS (Layout 3 Kolom Presisi) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                
                {{-- CARD 1: Barang Dicari --}}
                <div class="bg-white rounded-3xl p-6 shadow-lg shadow-red-100/50 border border-red-50 hover:shadow-red-200 hover:-translate-y-2 transition-all duration-300 group relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-24 h-24 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9 9a2 2 0 114 0 2 2 0 01-4 0z"/><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a4 4 0 00-3.446 6.032l-2.261 2.26a1 1 0 101.414 1.415l2.261-2.261A4 4 0 1011 5z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="flex items-center space-x-4 relative z-10">
                        <div class="p-3 bg-red-100 text-red-600 rounded-2xl group-hover:bg-red-600 group-hover:text-white transition-colors duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-bold uppercase tracking-wider">Barang Dicari</p>
                            <h4 class="text-3xl font-extrabold text-gray-800">{{ $totalLost ?? 0 }}</h4>
                        </div>
                    </div>
                    <a href="{{ route('lost.index') }}" class="mt-6 block w-full py-2 bg-red-50 text-red-600 text-center rounded-xl font-bold text-sm hover:bg-red-600 hover:text-white transition-all">Lihat Detail &rarr;</a>
                </div>

                {{-- CARD 2: Barang Tersedia --}}
                <div class="bg-white rounded-3xl p-6 shadow-lg shadow-emerald-100/50 border border-emerald-50 hover:shadow-emerald-200 hover:-translate-y-2 transition-all duration-300 group relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-24 h-24 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="flex items-center space-x-4 relative z-10">
                        <div class="p-3 bg-emerald-100 text-emerald-600 rounded-2xl group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-bold uppercase tracking-wider">Barang Tersedia</p>
                            <h4 class="text-3xl font-extrabold text-gray-800">{{ $totalFound ?? 0 }}</h4>
                        </div>
                    </div>
                    <a href="{{ route('found.index') }}" class="mt-6 block w-full py-2 bg-emerald-50 text-emerald-600 text-center rounded-xl font-bold text-sm hover:bg-emerald-600 hover:text-white transition-all">Cek Sekarang &rarr;</a>
                </div>

                {{-- CARD 3: LOGIKA PINTAR (Klaim Saya vs Status Server) --}}
                @if(Auth::user()->role !== 'admin')
                    {{-- TAMPILAN MAHASISWA: KLAIM SAYA --}}
                    <div class="bg-white rounded-3xl p-6 shadow-lg shadow-amber-100/50 border border-amber-50 hover:shadow-amber-200 hover:-translate-y-2 transition-all duration-300 group relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <svg class="w-24 h-24 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="flex items-center space-x-4 relative z-10">
                            <div class="p-3 bg-amber-100 text-amber-600 rounded-2xl group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-bold uppercase tracking-wider">Klaim Saya</p>
                                <h4 class="text-3xl font-extrabold text-gray-800">{{ $myClaims ?? 0 }}</h4>
                            </div>
                        </div>
                        <a href="{{ route('claims.index') }}" class="mt-6 block w-full py-2 bg-amber-50 text-amber-600 text-center rounded-xl font-bold text-sm hover:bg-amber-500 hover:text-white transition-all">Lihat Status &rarr;</a>
                    </div>
                @else
                    {{-- TAMPILAN ADMIN: SERVER STATUS (Agar tetap 3 kolom) --}}
                    <div class="bg-slate-800 rounded-3xl p-6 shadow-lg shadow-slate-400/50 border border-slate-700 hover:shadow-slate-500 hover:-translate-y-2 transition-all duration-300 group relative overflow-hidden">
                        <div class="flex items-center space-x-4 relative z-10 text-white">
                            <div class="p-3 bg-slate-700 text-blue-400 rounded-2xl">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm text-slate-400 font-bold uppercase tracking-wider">System Status</p>
                                <div class="flex items-center mt-1">
                                    <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse mr-2"></span>
                                    <h4 class="text-xl font-bold text-white">Online</h4>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 bg-slate-900/50 rounded-xl p-3 text-center border border-slate-600">
                            <p class="text-xs text-slate-400 font-mono tracking-wide">API: {{ url('/api') }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- 3. AKSES CEPAT (Menu Navigasi) --}}
            <div class="flex items-center mb-6">
                <div class="h-8 w-1 bg-indigo-600 rounded-full mr-3"></div>
                <h3 class="font-bold text-gray-800 text-xl">Akses Cepat</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <a href="{{ route('lost.create') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-indigo-100 hover:shadow-xl hover:shadow-indigo-100/40 transition-all duration-300 flex items-center">
                    <div class="w-16 h-16 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center text-3xl group-hover:scale-110 group-hover:bg-red-500 group-hover:text-white transition duration-300 shadow-sm mr-5">
                        📢
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-gray-800 group-hover:text-red-600 transition">Lapor Kehilangan</h4>
                        <p class="text-sm text-gray-500 mt-1">Posting barang hilangmu.</p>
                    </div>
                </a>

                <a href="{{ route('found.create') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-indigo-100 hover:shadow-xl hover:shadow-indigo-100/40 transition-all duration-300 flex items-center">
                    <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-3xl group-hover:scale-110 group-hover:bg-blue-500 group-hover:text-white transition duration-300 shadow-sm mr-5">
                        🤝
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-gray-800 group-hover:text-blue-600 transition">Lapor Penemuan</h4>
                        <p class="text-sm text-gray-500 mt-1">Bantu kembalikan barang.</p>
                    </div>
                </a>

                <a href="{{ route('match.index') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-indigo-100 hover:shadow-xl hover:shadow-indigo-100/40 transition-all duration-300 flex items-center">
                    <div class="w-16 h-16 bg-purple-50 text-purple-500 rounded-2xl flex items-center justify-center text-3xl group-hover:scale-110 group-hover:bg-purple-500 group-hover:text-white transition duration-300 shadow-sm mr-5">
                        ✨
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-gray-800 group-hover:text-purple-600 transition">Smart Match</h4>
                        <p class="text-sm text-gray-500 mt-1">Cek kecocokan otomatis.</p>
                    </div>
                </a>

            </div>

        </div>
    </div>
</x-app-layout>