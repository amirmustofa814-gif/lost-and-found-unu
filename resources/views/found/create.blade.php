<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
           {{ __('Buat Laporan Penemuan Baru') }}
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

                <form method="POST" action="{{ route('found.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="border-b pb-4 mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Isi Detail Barang yang Ditemukan</h3>
                    </div>

                    {{-- Nama Barang --}}
                    <div>
                        <x-input-label for="item_name" :value="__('Nama Barang *')" />
                        <x-text-input id="item_name" class="block mt-1 w-full" type="text" name="item_name" :value="old('item_name')" required placeholder="Misal: Kunci Motor Honda" />
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <x-input-label for="category_id" :value="__('Kategori *')" />
                        <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach(\App\Models\Category::all() as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Lokasi Ditemukan --}}
                        <div>
                            <x-input-label for="location_found" :value="__('Lokasi Ditemukan *')" />
                            <x-text-input id="location_found" class="block mt-1 w-full" type="text" name="location_found" :value="old('location_found')" required placeholder="Misal: Parkiran Belakang" />
                        </div>
                        
                        {{-- Tanggal Ditemukan --}}
                        <div>
                            <x-input-label for="date_found" :value="__('Tanggal Ditemukan *')" />
                            <x-text-input id="date_found" class="block mt-1 w-full" type="date" name="date_found" :value="old('date_found')" required />
                        </div>
                    </div>

                    {{-- Jam Ditemukan --}}
                    <div>
                        <x-input-label for="time_found" :value="__('Jam Ditemukan *')" />
                        <x-text-input id="time_found" class="block mt-1 w-full" type="time" name="time_found" :value="old('time_found')" required />
                    </div>

                    {{-- Input Nomor HP --}}
                    <div>
                        <x-input-label for="phone_number" :value="__('Nomor HP / WhatsApp (Aktif) *')" />
                        <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number')" required placeholder="Contoh: 08123456789" />
                        <p class="text-xs text-gray-500 mt-1">*Nomor ini penting agar pemilik barang bisa menghubungi Anda.</p>
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <x-input-label for="description" :value="__('Deskripsi Detail *')" />
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Jelaskan warna, merek, kondisi, atau ciri khusus lainnya..." required>{{ old('description') }}</textarea>
                    </div>

                    {{-- Upload Foto (Min 2, Max 5) --}}
                    <div>
                        <x-input-label for="images" :value="__('Foto Barang (Wajib 2 - 5 Foto) *')" />
                        
                        <input type="file" 
                               name="images[]" 
                               id="images" 
                               multiple 
                               accept="image/*"
                               onchange="previewImages()"
                               class="mt-1 block w-full text-sm text-gray-500 
                                      file:mr-4 file:py-2 file:px-4 
                                      file:rounded-full file:border-0 
                                      file:text-sm file:font-semibold 
                                      file:bg-indigo-50 file:text-indigo-700 
                                      hover:file:bg-indigo-100 border border-gray-300 rounded-lg cursor-pointer">
                        
                        <p class="text-xs text-gray-500 mt-1">
                            Format: JPG, PNG. Maks 2MB/foto. Pilih minimal 2 foto sekaligus (Tekan CTRL saat memilih).
                        </p>
                        <x-input-error :messages="$errors->get('images')" class="mt-2" />
                        
                        <div id="image-preview-container" class="mt-4 grid grid-cols-3 gap-4 hidden">
                            </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t mt-6">
                        <a href="{{ route('found.index') }}" class="mr-4 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition">Batal</a>
                        <button type="submit" class="py-2 px-6 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition uppercase tracking-wide">
                           Simpan Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script Javascript untuk Preview --}}
    <script>
        function previewImages() {
            var previewContainer = document.getElementById('image-preview-container');
            var files = document.getElementById('images').files;

            // Reset preview
            previewContainer.innerHTML = '';
            
            if (files.length > 0) {
                previewContainer.classList.remove('hidden');
                
                // Loop semua file yang dipilih
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    var reader = new FileReader();

                    reader.onload = (function(f) {
                        return function(e) {
                            var div = document.createElement('div');
                            div.className = 'relative group border rounded-lg overflow-hidden h-24 shadow-sm';
                            div.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover transition transform hover:scale-110">';
                            previewContainer.appendChild(div);
                        };
                    })(file);

                    reader.readAsDataURL(file);
                }
            } else {
                previewContainer.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>