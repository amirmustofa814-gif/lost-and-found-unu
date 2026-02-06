<x-guest-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Daftar Akun Baru</h2>
            <p class="text-gray-600 mt-2">Lengkapi data diri Anda di bawah ini</p>
        </div>

        <form method="POST" action="{{ route('register') }}" x-data="{ showPassword: false }">
            @csrf

           <div class="mb-4">
                <x-input-label for="name" value="Nama Lengkap" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus placeholder="Nama Lengkap" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            
            <div class="mb-4">
                <x-input-label for="nim" value="NIM Mahasiswa" />
                <x-text-input id="nim" class="block mt-1 w-full" type="text" name="nim" :value="old('nim')" required placeholder="Contoh: 12345678" />
                <x-input-error :messages="$errors->get('nim')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="phone_number" value="Nomor HP (WhatsApp)" />
                
                <div class="grid grid-cols-[1fr_auto] gap-2 mt-1 w-full">
                    <x-text-input id="phone_number" 
                                 class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" 
                                 type="text" name="phone_number" :value="old('phone_number')" required placeholder="08xxxxxxxxxx" />
                    
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white border border-blue-600 px-6 py-2 rounded-md text-sm font-bold shadow-sm whitespace-nowrap transition duration-200">
                        Kirim OTP
                    </button>
                </div>

                <p class="text-xs text-gray-500 mt-1">*Pastikan nomor aktif untuk menerima kode verifikasi.</p>
                <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
            </div>
            </div>

            <div class="mb-4">
                <x-input-label for="email" value="Alamat Email" />
                <x-text-input id="email" class="block mt-1 w-full bg-blue-50" type="email" name="email" :value="old('email')" required placeholder="email@contoh.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" class="block mt-1 w-full" 
                                    ::type="showPassword ? 'text' : 'password'" 
                                    name="password" required autocomplete="new-password" placeholder="........" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" 
                                    ::type="showPassword ? 'text' : 'password'" 
                                    name="password_confirmation" required autocomplete="new-password" placeholder="........" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class="flex items-center mb-6">
                <input id="show_password" type="checkbox" x-model="showPassword" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                <label for="show_password" class="ml-2 text-sm text-gray-600 cursor-pointer">Tampilkan Password</label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transition duration-200">
                    Daftar Sekarang
                </button>
            </div>

            <div class="text-center mt-4 text-sm text-gray-600">
                Sudah punya akun? 
                <a class="underline text-blue-600 hover:text-blue-900" href="{{ route('login') }}">
                    Masuk di sini
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>