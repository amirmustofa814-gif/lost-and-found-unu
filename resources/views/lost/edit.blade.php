<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Laporan Kehilangan') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700">
                <div class="p-8">

                    {{-- Form Edit --}}
                    <form action="{{ route('lost.update', $lostItem->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- 1. NAMA BARANG (Full Width) --}}
                        <div class="mb-6">
                            <label for="item_name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nama Barang</label>
                            <input type="text" name="item_name" id="item_name" 
                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                                value="{{ old('item_name', $lostItem->item_name) }}" required>
                        </div>

                        {{-- 2. GRID 2 KOLOM (Kategori & Tanggal) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Kategori --}}
                            <div>
                                <label for="category_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kategori</label>
                                <select name="category_id" id="category_id" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $lostItem->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tanggal Hilang --}}
                            <div>
                                <label for="date_lost" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tanggal Hilang</label>
                                <input type="date" name="date_lost" id="date_lost" 
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                                    value="{{ old('date_lost', $lostItem->date_lost) }}" required>
                            </div>
                        </div>

                        {{-- 3. GRID 2 KOLOM (Lokasi & No HP) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Lokasi Hilang --}}
                            <div>
                                <label for="location_lost" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Lokasi Hilang</label>
                                <input type="text" name="location_lost" id="location_lost" 
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                                    value="{{ old('location_lost', $lostItem->location_lost) }}" required>
                            </div>

                            {{-- Nomor HP --}}
                            <div>
                                <label for="phone_number" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nomor HP / WhatsApp</label>
                                <input type="text" name="phone_number" id="phone_number" 
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                                    value="{{ old('phone_number', $lostItem->phone_number) }}" placeholder="08..." required>
                            </div>
                        </div>

                        {{-- 4. DESKRIPSI (Full Width) --}}
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Deskripsi Detail</label>
                            <textarea name="description" id="description" rows="4" 
                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $lostItem->description) }}</textarea>
                        </div>

                        {{-- 5. FOTO (Preview & Input) --}}
                        <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Foto Barang</label>
                            
                            <div class="flex flex-col sm:flex-row items-center gap-6">
                                {{-- Preview Foto Lama --}}
                                <div class="shrink-0">
                                    @if($lostItem->primaryImage)
                                        <div class="relative group">
                                            <img src="{{ asset('storage/' . $lostItem->primaryImage->image_path) }}" alt="Foto Barang" class="w-32 h-32 object-cover rounded-lg shadow-md border border-gray-200">
                                            <div class="absolute inset-0 bg-black/50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition text-white text-xs font-bold">
                                                Foto Saat Ini
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-xs text-center p-2">
                                            Belum ada foto
                                        </div>
                                    @endif
                                </div>

                                {{-- Input Ganti Foto --}}
                                <div class="flex-1 w-full">
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Ganti Foto (Opsional)</label>
                                    <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" 
                                        name="images[]" id="file_input" type="file">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">Biarkan kosong jika tidak ingin mengubah foto.</p>
                                </div>
                            </div>
                        </div>

                        {{-- 6. TOMBOL AKSI (Batal & Simpan) --}}
                        <div class="flex justify-end items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            {{-- Tombol Batal --}}
                            <a href="{{ route('lost.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition shadow-sm">
                                Batal
                            </a>
                            
                            {{-- Tombol Simpan --}}
                            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                                Simpan Perubahan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>