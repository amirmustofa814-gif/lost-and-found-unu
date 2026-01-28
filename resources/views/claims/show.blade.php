<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Klaim Barang') }} 
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Status Banner --}}
            <div class="mb-6 text-center">
                @if($claim->status == 'pending')
                    <span class="bg-yellow-100 text-yellow-800 text-lg font-bold px-4 py-2 rounded-full">⏳ Menunggu Verifikasi</span>
                @elseif($claim->status == 'verified')
                    <span class="bg-green-100 text-green-800 text-lg font-bold px-4 py-2 rounded-full">✅ Klaim Diterima (Sah)</span>
                @elseif($claim->status == 'rejected')
                    <span class="bg-red-100 text-red-800 text-lg font-bold px-4 py-2 rounded-full">❌ Klaim Ditolak</span>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col md:flex-row">
                
                {{-- Foto Barang Asli --}}
                <div class="md:w-1/2 bg-gray-100 p-4 flex flex-col items-center justify-center border-r">
                    <h4 class="font-bold text-gray-500 mb-2">FOTO BARANG TEMUAN</h4>
                    @if($claim->foundItem->primaryImage)
                        <img src="{{ asset('storage/' . $claim->foundItem->primaryImage->image_path) }}" class="rounded-lg shadow max-h-80 object-contain">
                    @else
                        <div class="text-gray-400">Tidak ada foto barang</div>
                    @endif
                </div>

                {{-- Detail Info Klaim --}}
                <div class="md:w-1/2 p-6">
                    <h3 class="text-2xl font-bold mb-1">{{ $claim->foundItem->item_name }}</h3>
                    <p class="text-gray-600 mb-4 text-sm">📍 Ditemukan di: {{ $claim->foundItem->location_found }}</p>

                    <hr class="my-4 border-gray-200">

                    <div class="bg-blue-50 p-4 rounded-lg mb-4">
                        <h4 class="font-bold text-blue-800 text-sm uppercase mb-2">Data Pengklaim</h4>
                        <p class="font-bold text-lg">{{ $claim->user->name }}</p>
                        <p class="text-sm text-gray-600">{{ $claim->user->email }}</p>
                        <p class="text-sm text-gray-600">📞 {{ $claim->user->phone_number ?? '-' }}</p>
                    </div>

                    <h4 class="font-bold text-gray-700 mt-4">Pesan / Ciri-ciri:</h4>
                    <p class="bg-gray-50 p-3 rounded border text-gray-700 italic mb-4">"{{ $claim->description }}"</p>

                    {{-- TAMPILKAN FOTO BUKTI (FITUR BARU) --}}
                    @if($claim->proof_image)
                        <div class="mt-4 border p-3 rounded-lg bg-gray-50">
                            <span class="text-xs font-bold text-gray-500 block mb-2 uppercase">📸 Foto Bukti Kepemilikan:</span>
                            <a href="{{ asset('storage/' . $claim->proof_image) }}" target="_blank">
                                <img src="{{ asset('storage/' . $claim->proof_image) }}" alt="Bukti Foto" class="w-full rounded shadow hover:opacity-90 transition cursor-zoom-in">
                            </a>
                        </div>
                    @else
                        <p class="text-xs text-gray-400 italic mt-2">*Tidak ada foto bukti dilampirkan.</p>
                    @endif

                    {{-- Tombol Aksi (Hanya muncul untuk PENEMU barang) --}}
                    @if($isFinder && $claim->status == 'pending')
                        <div class="mt-8 grid grid-cols-2 gap-4">
                            {{-- Tombol Tolak --}}
                            <form action="{{ route('claims.reject', $claim->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak klaim ini?');">
                                @csrf
                                <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow">
                                    Tolak ❌
                                </button>
                            </form>

                            {{-- Tombol Terima --}}
                            <form action="{{ route('claims.verify', $claim->id) }}" method="POST" onsubmit="return confirm('Yakin barang ini milik dia? Transaksi akan selesai.');">
                                @csrf
                                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded shadow">
                                    Terima ✅
                                </button>
                            </form>
                        </div>
                    @endif
                    
                    @if($isRequester && $claim->status == 'pending')
                         <div class="mt-6 p-3 bg-yellow-50 text-yellow-700 text-sm rounded border border-yellow-200">
                            Menunggu penemu barang memverifikasi bukti kamu.
                         </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>