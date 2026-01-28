<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Detail Pengguna: {{ $user->name }}
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm font-bold">
                &larr; Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Pesan Error (Jika coba hapus diri sendiri) --}}
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Gagal!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- 1. KARTU INFORMASI UTAMA & AKSI --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border border-gray-100 dark:border-gray-700">
                
                <div class="flex items-center gap-6">
                    {{-- Avatar --}}
                    <div class="w-20 h-20 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center text-2xl font-bold text-indigo-600 dark:text-indigo-300 uppercase shrink-0">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                    
                    {{-- Info Teks --}}
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $user->name }}</h3>
                        <div class="flex flex-col gap-1 mt-1">
                            <p class="text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                {{ $user->email }}
                            </p>
                            <p class="text-gray-600 dark:text-gray-300 flex items-center gap-2 font-mono text-sm">
                                 NIM: <span class="font-bold bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">{{ $user->nim ?? '-' }}</span>
                            </p>
                        </div>
                        <div class="mt-3 flex gap-3">
                            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ strtoupper($user->role) }}
                            </span>
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                Bergabung: {{ $user->created_at->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- TOMBOL HAPUS (DANGER ZONE) --}}
                <div class="mt-4 md:mt-0">
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('⚠️ PERINGATAN KERAS:\n\nMenghapus pengguna ini akan menghapus SEMUA data terkait (Laporan Hilang, Temuan, Klaim, dan Foto).\n\nTindakan ini TIDAK BISA DIBATALKAN.\n\nApakah Anda yakin ingin melanjutkan?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="group flex items-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white transition-all font-bold text-sm shadow-sm">
                            <svg class="w-5 h-5 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus Pengguna
                        </button>
                    </form>
                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- 2. RIWAYAT BARANG HILANG --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                    <h4 class="font-bold text-lg mb-4 text-red-600 flex items-center gap-2">
                        Laporan Kehilangan ({{ $user->lostItems->count() }})
                    </h4>
                    @if($user->lostItems->isEmpty())
                        <p class="text-gray-400 text-sm italic">Belum ada laporan.</p>
                    @else
                        <ul class="space-y-3">
                            @foreach($user->lostItems as $item)
                                <li class="flex justify-between items-center border-b dark:border-gray-700 pb-2">
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $item->item_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $item->created_at->format('d M Y') }}</p>
                                    </div>
                                    <span class="text-xs px-2 py-1 rounded font-bold {{ $item->status == 'ditemukan' ? 'bg-green-100 text-green-700' : 'bg-red-50 text-red-600' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- 3. RIWAYAT BARANG TEMUAN --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                    <h4 class="font-bold text-lg mb-4 text-green-600 flex items-center gap-2">
                        Laporan Penemuan ({{ $user->foundItems->count() }})
                    </h4>
                    @if($user->foundItems->isEmpty())
                        <p class="text-gray-400 text-sm italic">Belum ada laporan.</p>
                    @else
                        <ul class="space-y-3">
                            @foreach($user->foundItems as $item)
                                <li class="flex justify-between items-center border-b dark:border-gray-700 pb-2">
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $item->item_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $item->created_at->format('d M Y') }}</p>
                                    </div>
                                    <span class="text-xs px-2 py-1 rounded font-bold {{ $item->status == 'diambil' ? 'bg-gray-100 text-gray-500' : 'bg-green-50 text-green-600' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- 4. RIWAYAT KLAIM --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                <h4 class="font-bold text-lg mb-4 text-yellow-600 flex items-center gap-2">
                     Riwayat Klaim ({{ $user->claims->count() }})
                </h4>
                @if($user->claims->isEmpty())
                    <p class="text-gray-400 text-sm italic">Belum pernah mengajukan klaim.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-2">Barang</th>
                                    <th class="px-4 py-2">Tanggal</th>
                                    <th class="px-4 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->claims as $claim)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                            {{ $claim->foundItem->item_name ?? 'Item Dihapus' }}
                                        </td>
                                        <td class="px-4 py-3">{{ $claim->created_at->format('d M Y') }}</td>
                                        <td class="px-4 py-3">
                                            @if($claim->status == 'verified')
                                                <span class="text-green-600 font-bold">Diterima</span>
                                            @elseif($claim->status == 'rejected')
                                                <span class="text-red-600 font-bold">Ditolak</span>
                                            @else
                                                <span class="text-yellow-600 font-bold">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>