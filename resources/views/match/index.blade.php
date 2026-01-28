<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pencocokan Cerdas (Smart Match)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(empty($results))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <div class="text-gray-300 text-6xl mb-4"></div>
                    <h3 class="text-lg font-medium text-gray-900">Belum ada kecocokan ditemukan</h3>
                    <p class="text-gray-500 mt-1">Sistem belum menemukan barang temuan yang mirip dengan barang hilangmu saat ini.</p>
                </div>
            @else
                <div class="space-y-12">
                    @foreach($results as $match)
                        {{-- CARD UTAMA: Barang Hilang --}}
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                            <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="bg-red-100 text-red-600 py-1 px-3 rounded-full text-xs font-bold uppercase">
                                        @if(Auth::user()->role === 'admin')
                                            Dicari Oleh: {{ $match['lost_item']->user->name }}
                                        @else
                                            Barang Kamu
                                        @endif
                                    </span>
                                    <h3 class="font-bold text-lg text-gray-800">{{ $match['lost_item']->item_name }}</h3>
                                </div>
                                
                                {{-- Info Tambahan untuk Admin --}}
                                @if(Auth::user()->role === 'admin')
                                    <div class="text-xs text-gray-500">
                                        📞 {{ $match['lost_item']->user->email }}
                                    </div>
                                @endif
                            </div>
                            
                            <div class="p-6">
                                <h4 class="font-bold text-gray-700 mb-4 flex items-center">
                                    <span class="mr-2"></span> Ditemukan {{ $match['candidates']->count() }} Kandidat Cocok:
                                </h4>

                                {{-- LIST KANDIDAT: Barang Temuan Orang Lain --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach($match['candidates'] as $candidate)
                                        <div class="border-2 border-indigo-100 rounded-xl p-4 hover:border-indigo-500 transition relative bg-indigo-50/30 flex flex-col h-full">
                                            
                                            {{-- Label Persentase (Gimmick UI) --}}
                                            <div class="absolute top-0 right-0 bg-indigo-600 text-white text-xs font-bold px-2 py-1 rounded-bl-xl rounded-tr-xl z-10">
                                                Potensi Cocok
                                            </div>

                                            {{-- Bagian Atas: Info Barang --}}
                                            <div class="flex gap-4 items-start mb-4">
                                                {{-- Gambar Kandidat --}}
                                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex-shrink-0 overflow-hidden border">
                                                    @if($candidate->primaryImage)
                                                        <img src="{{ asset('storage/' . $candidate->primaryImage->image_path) }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="flex items-center justify-center h-full text-xs text-gray-400">No img</div>
                                                    @endif
                                                </div>

                                                {{-- Info Kandidat --}}
                                                <div>
                                                    <h5 class="font-bold text-gray-900 leading-tight">{{ $candidate->item_name }}</h5>
                                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $candidate->description }}</p>
                                                    <p class="text-xs text-indigo-600 font-semibold mt-1">
                                                        📍 {{ $candidate->location_found }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Bagian Bawah: Aksi --}}
                                            <div class="mt-auto pt-4 border-t border-indigo-100">
                                                
                                                @if(Auth::user()->role !== 'admin')
                                                    {{-- FORM KLAIM UNTUK USER BIASA --}}
                                                    <form action="{{ route('claims.store') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="found_item_id" value="{{ $candidate->id }}">
                                                        
                                                        <div class="space-y-2">
                                                            <textarea name="description" rows="1" class="w-full text-xs border-gray-300 rounded focus:border-indigo-500 focus:ring-indigo-500" placeholder="Pesan untuk penemu..." required></textarea>
                                                            
                                                            <div>
                                                                <label class="block text-xs font-bold text-gray-600 mb-1">Upload Bukti (Opsional)</label>
                                                                <input type="file" name="proof_image" accept="image/*" class="block w-full text-xs text-gray-500
                                                                  file:mr-2 file:py-1 file:px-2
                                                                  file:rounded-full file:border-0
                                                                  file:text-xs file:font-semibold
                                                                  file:bg-indigo-100 file:text-indigo-700
                                                                  hover:file:bg-indigo-200
                                                                ">
                                                            </div>

                                                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg text-sm shadow transition duration-200 mt-2">
                                                                Ajukan Klaim 
                                                            </button>
                                                        </div>
                                                    </form>
                                                @else
                                                    {{-- TOMBOL LIHAT UNTUK ADMIN --}}
                                                    <div class="text-center space-y-2">
                                                        <p class="text-xs text-gray-500 italic">
                                                            Mode Admin: Anda hanya memantau.
                                                        </p>
                                                        <a href="{{ route('found.show', $candidate->id) }}" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg text-xs transition border border-gray-300">
                                                             Cek Detail Barang Temuan
                                                        </a>
                                                    </div>
                                                @endif

                                            </div>
                                            
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>