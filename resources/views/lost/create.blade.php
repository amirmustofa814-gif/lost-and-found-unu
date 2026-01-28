<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Laporan Kehilangan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                
                {{-- TAMPILKAN ERROR JIKA ADA (Biar tidak bingung kalau gagal simpan) --}}
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg relative" role="alert">
                        <strong class="font-bold flex items-center gap-2">
                            <span>⚠️</span> Ada kesalahan input!
                        </strong>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('lost.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    {{-- Nama Barang --}}
                    <div>
                        <label for="item_name" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                        <input type="text" name="item_name" id="item_name" value="{{ old('item_name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required placeholder="Contoh: Laptop Asus ROG">
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                        <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            {{-- Pastikan data kategori ada di DB --}}
                            @foreach(\App\Models\Category::all() as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Tanggal --}}
                        <div>
                            <label for="date_lost" class="block text-sm font-medium text-gray-700">Tanggal Hilang</label>
                            <input type="date" name="date_lost" id="date_lost" value="{{ old('date_lost') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        {{-- Lokasi --}}
                        <div>
                            <label for="location_lost" class="block text-sm font-medium text-gray-700">Lokasi Hilang</label>
                            <input type="text" name="location_lost" id="location_lost" value="{{ old('location_lost') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: Kantin, Lab 3" required>
                        </div>
                    </div>

                    {{-- Input Nomor HP (BARU) --}}
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Nomor HP / WhatsApp (Aktif)</label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: 08123456789" required>
                        <p class="text-xs text-gray-500 mt-1">*Nomor ini hanya akan ditampilkan kepada orang yang terverifikasi (klaim diterima).</p>
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Detail</label>
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Warna, ciri khusus, stiker, dll...">{{ old('description') }}</textarea>
                    </div>

                    {{-- Upload Foto --}}
                    <div>
                        <label for="images" class="block text-sm font-medium text-gray-700">Foto Barang (Opsional)</label>
                        <input type="file" name="images[]" id="images" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>

                    <div class="flex justify-end pt-4">
                        <a href="{{ route('lost.index') }}" class="mr-4 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">Batal</a>
                        <button type="submit" class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Simpan Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>