<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http; 

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

 public function store(Request $request)
    {
        // 1. VALIDASI INPUT
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:20', 'unique:'.User::class],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'phone_number' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. BUAT KODE OTP
        $otp = rand(100000, 999999);

        // 3. SIMPAN DATA USER
        $user = User::create([
            'name' => $request->name,
            'nim' => $request->nim,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'otp_code' => $otp,
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa',
        ]);

        // 4. FORMAT NOMOR HP
        $nomor_hp = $this->formatNomorHp($request->phone_number);

        // 5. KIRIM PESAN WA (FINAL & RAPI)
        try {
            Http::post('http://localhost:3000/send/message', [
                'phone' => $nomor_hp,
                'message' => "Halo {$user->name}, Kode Verifikasi Anda: *{$otp}*",
            ]);
        } catch (\Exception $e) {
            // Kalau gagal, catat di log saja
            \Log::error('Gagal WA: ' . $e->getMessage());
        }

        event(new Registered($user));

        Auth::login($user);

        // Redirect ke halaman Input OTP
        return view('auth.verify-otp-inline', ['user' => $user]);
    }
    // Fungsi Kecil untuk mengubah 0812... jadi 62812...
    private function formatNomorHp($nomor)
    {
        $nomor = preg_replace('/[^0-9]/', '', $nomor); // Hapus spasi/strip
        if (substr($nomor, 0, 2) === '08') {
            return '62' . substr($nomor, 1);
        }
        return $nomor;
    }
}