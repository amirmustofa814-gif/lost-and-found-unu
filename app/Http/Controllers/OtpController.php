<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    public function show()
    {
        return view('auth.verify-otp');
    }

    public function verify(Request $request)
    {
        $request->validate(['otp' => 'required|numeric']);

        if ($request->otp == Auth::user()->otp_code) {
            // Jika OTP Benar, user boleh masuk Dashboard
            // (Nanti kita buat logika update status user jadi 'verified')
            return redirect()->route('dashboard')->with('success', 'Akun berhasil diverifikasi!');
        }

        return back()->withErrors(['otp' => 'Kode OTP salah!']);
    }
}