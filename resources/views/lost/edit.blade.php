<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
             {{ __('Edit Laporan Kehilangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('lost.update', $lostItem->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- PENTING: Method PUT untuk update --}}

                        {{-- Nama Barang --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Barang</label>
                            <input type="text" name="item_name" value="{{ old('item_name', $lostItem->item_name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        {{-- Kategori --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                            <select name="category_id" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                                @foreach(\App\Models\Category::all() as $cat)
                                    <option value="{{ $cat->id }}" {{ $lostItem->category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Lokasi --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Lokasi Hilang</label>
                            <input type="text" name="location_lost" value="{{ old('location_lost', $lostItem->location_lost) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        {{-- Tanggal --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Hilang</label>
                            <input type="date" name="date_lost" value="{{ old('date_lost', $lostItem->date_lost) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        {{-- Input Nomor HP --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nomor HP / WhatsApp</label>
                            <input type="text" name="phone_number" value="{{ old('phone_number', $lostItem->phone_number) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi Detail</label>
                            <textarea name="description" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>{{ old('description', $lostItem->description) }}</textarea>
                        </div>

                        {{-- Foto --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Ganti Foto (Opsional)</label>
                            @if($lostItem->images->count() > 0)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $lostItem->images->first()->image_path) }}" class="h-20 w-20 object-cover rounded border">
                                    <p class="text-xs text-gray-500 mt-1">Foto saat ini</p>
                                </div>
                            @endif
                            <input type="file" name="image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        {{-- Tombol --}}
                        <div class="flex items-center justify-between gap-4">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition">
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('lost.show', $lostItem->id) }}" class="text-gray-500 hover:text-gray-700 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Batal
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>