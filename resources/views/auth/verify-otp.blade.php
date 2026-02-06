<x-guest-layout>
    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf
        <div class="mb-4 text-sm text-gray-600">
            Kode OTP telah dikirim ke WhatsApp Anda. Silakan masukkan di bawah ini.
        </div>

        <div>
            <x-input-label for="otp" :value="__('Kode OTP')" />
            <x-text-input id="otp" class="block mt-1 w-full" type="text" name="otp" required autofocus />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Verifikasi') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>