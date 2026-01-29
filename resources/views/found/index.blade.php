<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Riwayat Penemuan Saya') }}
            </h2>
            <a href="{{ route('found.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md text-sm shadow transition">
                + Lapor Penemuan
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Pesan Sukses --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative flex items-center gap-2" role="alert">
                    <span>✅</span>
                    <span class="block sm:inline font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-6 text-gray-900">
                    
                    @if($foundItems->isEmpty())
                        <div class="text-center py-16 text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <p class="text-lg font-medium text-gray-600">Belum ada data barang temuan.</p>
                            <p class="text-sm mt-1">Jika kamu menemukan barang, segera laporkan di sini.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($foundItems as $item)
                                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition border border-gray-100 flex flex-col h-full overflow-hidden group/card">
                                    
                                    {{-- ================================================= --}}
                                    {{-- BAGIAN SLIDER FOTO (CAROUSEL) --}}
                                    {{-- ================================================= --}}
                                    <div x-data="{ activeSlide: 0, totalSlides: {{ $item->images->count() }} }" class="relative h-56 w-full bg-gray-100 group">
                                        
                                        {{-- Loop Semua Foto --}}
                                        @forelse($item->images as $index => $image)
                                            <div x-show="activeSlide === {{ $index }}" 
                                                 class="absolute inset-0 w-full h-full transition-opacity duration-500 ease-in-out"
                                                 x-transition:enter="opacity-0"
                                                 x-transition:enter-end="opacity-100"
                                                 x-transition:leave="opacity-100"
                                                 x-transition:leave-end="opacity-0">
                                                <img src="{{ asset('storage/' . $image->image_path) }}" 
                                                     alt="{{ $item->item_name }}" 
                                                     class="w-full h-full object-cover">
                                            </div>
                                        @empty
                                            {{-- Placeholder jika tidak ada foto --}}
                                            <div class="flex flex-col items-center justify-center h-full text-gray-400 bg-gray-50">
                                                <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <span class="text-xs italic">Tidak ada foto</span>
                                            </div>
                                        @endforelse

                                        {{-- Tombol Navigasi (Hanya muncul jika foto > 1) --}}
                                        @if($item->images->count() > 1)
                                            {{-- Tombol Kiri --}}
                                            <button @click.prevent="activeSlide = activeSlide === 0 ? totalSlides - 1 : activeSlide - 1" 
                                                    class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/70 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-sm shadow-md z-10 cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                            </button>
                                            
                                            {{-- Tombol Kanan --}}
                                            <button @click.prevent="activeSlide = activeSlide === totalSlides - 1 ? 0 : activeSlide + 1" 
                                                    class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/70 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-sm shadow-md z-10 cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                            </button>

                                            {{-- Indikator Titik (Dots) --}}
                                            <div class="absolute bottom-2 left-0 right-0 flex justify-center gap-1.5 z-10 pointer-events-none">
                                                @foreach($item->images as $index => $image)
                                                    <div class="w-1.5 h-1.5 rounded-full shadow-sm transition-all duration-300 border border-black/10" 
                                                         :class="activeSlide === {{ $index }} ? 'bg-white w-3' : 'bg-white/50'"></div>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Badge Status --}}
                                        <div class="absolute top-3 right-3 z-10">
                                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md shadow-sm border border-white/20 backdrop-blur-md
                                                {{ $item->status == 'tersedia' ? 'bg-green-500/90 text-white' : 'bg-gray-500/90 text-white' }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    {{-- ================================================= --}}


                                    <div class="p-5 flex flex-col flex-1">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <h4 class="font-bold text-lg text-gray-900 leading-tight line-clamp-1 group-hover/card:text-indigo-600 transition">
                                                    {{ $item->item_name }}
                                                </h4>
                                                <div class="flex items-center gap-1 text-xs text-gray-500 mt-1">
                                                    <span></span> {{ $item->category->name ?? 'Umum' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-sm text-gray-600 mb-4 line-clamp-2 min-h-[2.5em]">
                                            <span class="mr-1"></span> Di {{ $item->location_found }}
                                        </div>

                                        <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between">
                                            <div class="text-xs text-gray-400 flex flex-col">
                                                <span>Ditemukan:</span>
                                                <span class="font-semibold text-gray-600">{{ \Carbon\Carbon::parse($item->date_found)->format('d M Y') }}</span>
                                            </div>
                                            
                                            <a href="{{ route('found.show', $item->id) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-bold flex items-center gap-1 transition-transform hover:translate-x-1">
                                                Detail <span class="text-lg">&rarr;</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $foundItems->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>