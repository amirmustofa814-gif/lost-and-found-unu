<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                 Detail Barang Temuan
            </h2>
            <a href="{{ route('found.index') }}" class="text-gray-500 hover:text-gray-700 font-medium text-sm transition flex items-center gap-1">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-5 border border-gray-100">
                
                {{-- ========================================================= --}}
                {{-- KOLOM KIRI: GALERI FOTO (40% width) --}}
                {{-- ========================================================= --}}
                <div class="lg:col-span-2 bg-gray-100 flex flex-col" 
                     x-data="{ 
                        activeImage: '{{ $foundItem->images->isNotEmpty() ? asset('storage/' . $foundItem->images->first()->image_path) : '' }}' 
                     }">
                    
                    @if($foundItem->images->isNotEmpty())
                        {{-- 1. FOTO UTAMA (BESAR) --}}
                        <div class="relative w-full h-[400px] lg:h-[500px] bg-gray-200 group">
                            <img :src="activeImage" 
                                 alt="{{ $foundItem->item_name }}" 
                                 class="w-full h-full object-contain bg-gray-900/5 transition-all duration-300">
                            
                            {{-- Tombol Zoom --}}
                            <a :href="activeImage" target="_blank" class="absolute bottom-4 right-4 bg-white/90 text-gray-800 text-xs font-bold px-4 py-2 rounded-full shadow-lg hover:bg-white transition flex items-center gap-2 backdrop-blur-sm z-10">
                                 Lihat Foto Asli
                            </a>
                        </div>

                        {{-- 2. THUMBNAIL (DERETAN FOTO KECIL) --}}
                        @if($foundItem->images->count() > 1)
                            <div class="p-4 bg-white border-t border-gray-200 overflow-x-auto flex gap-3 scrollbar-hide">
                                @foreach($foundItem->images as $image)
                                    <button @click="activeImage = '{{ asset('storage/' . $image->image_path) }}'" 
                                            class="w-16 h-16 flex-shrink-0 rounded-md overflow-hidden border-2 transition-all duration-200 focus:outline-none"
                                            :class="activeImage === '{{ asset('storage/' . $image->image_path) }}' ? 'border-indigo-600 ring-2 ring-indigo-100 opacity-100' : 'border-transparent opacity-60 hover:opacity-100'">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif

                    @else
                        {{-- Placeholder Jika Tidak Ada Foto --}}
                        <div class="flex flex-col items-center justify-center h-full text-gray-400 bg-gray-50 min-h-[400px]">
                            <svg class="w-24 h-24 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-sm font-medium opacity-50">Tidak ada foto dilampirkan</span>
                        </div>
                    @endif
                </div>

                {{-- ========================================================= --}}
                {{-- KOLOM KANAN: INFORMASI (60% width) --}}
                {{-- ========================================================= --}}
                <div class="lg:col-span-3 p-8 lg:p-10 flex flex-col justify-between h-full bg-white">
                    
                    <div>
                        {{-- Header Barang --}}
                        <div class="flex flex-wrap items-center gap-2 mb-4">
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold uppercase tracking-wider rounded-md border border-indigo-100">
                                {{ $foundItem->category->name ?? 'Umum' }}
                            </span>
                            @if($foundItem->status == 'tersedia')
                                <span class="px-3 py-1 bg-green-50 text-green-700 text-xs font-bold uppercase tracking-wider rounded-md border border-green-100 flex items-center gap-2">
                                    <span class="relative flex h-2 w-2">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                    </span>
                                    Tersedia
                                </span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-bold uppercase tracking-wider rounded-md border border-gray-200">
                                    🔒 {{ ucfirst($foundItem->status) }}
                                </span>
                            @endif
                        </div>
                        
                        <h1 class="text-4xl font-extrabold text-gray-900 leading-tight mb-3">
                            {{ $foundItem->item_name }}
                        </h1>

                        {{-- Info Penemu --}}
                        <div class="flex items-center gap-3 mb-8 pb-8 border-b border-gray-100">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-lg">
                                {{ substr($foundItem->user->name ?? 'A', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold tracking-wide">Ditemukan Oleh</p>
                                <p class="text-sm font-bold text-gray-800">{{ $foundItem->user->name ?? 'Anonim' }}</p>
                            </div>
                            <div class="ml-auto text-right">
                                <p class="text-xs text-gray-500 uppercase font-bold tracking-wide">Waktu Posting</p>
                                <p class="text-sm text-gray-600">
                                    {{ $foundItem->created_at ? $foundItem->created_at->diffForHumans() : '-' }}
                                </p>
                            </div>
                        </div>

                        {{-- Detail Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            {{-- Tanggal & Jam Ditemukan --}}
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <p class="text-xs text-gray-500 uppercase font-bold tracking-wide mb-1 flex items-center gap-1"> Waktu Ditemukan</p>
                                
                                <p class="text-gray-900 font-semibold text-lg">
                                    {{ \Carbon\Carbon::parse($foundItem->date_found)->translatedFormat('d F Y') }}
                                </p>
                                
                                @if($foundItem->time_found)
                                    <div class="text-sm text-indigo-600 font-semibold mt-1">
                                        Pukul {{ \Carbon\Carbon::parse($foundItem->time_found)->format('H:i') }} WIB
                                    </div>
                                @endif
                            </div>

                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <p class="text-xs text-gray-500 uppercase font-bold tracking-wide mb-1 flex items-center gap-1"> Lokasi Penemuan</p>
                                <p class="text-gray-900 font-semibold">{{ $foundItem->location_found }}</p>
                            </div>
                            
                            <div class="md:col-span-2 bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                                <p class="text-xs text-indigo-600 uppercase font-bold tracking-wide mb-1 flex items-center gap-1"> Posisi Barang Sekarang</p>
                                <p class="text-indigo-900 font-bold text-lg">{{ $foundItem->current_position ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-8">
                            <h3 class="text-sm font-bold text-gray-900 mb-2 uppercase tracking-wide">Deskripsi & Ciri-ciri</h3>
                            <div class="text-gray-600 text-sm leading-relaxed whitespace-pre-line bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                                {{ $foundItem->description }}
                            </div>
                        </div>
                    </div>

                    {{-- Action Area (Footer) --}}
                    <div class="mt-4">
                        @if(Auth::id() == $foundItem->user_id)
                            {{-- Jika Pemilik --}}
                            <div class="flex gap-3">
                                <a href="{{ route('found.edit', $foundItem->id) }}" class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-yellow-900 font-bold py-3.5 px-4 rounded-xl text-center transition shadow-sm hover:shadow-md flex justify-center items-center gap-2">
                                     Edit Data
                                </a>
                                <form action="{{ route('found.destroy', $foundItem->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full bg-red-100 hover:bg-red-200 text-red-700 font-bold py-3.5 px-4 rounded-xl text-center transition flex justify-center items-center gap-2">
                                         Hapus
                                    </button>
                                </form>
                            </div>
                        @elseif($foundItem->status == 'tersedia')
                            {{-- Jika Orang Lain (Form Klaim) --}}
                            <div x-data="{ open: false }">
                                <button @click="open = !open" x-show="!open" class="w-full bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                    <span></span> Ini Barang Saya! Ajukan Klaim
                                </button>

                                {{-- Form Klaim Slide Down --}}
                                <div x-show="open" x-transition class="bg-indigo-50 p-6 rounded-xl border-2 border-indigo-100 mt-2 shadow-inner">
                                    <h4 class="font-bold text-indigo-900 mb-4 text-lg">Formulir Klaim Barang</h4>
                                    <form action="{{ route('claims.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="found_item_id" value="{{ $foundItem->id }}">
                                        
                                        <div class="mb-4">
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Bukti Kepemilikan (Wajib)</label>
                                            <textarea name="description" rows="3" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Jelaskan ciri khusus yang tidak terlihat di foto (misal: isi dompet, goresan di belakang, wallpaper HP)..." required></textarea>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Foto Bukti (Opsional)</label>
                                            <input type="file" name="proof_image" class="block w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-200 file:text-indigo-700 hover:file:bg-indigo-300">
                                            <p class="text-xs text-gray-400 mt-1">Upload foto lama barang ini jika ada.</p>
                                        </div>

                                        <div class="flex gap-3 pt-2">
                                            <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-lg text-sm transition shadow-md"> Kirim Pengajuan</button>
                                            <button type="button" @click="open = false" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 transition">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="w-full bg-gray-100 text-gray-400 font-bold py-4 rounded-xl text-center border-2 border-dashed border-gray-200 cursor-not-allowed flex items-center justify-center gap-2">
                                🔒 Barang ini sudah selesai dikembalikan
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>