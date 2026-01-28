<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Edit Laporan Penemuan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                
                {{-- TAMPILKAN ERROR JIKA ADA --}}
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

                <form method="POST" action="{{ route('found.update', $foundItem->id) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT') {{-- PENTING: Method PUT untuk Update --}}

                    <div class="border-b pb-4 mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Perbarui Data Barang Temuan</h3>
                    </div>

                    {{-- Nama Barang --}}
                    <div>
                        <label for="item_name" class="block text-sm font-medium text-gray-700">Nama Barang *</label>
                        <input type="text" name="item_name" id="item_name" value="{{ old('item_name', $foundItem->item_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori *</label>
                        <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach(\App\Models\Category::all() as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $foundItem->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Lokasi Ditemukan --}}
                        <div>
                            <label for="location_found" class="block text-sm font-medium text-gray-700">Lokasi Ditemukan *</label>
                            <input type="text" name="location_found" id="location_found" value="{{ old('location_found', $foundItem->location_found) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        
                        {{-- Tanggal Ditemukan --}}
                        <div>
                            <label for="date_found" class="block text-sm font-medium text-gray-700">Tanggal Ditemukan *</label>
                            <input type="date" name="date_found" id="date_found" value="{{ old('date_found', $foundItem->date_found) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                    </div>
                    
                        {{-- Jam Ditemukan --}}
                        <div>
                            <x-input-label for="time_found" :value="__('Jam Ditemukan')" />
                            <x-text-input id="time_found" class="block mt-1 w-full" type="time" name="time_found" :value="old('time_found', $foundItem->time_found)" required />
                            <x-input-error :messages="$errors->get('time_found')" class="mt-2" />
                        </div>

                    {{-- Input Nomor HP (WAJIB) --}}
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Nomor HP / WhatsApp (Aktif) *</label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $foundItem->phone_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <p class="text-xs text-gray-500 mt-1">*Nomor ini penting agar pemilik barang bisa menghubungi Anda.</p>
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Detail *</label>
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('description', $foundItem->description) }}</textarea>
                    </div>

                    {{-- Posisi Barang Sekarang (KHUSUS FOUND ITEM) --}}
                    {{-- Pastikan kolom 'current_position' ada di database & fillable model --}}
                    @if(Schema::hasColumn('found_items', 'current_position'))
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                        <label for="current_position" class="block text-sm font-bold text-indigo-700">Dimana Barang Ini Sekarang? *</label>
                        <input type="text" name="current_position" id="current_position" value="{{ old('current_position', $foundItem->current_position ?? '') }}" class="mt-1 block w-full rounded-md border-indigo-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Misal: Saya titipkan di Pos Satpam">
                    </div>
                    @endif

                    {{-- Upload Foto --}}
                    <div class="border-t pt-4">
                        <label for="image" class="block text-sm font-medium text-gray-700">Ganti Foto (Opsional)</label>
                        
                        {{-- Tampilkan foto lama jika ada --}}
                        @if($foundItem->primaryImage)
                            <div class="my-2 p-2 border rounded-lg inline-block bg-gray-50">
                                <p class="text-xs text-gray-500 mb-1">Foto Saat Ini:</p>
                                <img src="{{ asset('storage/' . $foundItem->primaryImage->image_path) }}" class="h-24 w-auto rounded object-cover">
                            </div>
                        @endif

                        <input type="file" name="image" id="image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-500 mt-1">Upload foto baru hanya jika ingin menggantinya.</p>
                    </div>

                    <div class="flex justify-end pt-4 border-t mt-6">
                        <a href="{{ route('found.show', $foundItem->id) }}" class="mr-4 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition">Batal</a>
                        <button type="submit" class="py-2 px-6 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition uppercase tracking-wide">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>