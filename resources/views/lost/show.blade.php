<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Laporan Kehilangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('lost.index') }}" class="text-indigo-600 hover:text-indigo-800 mb-4 inline-block font-medium">&larr; Kembali ke Daftar</a>

            <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl grid grid-cols-1 md:grid-cols-2">
                
                {{-- Bagian Gambar (Kiri/Atas) --}}
                <div class="bg-gray-100 relative h-64 md:h-auto">
                    @if($lostItem->images->count() > 0)
                        <img src="{{ asset('storage/' . $lostItem->images->first()->image_path) }}" alt="{{ $lostItem->item_name }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex items-center justify-center h-full text-gray-400">
                            <span class="text-5xl">📷</span>
                        </div>
                    @endif
                </div>

                {{-- Bagian Info (Kanan/Bawah) --}}
                <div class="p-8">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide font-semibold">{{ $lostItem->category->name ?? 'Uncategorized' }}</p>
                            <h1 class="text-3xl font-bold text-gray-900 mt-1">{{ $lostItem->item_name }}</h1>
                        </div>
                        <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-sm font-bold uppercase shadow-sm">
                            {{ $lostItem->status }}
                        </span>
                    </div>

                    <div class="mt-6 space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Deskripsi</h4>
                            <p class="text-gray-800 mt-1 leading-relaxed">{{ $lostItem->description }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 border-t border-gray-100 pt-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Tanggal Hilang</h4>
                                <p class="text-gray-800 font-medium">{{ \Carbon\Carbon::parse($lostItem->date_lost)->format('d F Y') }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Lokasi</h4>
                                <p class="text-gray-800 font-medium flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $lostItem->location_lost }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="mt-8 pt-6 border-t border-gray-100 flex gap-3">
                        <a href="{{ route('lost.edit', $lostItem->id) }}" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow transition text-center">
                            Edit Laporan
                        </a>
                        </button>
                        <form action="{{ route('lost.destroy', $lostItem->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus laporan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-white border border-gray-300 text-red-600 hover:bg-red-50 font-bold py-2 px-4 rounded-lg transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>