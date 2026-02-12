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
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" 
                              :value="old('name')" required autofocus placeholder="Nama Lengkap" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            
            <div class="mb-4">
                <x-input-label for="nim" value="NIM Mahasiswa" />
                <x-text-input id="nim" class="block mt-1 w-full" type="text" name="nim" 
                              :value="old('nim')" required placeholder="Contoh: 12345678" />
                <x-input-error :messages="$errors->get('nim')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="phone_number" value="Nomor HP (WhatsApp)" />
                
                <div class="grid grid-cols-[1fr_auto] gap-2 mt-1 w-full">
                    <x-text-input id="phone_number" 
                                class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" 
                                type="text" name="phone_number" 
                                :value="old('phone_number')" required placeholder="08xxxxxxxxxx" />
                    
                    <button type="button" onclick="kirimOtp()" id="btn-kirim-otp"
                            class="bg-blue-600 hover:bg-blue-700 text-white border border-blue-600 px-6 py-2 rounded-md text-sm font-bold shadow-sm whitespace-nowrap transition duration-200">
                        Kirim OTP
                    </button>
                </div>

                <p class="text-xs text-gray-500 mt-1">*Pastikan nomor aktif untuk menerima kode verifikasi.</p>
                <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
            </div>

            <div class="mb-4 {{ (old('otp_code') || $errors->has('otp_code')) ? '' : 'hidden' }}" id="div-otp">
                
                <div class="flex justify-between items-center">
                    <x-input-label for="otp_code" value="Masukkan Kode OTP dari WA" class="text-blue-600 font-bold" />
                    <span id="countdown-timer" class="text-red-600 font-bold text-sm hidden">05:00</span>
                </div>
                
                <x-text-input id="otp_code" 
                              class="block mt-1 w-full border-blue-500 ring-2 ring-blue-200" 
                              type="number" 
                              name="otp_code" 
                              placeholder="6 Digit Angka" />

                <p class="text-xs text-green-600 mt-1" id="otp-msg">
                    {{ (old('otp_code') || $errors->has('otp_code')) ? 'Silakan perbaiki Kode OTP Anda.' : 'Kode OTP telah dikirim ke WhatsApp Anda.' }}
                </p>

                <x-input-error :messages="$errors->get('otp_code')" class="mt-2" />
            
            </div>

            <div class="mb-4">
                <x-input-label for="email" value="Alamat Email" />
                <x-text-input id="email" class="block mt-1 w-full bg-blue-50" type="email" name="email" 
                              :value="old('email')" required placeholder="email@contoh.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" class="block mt-1 w-full" 
                                    ::type="showPassword ? 'text' : 'password'" 
                                    name="password" 
                                    :value="old('password')"
                                    required autocomplete="new-password" placeholder="........" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" 
                                    ::type="showPassword ? 'text' : 'password'" 
                                    name="password_confirmation" 
                                    :value="old('password_confirmation')"
                                    required autocomplete="new-password" placeholder="........" />
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

   <script>
        let countdownInterval; 
        function kirimOtp() {
            var hp = document.getElementById('phone_number').value;
            var btn = document.getElementById('btn-kirim-otp');
            var divOtp = document.getElementById('div-otp');
            var timerDisplay = document.getElementById('countdown-timer');

            if(hp == '') {
                alert('Silakan isi nomor HP terlebih dahulu!');
                return;
            }

            btn.innerText = 'Mengirim...';
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');

            fetch("{{ route('otp.send.ajax') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ phone_number: hp })
            })
            .then(response => response.json())
            .then(data => {
                if(data.status == 'success') {
                    alert('SUKSES! Kode OTP dikirim (Berlaku 5 Menit).');
                    divOtp.classList.remove('hidden');
                    
                    // --- MULAI TIMER ---
                    startTimer(data.expires_in, timerDisplay, btn);
                    
                } else {
                    alert('GAGAL: ' + data.message);
                    resetButton(btn);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resetButton(btn);
            });
        }

        // FUNGSI HITUNG MUNDUR
        function startTimer(duration, display, btn) {
            clearInterval(countdownInterval);
            display.classList.remove('hidden'); 
            
            var timer = duration, minutes, seconds;
            
            // Ubah tombol jadi disable selama timer jalan
            btn.innerText = 'Tunggu...';
            btn.disabled = true;

            countdownInterval = setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    // WAKTU HABIS
                    clearInterval(countdownInterval);
                    display.textContent = "Kadaluarsa";
                    resetButton(btn); 
                    alert('Waktu OTP habis. Silakan kirim ulang.');
                }
            }, 1000);
        }

        function resetButton(btn) {
            btn.innerText = 'Kirim Ulang';
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
        @if($errors->has('otp_code') && old('otp_code'))
        document.addEventListener("DOMContentLoaded", function() {
            alert("Kode OTP Anda Salah! Silakan cek WhatsApp lagi.");
            
            // Pastikan kolom OTP terbuka //
            var divOtp = document.getElementById('div-otp');
            var btn = document.getElementById('btn-kirim-otp');
            
            divOtp.classList.remove('hidden');
            btn.innerText = 'Kirim Ulang';
        });
        @endif
    </script>
</x-guest-layout>