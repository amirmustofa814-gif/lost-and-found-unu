<x-guest-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-sm">
        
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900">Verifikasi OTP</h2>
            <p class="text-gray-600 mt-1">Kode verifikasi telah dikirim ke WhatsApp Anda</p>
        </div>

        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-bold">Kode OTP terkirim! Cek WA sekarang.</span>
        </div>

        <form method="POST" action="{{ route('otp.verify') }}">
            @csrf

            <div class="mb-4">
                <x-input-label value="Nama Lengkap" />
                <input type="text" value="{{ $user->name }}" readonly 
                       class="block mt-1 w-full border-gray-300 rounded-md text-gray-500 bg-gray-100 cursor-not-allowed">
            </div>

            <div class="mb-6 border border-blue-200 bg-blue-50 p-4 rounded-lg">
                
                <div class="mb-4">
                    <x-input-label value="Nomor HP (WhatsApp)" class="text-blue-900" />
                    <div class="grid grid-cols-[1fr_auto] gap-2 mt-1 w-full">
                        <input type="text" value="{{ $user->phone_number }}" readonly 
                               class="block w-full rounded-md border-gray-300 text-gray-900 font-medium bg-white shadow-sm">
                        
                        <button type="button" disabled class="bg-gray-200 text-gray-500 border border-gray-300 px-4 py-2 rounded-md text-sm font-bold shadow-sm whitespace-nowrap cursor-not-allowed">
                            Terkirim ✓
                        </button>
                    </div>
                </div>

                <div class="mt-2 animate-pulse-once">
                    <x-input-label for="otp_code" value="Masukkan Kode OTP" class="text-blue-800 font-bold mb-1" />
                    <input id="otp_code" 
                           class="block w-full text-center text-3xl tracking-[0.5em] font-bold border-blue-400 focus:border-blue-600 focus:ring-blue-600 rounded-md py-2 text-blue-900 placeholder-blue-200 bg-white" 
                           type="text" name="otp_code" required autofocus placeholder="••••••" maxlength="6" />
                    <p class="text-xs text-blue-600 mt-2 text-center">*Masukkan 6 digit angka dari pesan WhatsApp</p>
                    <x-input-error :messages="$errors->get('otp_code')" class="mt-2 text-center" />
                </div>
            </div>

            <div class="mb-4 opacity-70">
                <x-input-label value="Alamat Email" />
                <input type="text" value="{{ $user->email }}" readonly class="block mt-1 w-full border-gray-300 rounded-md bg-gray-100 text-gray-500">
            </div>

            <div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md transition duration-200 text-lg">
                    Verifikasi & Selesai
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>