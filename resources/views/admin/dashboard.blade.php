<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-red-600 leading-tight">
            {{ __('Admin Dashboard') }} 
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Statistik Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                {{-- Card 1 --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-blue-500 transition-colors duration-300">
                    <div class="text-gray-500 dark:text-gray-400 text-sm uppercase font-bold">Total User</div>
                    <div class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalUsers }}</div>
                </div>
                {{-- Card 2 --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-red-500 transition-colors duration-300">
                    <div class="text-gray-500 dark:text-gray-400 text-sm uppercase font-bold">Barang Hilang</div>
                    <div class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalLost }}</div>
                </div>
                {{-- Card 3 --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-green-500 transition-colors duration-300">
                    <div class="text-gray-500 dark:text-gray-400 text-sm uppercase font-bold">Barang Temuan</div>
                    <div class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalFound }}</div>
                </div>
                {{-- Card 4 --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-yellow-500 transition-colors duration-300">
                    <div class="text-gray-500 dark:text-gray-400 text-sm uppercase font-bold">Total Klaim</div>
                    <div class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalClaims }}</div>
                </div>
            </div>

            {{-- Tabel User Terbaru --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-colors duration-300">
                <div class="p-6">
                    <h3 class="font-bold text-lg mb-4 text-gray-900 dark:text-white">Pengguna Terbaru</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-gray-900 dark:text-gray-100">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700 border-b dark:border-gray-600">
                                    <th class="p-3">Nama</th>
                                    <th class="p-3">Email</th>
                                    <th class="p-3">Role</th>
                                    <th class="p-3">Bergabung</th>
                                    <th class="p-3">Aksi</th> 
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestUsers as $user)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="p-3 font-bold">{{ $user->name }}</td>
                                    <td class="p-3">{{ $user->email }}</td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 rounded text-xs font-bold {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ strtoupper($user->role) }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-sm text-gray-500 dark:text-gray-400">{{ $user->created_at->format('d M Y') }}</td>
                                    
                                    {{-- TOMBOL LIHAT DETAIL --}}
                                    <td class="p-3">
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="inline-flex items-center px-3 py-1 bg-indigo-50 dark:bg-indigo-900 border border-indigo-200 dark:border-indigo-700 rounded-full text-xs font-bold text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-800 transition">
                                             Detail
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>