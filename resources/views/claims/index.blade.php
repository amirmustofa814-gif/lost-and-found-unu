<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
             {{ __('Transaksi Klaim') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Pesan Sukses / Error --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg flex items-center gap-2">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg flex items-center gap-2">
                    <span>❌</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- ========================================================== --}}
            {{-- BAGIAN 1: KLAIM MASUK (Untuk Penemu / Admin)               --}}
            {{-- ========================================================== --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 dark:border-gray-700 mb-12">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                         Klaim Masuk 
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
                                        
                                        {{-- KOLOM BARU: PENEMU (HANYA ADMIN) --}}
                                        @if(Auth::user()->role === 'admin')
                                            <th class="p-4">Penemu</th>
                                        @endif

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

                                        {{-- ISI KOLOM PENEMU (HANYA ADMIN) --}}
                                        @if(Auth::user()->role === 'admin')
                                            <td class="p-4">
                                                <div class="font-bold text-blue-600 dark:text-blue-400 text-xs">
                                                    {{ $claim->foundItem->user->name ?? 'User Terhapus' }}
                                                </div>
                                                <div class="text-[10px] text-gray-400">
                                                    {{ $claim->foundItem->user->email ?? '-' }}
                                                </div>
                                            </td>
                                        @endif

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
                                                     Ada Foto
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
                                                        <form action="{{ route('claims.verify', $claim->id) }}" method="POST"> @csrf @method('PATCH') <input type="hidden" name="action" value="approve"> <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg shadow text-xs font-bold"> Terima</button> </form>
                                                        <form action="{{ route('claims.verify', $claim->id) }}" method="POST" onsubmit="return confirm('Tolak?');"> @csrf @method('PATCH') <input type="hidden" name="action" value="reject"> <button type="submit" class="bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-lg shadow font-bold">✕</button> </form>
                                                        <button @click="showModal = true" class="bg-blue-500 hover:bg-blue-600 text-white w-8 h-8 rounded-lg shadow font-bold">👁️</button>
                                                    </div>

                                                {{-- 2. TOMBOL (ADMIN) --}}
                                                @elseif($claim->status == 'approved')
                                                    @if(Auth::id() === $claim->foundItem->user_id)
                                                        <button @click="showModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg shadow text-xs font-bold animate-pulse"> Upload Bukti</button>
                                                    @elseif(Auth::user()->role === 'admin')
                                                        <button @click="showModal = true" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg shadow text-xs font-bold {{ !$claim->handover_photo_path ? 'opacity-50 cursor-not-allowed' : 'animate-pulse' }}">
                                                            {{ $claim->handover_photo_path ? '✅ Verifikasi' : ' Menunggu Bukti' }}
                                                        </button>
                                                    @endif

                                                {{-- 3. TOMBOL LIHAT BUKTI (ADMIN) --}}
                                                @elseif($claim->status == 'verified')
                                                    <div class="flex items-center justify-center gap-2">
                                                        <span class="text-green-600 text-lg">✅</span>
                                                        @if(Auth::user()->role === 'admin' && $claim->handover_photo_path)
                                                            <button @click="showModal = true" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded text-xs border border-gray-300 flex items-center gap-1">
                                                                🖼️ Bukti
                                                            </button>
                                                        @endif
                                                    </div>

                                                @elseif($claim->status == 'rejected')
                                                    <span class="text-red-600 text-lg">❌</span>
                                                @endif

                                                {{-- ================================================= --}}
                                                {{-- MODAL POPUP                                       --}}
                                                {{-- ================================================= --}}
                                                <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
                                                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden relative" @click.away="showModal = false">
                                                        
                                                        <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b dark:border-gray-600 flex justify-between items-center">
                                                            <h3 class="text-lg font-bold text-gray-800 dark:text-white"> Detail & Validasi Klaim</h3>
                                                            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 font-bold text-xl">&times;</button>
                                                        </div>

                                                        <div class="p-6 overflow-y-auto max-h-[80vh]">
                                                            
                                                            {{-- INFO PENEMU (ADMIN ONLY) --}}
                                                            @if(Auth::user()->role === 'admin')
                                                                <div class="mb-4 bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-200 dark:border-blue-800 flex justify-between items-center">
                                                                    <div>
                                                                        <span class="text-xs font-bold text-gray-500 uppercase">Penemu Barang:</span>
                                                                        <p class="font-bold text-gray-800 dark:text-white">{{ $claim->foundItem->user->name ?? 'User Terhapus' }}</p>
                                                                    </div>
                                                                    <div class="text-right">
                                                                        <span class="text-xs font-bold text-gray-500 uppercase">Kontak Penemu:</span>
                                                                        <p class="text-xs text-gray-600 dark:text-gray-300">{{ $claim->foundItem->user->email ?? '-' }}</p>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                                                {{-- Data Klaim --}}
                                                                <div>
                                                                    <h4 class="text-xs font-bold text-gray-400 uppercase mb-3 border-b pb-1">Data Klaim (Pemohon)</h4>
                                                                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg text-sm italic mb-3">"{{ $claim->description }}"</div>
                                                                    @if($claim->proof_image_path) <img src="{{ asset('storage/' . $claim->proof_image_path) }}" class="w-full h-32 object-cover rounded-lg border"> @else <div class="h-24 bg-gray-100 flex items-center justify-center text-xs text-gray-400 rounded-lg">No Proof Image</div> @endif
                                                                </div>
                                                                
                                                                {{-- Referensi Laporan Kehilangan (DENGAN FOTO) --}}
                                                                <div>
                                                                    <h4 class="text-xs font-bold text-red-400 uppercase mb-3 border-b pb-1">Laporan Kehilangan</h4>
                                                                    @php $matched = $claim->user->lostItems->where('category_id', $claim->foundItem->category_id)->first(); @endphp
                                                                    @if($matched)
                                                                        <div class="bg-red-50 p-3 rounded-lg border border-red-100">
                                                                            <h5 class="font-bold text-sm">{{ $matched->item_name }}</h5>
                                                                            <p class="text-xs text-gray-600">{{ Str::limit($matched->description, 50) }}</p>
                                                                            <p class="text-[10px] mt-1 text-gray-500 mb-2"> {{ $matched->location ?? 'Tidak ada lokasi' }}</p>
                                                                            
                                                                            {{-- FOTO DI BAWAH INFO --}}
                                                                            @if($matched->images->count() > 0)
                                                                                <div x-data="{ activeSlide: 0, slides: [ @foreach($matched->images as $img) '{{ asset('storage/' . $img->image_path) }}', @endforeach ] }" class="relative w-full h-40 rounded-lg overflow-hidden border border-red-200 bg-gray-200 mt-2">
                                                                                    <template x-for="(slide, index) in slides" :key="index">
                                                                                        <img :src="slide" x-show="activeSlide === index" class="w-full h-full object-cover absolute" onclick="window.open(this.src)">
                                                                                    </template>
                                                                                    <button x-show="slides.length > 1" @click="activeSlide = activeSlide === 0 ? slides.length - 1 : activeSlide - 1" class="absolute left-1 top-1/2 -translate-y-1/2 bg-black/50 text-white p-1 rounded-full">❮</button>
                                                                                    <button x-show="slides.length > 1" @click="activeSlide = activeSlide === slides.length - 1 ? 0 : activeSlide + 1" class="absolute right-1 top-1/2 -translate-y-1/2 bg-black/50 text-white p-1 rounded-full">❯</button>
                                                                                </div>
                                                                            @else
                                                                                <div class="h-40 bg-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-500 mt-2">No Image Found</div>
                                                                            @endif
                                                                        </div>
                                                                    @else
                                                                        <div class="text-center text-gray-400 text-xs py-4 border border-dashed rounded">Tidak ada laporan terkait.</div>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            {{-- BAGIAN BAWAH: AKSI --}}
                                                            @if($claim->status == 'approved' && Auth::id() === $claim->foundItem->user_id)
                                                                {{-- Penemu Upload --}}
                                                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 text-center">
                                                                    <h4 class="font-bold text-blue-800 text-sm mb-2"> Penemu: Upload Bukti Serah Terima</h4>
                                                                    <form action="{{ route('claims.verify', $claim->id) }}" method="POST" enctype="multipart/form-data">
                                                                        @csrf @method('PATCH')
                                                                        <input type="hidden" name="action" value="upload">
                                                                        <input type="file" name="handover_photo" required class="block w-full text-sm mb-3 border rounded p-2 bg-white">
                                                                        <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded font-bold"> Upload Bukti</button>
                                                                    </form>
                                                                </div>

                                                            @elseif($claim->status == 'approved' && Auth::user()->role === 'admin')
                                                                {{-- Admin Verifikasi --}}
                                                                <div class="bg-gray-100 p-4 rounded-lg border border-gray-300 text-center">
                                                                    @if($claim->handover_photo_path)
                                                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-bold">✅ Bukti Ada</span>
                                                                        <img src="{{ asset('storage/' . $claim->handover_photo_path) }}" class="mt-2 h-40 object-contain mx-auto border rounded bg-white">
                                                                        <form action="{{ route('claims.verify', $claim->id) }}" method="POST" class="mt-3">
                                                                            @csrf @method('PATCH')
                                                                            <input type="hidden" name="action" value="verify">
                                                                            <button type="submit" class="w-full py-2 bg-green-600 hover:bg-green-700 text-white rounded font-bold">✅ Selesaikan Transaksi</button>
                                                                        </form>
                                                                    @else
                                                                        <p class="text-sm font-bold text-red-600"> Menunggu Penemu Upload Bukti...</p>
                                                                    @endif
                                                                </div>

                                                            @elseif($claim->status == 'verified' && $claim->handover_photo_path)
                                                                {{-- TAMPILAN BUKTI JIKA SELESAI --}}
                                                                <div class="mt-4 p-4 bg-green-50 text-green-700 rounded-lg text-center border border-green-200">
                                                                    <h4 class="font-bold text-sm mb-2">✅ Bukti Serah Terima</h4>
                                                                    <img src="{{ asset('storage/' . $claim->handover_photo_path) }}" class="h-64 w-full object-contain bg-white rounded border cursor-pointer" onclick="window.open(this.src)">
                                                                    <p class="text-xs mt-2 text-gray-500">Diverifikasi oleh Admin pada {{ $claim->verified_at }}</p>
                                                                </div>
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
            
            {{-- STATUS PENGAJUAN SAYA (User) --}}
             @if(Auth::user()->role !== 'admin')
                <div class="mt-8">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <span class="flex items-center justify-center w-8 h-8 bg-green-100 text-green-600 rounded-lg text-sm"></span>
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
                                    <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $myClaim->status === 'pending' ? 'bg-yellow-400' : ($myClaim->status === 'verified' ? 'bg-green-500' : ($myClaim->status === 'approved' ? 'bg-blue-500' : 'bg-red-500')) }}"></div>
                                    <div class="pl-4 flex gap-4">
                                        <div class="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 overflow-hidden shrink-0 border dark:border-gray-600">
                                            @if($myClaim->foundItem->primaryImage) <img src="{{ asset('storage/' . $myClaim->foundItem->primaryImage->image_path) }}" class="w-full h-full object-cover"> @else <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">No Pic</div> @endif
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-800 dark:text-white line-clamp-1">{{ $myClaim->foundItem->item_name }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Diajukan: {{ $myClaim->created_at->diffForHumans() }}</p>
                                            <div class="mt-2">
                                                @if($myClaim->status === 'pending')
                                                    <div class="flex flex-col items-start gap-2">
                                                        <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded font-bold"> Menunggu Respon</span>
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

                                                {{-- LOGIKA BARU: TAMPILKAN TOMBOL WA JUGA SAAT APPROVED --}}
                                                @elseif($myClaim->status === 'approved')
                                                    <div class="flex flex-col items-start gap-2">
                                                        <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded font-bold">🔹 Disetujui Penemu</span>
                                                        <span class="text-[10px] text-gray-400 italic">Silakan temui penemu & lakukan serah terima.</span>
                                                        
                                                        {{-- TOMBOL WA DI SINI JUGA --}}
                                                        @if($myClaim->foundItem->phone_number)
                                                            @php
                                                                $cleanPhone = preg_replace('/[^0-9]/', '', $myClaim->foundItem->phone_number);
                                                                if(substr($cleanPhone, 0, 1) == '0') $cleanPhone = '62' . substr($cleanPhone, 1);
                                                            @endphp
                                                            <a href="https://wa.me/{{ $cleanPhone }}?text=Halo, saya ingin bertemu untuk mengambil barang {{ $myClaim->foundItem->item_name }}" target="_blank" class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-1.5 px-3 rounded-lg transition shadow-sm mt-1">
                                                                <span>📞</span> Hubungi Penemu
                                                            </a>
                                                        @endif
                                                    </div>

                                                @elseif($myClaim->status === 'verified')
                                                    <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded font-bold">✅ Selesai</span>
                                                @else
                                                    <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded font-bold">❌ Ditolak</span>
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