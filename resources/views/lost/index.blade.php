<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Barang Hilang Saya') }}
            </h2>
            <a href="{{ route('lost.create') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg text-sm shadow-md transition">
                + Lapor Kehilangan Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if($lostItems->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <div class="text-gray-400 text-6xl mb-4"></div>
                    <h3 class="text-lg font-medium text-gray-900">Belum ada laporan kehilangan</h3>
                    <p class="text-gray-500 mt-1">Jika kamu kehilangan barang, segera buat laporan baru.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($lostItems as $item)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition group">
                            {{-- Gambar --}}
                            <div class="h-48 bg-gray-200 w-full relative overflow-hidden">
                                @if($item->primaryImage)
                                    <img src="{{ asset('storage/' . $item->primaryImage->image_path) }}" alt="{{ $item->item_name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <div class="flex items-center justify-center h-full text-gray-400 text-4xl">📷</div>
                                @endif
                                <div class="absolute top-2 right-2 bg-white/90 backdrop-blur px-2 py-1 rounded text-xs font-bold shadow-sm">
                                    {{ $item->date_lost }}
                                </div>
                            </div>

                            {{-- Konten --}}
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-bold text-lg text-gray-800 truncate">{{ $item->item_name }}</h3>
                                    <span class="px-2 py-1 bg-red-100 text-red-600 text-xs rounded-full font-bold uppercase">{{ $item->status }}</span>
                                </div>
                                <p class="text-gray-500 text-sm mb-4 line-clamp-2">{{ $item->description }}</p>
                                
                                <div class="flex items-center text-gray-400 text-xs mb-4">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $item->location_lost }}
                                </div>

                                <a href="{{ route('lost.show', $item->id) }}" class="block w-full text-center bg-gray-50 hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 font-semibold py-2 rounded border border-gray-200 transition">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>