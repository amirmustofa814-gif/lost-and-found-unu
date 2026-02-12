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
        // 1. VALIDASI INPUT (Tambah validasi untuk otp_code)
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:20', 'unique:'.User::class],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'phone_number' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'otp_code' => ['required', 'numeric'],
        ]);

        // Cek Waktu Kadaluarsa DULU
        if (session('otp_expires_at') && now()->greaterThan(session('otp_expires_at'))) {
            return back()
                ->withErrors(['otp_code' => 'Kode OTP sudah kadaluarsa! Silakan kirim ulang.'])
                ->withInput();
        }

        // 2. CEK APAKAH OTP COCOK DENGAN SESI? (Validasi Keamanan)
        if ($request->otp_code != session('register_otp')) {
            return back()
            ->withErrors(['otp_code' => 'Kode OTP Salah! Silakan cek WA lagi.'])
            ->withInput();
        }

        // 3. SIMPAN DATA USER (Langsung Verified)
        $user = User::create([
            'name' => $request->name,
            'nim' => $request->nim,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'otp_code' => null, 
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa',
            'email_verified_at' => now(),
        ]);

        // (Bagian Kirim WA dihapus karena sudah dikirim via AJAX sebelumnya)

        event(new Registered($user));
        
        // Hapus data sesi OTP (biar bersih)
        session()->forget(['register_otp', 'register_phone', 'otp_expires_at']);

        // Redirect ke Halaman Login dengan Pesan Sukses
        return redirect()->route('login')
            ->with('status', 'Registrasi Berhasil! Silakan Masuk.');
    }
    // Fungsi Kecil untuk mengubah 0812... jadi 62812... //
    private function formatNomorHp($nomor)
    {
        $nomor = preg_replace('/[^0-9]/', '', $nomor);
        if (substr($nomor, 0, 2) === '08') {
            return '62' . substr($nomor, 1);
        }
        return $nomor;
    }
}