<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Transaksi Klaim') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Pesan Sukses --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg flex items-center gap-2">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- ========================================================== --}}
            {{-- BAGIAN 1: KLAIM MASUK (Untuk Penemu / Admin)               --}}
            {{-- ========================================================== --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700 mb-12">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        📥 Klaim Masuk 
                        @if(isset($incomingClaims) && $incomingClaims->where('status', 'pending')->count() > 0)
                            <span class="text-xs bg-red-500 text-white px-2 py-1 rounded-full animate-pulse">
                                {{ $incomingClaims->where('status', 'pending')->count() }} Perlu Respon
                            </span>
                        @endif
                    </h3>
                    
                    @if(!isset($incomingClaims) || $incomingClaims->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400">Belum ada klaim masuk untuk barang temuanmu.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 uppercase font-bold text-xs">
                                    <tr>
                                        <th class="p-4">Barang Temuan</th>
                                        <th class="p-4">Pemohon</th>
                                        <th class="p-4">Ringkasan Bukti</th>
                                        <th class="p-4 text-center">Status</th>
                                        <th class="p-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach($incomingClaims as $claim)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        
                                        {{-- INFO BARANG --}}
                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 rounded-lg overflow-hidden border dark:border-gray-600 shrink-0">
                                                    @if($claim->foundItem->primaryImage)
                                                        <img src="{{ asset('storage/' . $claim->foundItem->primaryImage->image_path) }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center text-xs">No Pic</div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900 dark:text-white line-clamp-1 w-32">{{ $claim->foundItem->item_name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $claim->created_at->format('d M Y') }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- PEMOHON --}}
                                        <td class="p-4">
                                            <div class="font-bold text-gray-800 dark:text-gray-200 text-xs">{{ $claim->user->name }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $claim->user->email }}</div>
                                        </td>

                                        {{-- RINGKASAN BUKTI --}}
                                        <td class="p-4 max-w-xs">
                                            <div class="text-xs italic text-gray-500 truncate mb-1">
                                                "{{ Str::limit($claim->description, 30) }}"
                                            </div>
                                            @if($claim->proof_image_path)
                                                <span class="text-[10px] bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded border border-indigo-100">
                                                    📸 Ada Foto
                                                </span>
                                            @endif
                                        </td>

                                        {{-- STATUS --}}
                                        <td class="p-4 text-center">
                                            <span class="px-2 py-1 rounded text-xs font-bold 
                                            {{ $claim->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : ($claim->status == 'approved' ? 'bg-blue-100 text-blue-700' : ($claim->status == 'verified' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700')) }}">
                                                {{ ucfirst($claim->status) }}
                                            </span>
                                        </td>

                                        {{-- AKSI --}}
                                        <td class="p-4 text-center">
                                            <div x-data="{ showModal: false }">
                                                
                                                {{-- 1. TOMBOL (PENEMU) --}}
                                                @if($claim->status == 'pending' && Auth::id() === $claim->foundItem->user_id)
                                                    <div class="flex items-center justify-center gap-2">
                                                        <form action="{{ route('claims.verify', $claim->id) }}" method="POST">
                                                            @csrf @method('PATCH')
                                                            <input type="hidden" name="action" value="approve">
                                                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg shadow text-xs font-bold transition flex items-center gap-1">📸 Terima</button>
                                                        </form>
                                                        <form action="{{ route('claims.verify', $claim->id) }}" method="POST" onsubmit="return confirm('Tolak klaim ini?');">
                                                            @csrf @method('PATCH')
                                                            <input type="hidden" name="action" value="reject">
                                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-lg shadow transition flex items-center justify-center font-bold">✕</button>
                                                        </form>
                                                        <button @click="showModal = true" class="bg-blue-500 hover:bg-blue-600 text-white w-8 h-8 rounded-lg shadow transition flex items-center justify-center font-bold" title="Lihat Detail">👁️</button>
                                                    </div>

                                                {{-- 2. TOMBOL (ADMIN) --}}
                                                @elseif($claim->status == 'approved' && Auth::user()->role === 'admin')
                                                    <div class="flex items-center justify-center gap-2">
                                                        <button @click="showModal = true" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg shadow text-xs font-bold transition animate-pulse">Selesaikan</button>
                                                        <button @click="showModal = true" class="bg-blue-500 hover:bg-blue-600 text-white w-8 h-8 rounded-lg shadow transition flex items-center justify-center font-bold" title="Lihat Detail">👁️</button>
                                                    </div>

                                                {{-- 3. STATUS LAIN --}}
                                                @elseif($claim->status == 'verified')
                                                    <span class="text-green-600 text-lg">✅</span>
                                                @elseif($claim->status == 'rejected')
                                                    <span class="text-red-600 text-lg">❌</span>
                                                @else
                                                    <span class="text-gray-400 text-xs italic">Menunggu..</span>
                                                @endif

                                                {{-- ================================================= --}}
                                                {{-- MODAL POPUP (DETAIL LENGKAP + PERBAIKAN FOTO)     --}}
                                                {{-- ================================================= --}}
                                                <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
                                                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden relative" @click.away="showModal = false">
                                                        
                                                        <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b dark:border-gray-600 flex justify-between items-center">
                                                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">🔍 Detail & Validasi Klaim</h3>
                                                            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 font-bold text-xl">&times;</button>
                                                        </div>

                                                        <div class="p-6 overflow-y-auto max-h-[75vh]">
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                                {{-- KOLOM KIRI: DATA KLAIM --}}
                                                                <div>
                                                                    <h4 class="text-xs font-bold text-gray-400 uppercase mb-3 border-b pb-1">Data Klaim (Saat Ini)</h4>
                                                                    <div class="mb-4">
                                                                        <label class="block text-xs font-bold text-gray-500 mb-1">Pesan / Ciri Rahasia:</label>
                                                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg text-sm italic text-gray-800 dark:text-gray-200 border border-yellow-100">
                                                                            "{{ $claim->description }}"
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-4">
                                                                        <label class="block text-xs font-bold text-gray-500 mb-1">Foto Bukti:</label>
                                                                        @if($claim->proof_image_path)
                                                                            <img src="{{ asset('storage/' . $claim->proof_image_path) }}" class="w-full h-32 object-cover rounded-lg border border-gray-200 cursor-pointer" onclick="window.open(this.src)">
                                                                        @else
                                                                            <div class="h-24 bg-gray-100 flex items-center justify-center text-xs text-gray-400 rounded-lg">Tidak ada foto bukti</div>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                {{-- KOLOM KANAN: LAPORAN KEHILANGAN TERKAIT (PERBAIKAN FOTO) --}}
                                                                <div>
                                                                    <h4 class="text-xs font-bold text-red-400 uppercase mb-3 border-b pb-1">📄 Laporan Kehilangan (Referensi)</h4>
                                                                    
                                                                    @php
                                                                        // Cari laporan kehilangan yang cocok
                                                                        $matchedLostItem = $claim->user->lostItems
                                                                            ->where('category_id', $claim->foundItem->category_id)
                                                                            ->where('status', 'dicari')
                                                                            ->sortByDesc('created_at')
                                                                            ->first();
                                                                    @endphp

                                                                    @if($matchedLostItem)
                                                                        <div class="bg-red-50 dark:bg-red-900/10 p-3 rounded-lg border border-red-100 dark:border-red-800">
                                                                            
                                                                            {{-- LOGIKA BARU: Cek Primary Image, jika null cek Images Collection --}}
                                                                            @php
                                                                                $lostImage = $matchedLostItem->primaryImage ?? $matchedLostItem->images->first();
                                                                            @endphp

                                                                            @if($lostImage)
                                                                                <img src="{{ asset('storage/' . $lostImage->image_path) }}" class="w-full h-32 object-cover rounded-lg mb-2 border border-red-200 cursor-pointer hover:opacity-90" onclick="window.open(this.src)">
                                                                            @else
                                                                                <div class="h-24 bg-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-500 mb-2">
                                                                                    No Image Found
                                                                                </div>
                                                                            @endif

                                                                            <h5 class="font-bold text-sm text-gray-800 dark:text-white">{{ $matchedLostItem->item_name }}</h5>
                                                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 line-clamp-3">{{ $matchedLostItem->description }}</p>
                                                                            <div class="mt-2 pt-2 border-t border-red-100 text-[10px] text-gray-500">
                                                                                <p>📅 Hilang: {{ \Carbon\Carbon::parse($matchedLostItem->date_lost)->format('d M Y') }}</p>
                                                                                <p>📍 Lokasi: {{ $matchedLostItem->location }}</p>
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center border border-dashed border-gray-300">
                                                                            <p class="text-xs text-gray-500">Pengklaim ini tidak memiliki laporan kehilangan aktif untuk kategori barang ini.</p>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            {{-- BAGIAN BAWAH: ADMIN UPLOAD --}}
                                                            @if($claim->status == 'approved' && Auth::user()->role === 'admin')
                                                                <form action="{{ route('claims.verify', $claim->id) }}" method="POST" enctype="multipart/form-data" class="border-t pt-4 mt-4">
                                                                    @csrf @method('PATCH')
                                                                    <input type="hidden" name="action" value="verify">
                                                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Upload Foto Serah Terima (Admin):</label>
                                                                    <input type="file" name="handover_photo" required class="block w-full text-sm mb-4 border rounded p-2">
                                                                    <button type="submit" class="w-full py-2 bg-green-600 hover:bg-green-700 text-white rounded font-bold">Simpan & Selesaikan Transaksi</button>
                                                                </form>
                                                            @endif

                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- END MODAL --}}

                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ========================================================== --}}
            {{-- BAGIAN 2: STATUS PENGAJUAN SAYA (myClaims)                 --}}
            {{-- ========================================================== --}}
            @if(Auth::user()->role !== 'admin')
                <div class="mt-8">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <span class="flex items-center justify-center w-8 h-8 bg-green-100 text-green-600 rounded-lg text-sm">📤</span>
                        Status Pengajuan Saya
                    </h3>

                    @if(!isset($myClaims) || $myClaims->isEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-100 dark:border-gray-700 text-center shadow-sm">
                            <p class="text-gray-500 dark:text-gray-400">Kamu belum pernah mengajukan klaim.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($myClaims as $myClaim)
                                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition relative overflow-hidden">
                                    <div class="absolute left-0 top-0 bottom-0 w-1.5 
                                        {{ $myClaim->status === 'pending' ? 'bg-yellow-400' : ($myClaim->status === 'verified' ? 'bg-green-500' : ($myClaim->status === 'approved' ? 'bg-blue-500' : 'bg-red-500')) }}">
                                    </div>
                                    <div class="pl-4 flex gap-4">
                                        <div class="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 overflow-hidden shrink-0 border dark:border-gray-600">
                                            @if($myClaim->foundItem->primaryImage)
                                                <img src="{{ asset('storage/' . $myClaim->foundItem->primaryImage->image_path) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">No Pic</div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-800 dark:text-white line-clamp-1">{{ $myClaim->foundItem->item_name }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Diajukan: {{ $myClaim->created_at->diffForHumans() }}</p>
                                            <div class="mt-2">
                                                @if($myClaim->status === 'pending')
                                                    <div class="flex flex-col items-start gap-2">
                                                        <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded font-bold">⏳ Menunggu Respon</span>
                                                        @if($myClaim->foundItem->phone_number)
                                                            @php
                                                                $cleanPhone = preg_replace('/[^0-9]/', '', $myClaim->foundItem->phone_number);
                                                                if(substr($cleanPhone, 0, 1) == '0') $cleanPhone = '62' . substr($cleanPhone, 1);
                                                            @endphp
                                                            <a href="https://wa.me/{{ $cleanPhone }}?text=Halo, saya ingin menanyakan klaim barang {{ $myClaim->foundItem->item_name }}" target="_blank" class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-1.5 px-3 rounded-lg transition shadow-sm mt-1">
                                                                <span>📞</span> Hubungi Penemu
                                                            </a>
                                                        @endif
                                                    </div>
                                                @elseif($myClaim->status === 'approved')
                                                    <div class="flex flex-col items-start gap-2">
                                                        <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded font-bold">🔹 Disetujui Penemu</span>
                                                        <span class="text-[10px] text-gray-400 italic">Menunggu validasi akhir Admin</span>
                                                    </div>
                                                @elseif($myClaim->status === 'verified')
                                                    <div class="flex flex-col items-start gap-2">
                                                        <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded font-bold">✅ Klaim Diterima!</span>
                                                        <span class="text-xs text-gray-400">Transaksi Selesai</span>
                                                    </div>
                                                @else
                                                    <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded font-bold">❌ Klaim Ditolak</span>
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