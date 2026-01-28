<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Pusat Transaksi & Validasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- ========================================================== --}}
            {{-- SECTION 1: KLAIM MASUK (Untuk Admin & Penemu Barang)       --}}
            {{-- ========================================================== --}}
            <div class="mb-12">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <span class="flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg text-sm">📥</span>
                        Klaim Masuk
                    </h3>
                    @if($incomingClaims->where('status', 'pending')->count() > 0)
                        <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full animate-pulse">
                            {{ $incomingClaims->where('status', 'pending')->count() }} Pending
                        </span>
                    @endif
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-400 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-4">Barang Temuan</th>
                                    <th class="px-6 py-4">Pemohon</th>
                                    <th class="px-6 py-4">Bukti / Ciri Rahasia</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($incomingClaims as $claim)
                                <tr class="bg-white border-b hover:bg-gray-50 transition">
                                    {{-- Kolom Barang --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden border">
                                                @if($claim->foundItem->primaryImage)
                                                    <img src="{{ asset('storage/' . $claim->foundItem->primaryImage->image_path) }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="flex items-center justify-center h-full text-xs text-gray-400">No Pic</div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-800">{{ $claim->foundItem->item_name }}</div>
                                                <div class="text-xs text-gray-400">{{ $claim->foundItem->date_found }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom Pemohon --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                                {{ substr($claim->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-800">{{ $claim->user->name }}</div>
                                                <a href="mailto:{{ $claim->user->email }}" class="text-xs text-indigo-500 hover:underline flex items-center gap-1 mt-1">
                                                    ✉️ {{ $claim->user->email }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom Bukti --}}
                                    <td class="px-6 py-4">
                                        <div class="bg-yellow-50 text-yellow-800 p-3 rounded-lg border border-yellow-100 text-xs italic max-w-xs mb-2">
                                            "{{ $claim->description }}"
                                        </div>
                                        @if($claim->proof_image_path)
                                            <div class="group relative w-16 h-16">
                                                <a href="{{ asset('storage/' . $claim->proof_image_path) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $claim->proof_image_path) }}" 
                                                         class="w-full h-full object-cover rounded-lg border border-gray-200 shadow-sm hover:scale-150 transition-transform duration-300 z-10 relative"
                                                         title="Klik untuk memperbesar">
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Tidak ada foto bukti</span>
                                        @endif
                                    </td>

                                    {{-- Kolom Status --}}
                                    <td class="px-6 py-4 text-center">
                                        @if($claim->status == 'pending')
                                            <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full">⏳ Menunggu</span>
                                        @elseif($claim->status == 'verified')
                                            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">✅ Diterima</span>
                                        @else
                                            <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full">❌ Ditolak</span>
                                        @endif
                                    </td>

                                    {{-- Kolom Aksi --}}
                                    <td class="px-6 py-4 text-center">
                                        @if($claim->status == 'pending')
                                            @if(Auth::id() === $claim->foundItem->user_id)
                                                <div x-data="{ showModal: false }" class="flex justify-center gap-2">
                                                    
                                                    <button @click="showModal = true" class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-lg shadow transition" title="Terima & Upload Bukti">
                                                        📸 Terima
                                                    </button>

                                                    <form action="{{ route('claims.verify', $claim->id) }}" method="POST" onsubmit="return confirm('Tolak klaim ini?');">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="action" value="reject">
                                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg shadow transition" title="Tolak">
                                                            ❌
                                                        </button>
                                                    </form>

                                                    {{-- Modal Upload --}}
                                                    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                                        <div class="fixed inset-0 bg-gray-900 opacity-75"></div>
                                                        <div class="flex items-center justify-center min-h-screen px-4 text-center">
                                                            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full relative z-50 p-6 text-left">
                                                                <h3 class="text-lg font-bold text-gray-900 mb-2">📸 Upload Bukti Serah Terima</h3>
                                                                <div class="bg-blue-50 text-blue-800 p-3 rounded-md text-sm mb-4">
                                                                    <strong>Pastikan Anda SUDAH bertemu dengan pemilik barang.</strong><br>
                                                                    Upload foto serah terima di sini sebagai bukti transaksi selesai.
                                                                </div>
                                                                <form action="{{ route('claims.verify', $claim->id) }}" method="POST" enctype="multipart/form-data">
                                                                    @csrf @method('PATCH')
                                                                    <input type="hidden" name="action" value="verify">
                                                                    <div class="mb-4">
                                                                        <label class="block text-sm font-bold text-gray-700 mb-2">Foto Bukti *</label>
                                                                        <input type="file" name="handover_photo" required accept="image/*" class="block w-full text-sm border border-gray-300 rounded-lg">
                                                                    </div>
                                                                    <div class="flex justify-end gap-3 mt-6 border-t pt-4">
                                                                        <button type="button" @click="showModal = false" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg">Batal</button>
                                                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">✅ Simpan</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400 italic bg-gray-100 px-2 py-1 rounded">
                                                    Menunggu konfirmasi penemu
                                                </span>
                                            @endif

                                        @elseif($claim->status == 'verified')
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs font-bold text-green-600 mb-1">SELESAI</span>
                                                @if($claim->handover_photo_path)
                                                    <a href="{{ asset('storage/' . $claim->handover_photo_path) }}" target="_blank" class="text-xs text-indigo-500 underline hover:text-indigo-700 font-semibold">
                                                        Bukti Serah Terima
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs font-bold text-red-600">DITOLAK</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-400">Belum ada klaim masuk.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ========================================================== --}}
            {{-- SECTION 2: STATUS PENGAJUAN SAYA (HANYA USER BIASA)        --}}
            {{-- ========================================================== --}}
            @if(Auth::user()->role !== 'admin')
                <div class="mt-12">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="flex items-center justify-center w-8 h-8 bg-green-100 text-green-600 rounded-lg text-sm">📤</span>
                        Status Pengajuan Saya
                    </h3>

                    @if($myClaims->isEmpty())
                        <div class="bg-white rounded-2xl p-8 border border-gray-100 text-center shadow-sm">
                            <p class="text-gray-500">Kamu belum pernah mengajukan klaim.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($myClaims as $myClaim)
                                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition relative overflow-hidden">
                                    {{-- Status Stripe --}}
                                    <div class="absolute left-0 top-0 bottom-0 w-1.5 
                                        {{ $myClaim->status === 'pending' ? 'bg-yellow-400' : ($myClaim->status === 'verified' ? 'bg-green-500' : 'bg-red-500') }}">
                                    </div>
                                    
                                    <div class="pl-4 flex gap-4">
                                        {{-- Foto Barang --}}
                                        <div class="w-16 h-16 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                            @if($myClaim->foundItem->primaryImage)
                                                <img src="{{ asset('storage/' . $myClaim->foundItem->primaryImage->image_path) }}" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        
                                        {{-- Info --}}
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-800">{{ $myClaim->foundItem->item_name }}</h4>
                                            <p class="text-xs text-gray-500 mb-2">Diajukan: {{ $myClaim->created_at->diffForHumans() }}</p>
                                            
                                            {{-- STATUS & AKSI --}}
                                            <div class="mt-2">
                                                
                                                {{-- KONDISI PENDING --}}
                                                @if($myClaim->status === 'pending')
                                                    <div class="flex flex-col items-start gap-2">
                                                        <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded">⏳ Menunggu Respon</span>
                                                        
                                                        {{-- ✅✅ BAGIAN INI YANG DITAMBAHKAN (TOMBOL WA) --}}
                                                        @if($myClaim->foundItem->phone_number)
                                                            <a href="https://wa.me/{{ $myClaim->foundItem->phone_number }}" target="_blank" class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-1.5 px-3 rounded-lg transition shadow-sm">
                                                                <span>📞</span> Hubungi Penemu
                                                            </a>
                                                        @endif
                                                        {{-- ✅✅ SELESAI PENAMBAHAN --}}
                                                    </div>

                                                {{-- KONDISI VERIFIED --}}
                                                @elseif($myClaim->status === 'verified')
                                                    <div class="flex flex-col items-start gap-2">
                                                        <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded">✅ Klaim Diterima!</span>
                                                        <span class="text-xs text-gray-400">Transaksi Selesai</span>
                                                    </div>

                                                {{-- KONDISI DITOLAK --}}
                                                @else
                                                    <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded">❌ Klaim Ditolak</span>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif 

        </div>
    </div>
</x-app-layout>