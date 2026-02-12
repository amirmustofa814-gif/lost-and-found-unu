<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator; 
use App\Models\User;


class OtpController extends Controller
{
    // ------------------------------------------------------------------
    // 1. FITUR BARU: KIRIM OTP VIA TOMBOL REGISTER (AJAX)
    // ------------------------------------------------------------------
    public function sendOtpAjax(Request $request)
    {
        // VALIDASI KETAT (Nomor 08.., Angka, 10-13 digit)
        $validator = Validator::make($request->all(), [
            'phone_number' => [
                'required', 
                'numeric',
                'digits_between:10,13',
                'regex:/^08[0-9]+$/'
            ],
        ], [
            'phone_number.required' => 'Nomor HP wajib diisi!',
            'phone_number.numeric' => 'Nomor HP harus berupa angka!',
            'phone_number.digits_between' => 'Nomor HP minimal 10 digit, maksimal 13 digit!',
            'phone_number.regex' => 'Nomor HP harus diawali 08!',
        ]);

        // Kalau salah, kirim pesan error ke JavaScript
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error', 
                'message' => $validator->errors()->first('phone_number') 
            ]);
        }

        // A. Generate OTP
        $otp = rand(100000, 999999);
        
        // B. Set Waktu Kadaluarsa 5 Menit
        $expireTime = now()->addMinutes(5);
        
        // C. Simpan Session
        session([
            'register_otp' => $otp,
            'otp_expires_at' => $expireTime
        ]);
        
        // D. Format Nomor ke 62
        $nomor = preg_replace('/[^0-9]/', '', $request->phone_number);
        if (substr($nomor, 0, 2) === '08') {
            $nomor = '62' . substr($nomor, 1);
        }

        // E. Kirim WA Gateway
        try {
            Http::post('http://localhost:3000/send/message', [
                'phone' => $nomor,
                'message' => "Kode OTP Anda: *{$otp}* (Berlaku 5 Menit)",
            ]);
            
            return response()->json([
                'status' => 'success', 
                'message' => 'OTP Terkirim!',
                'expires_in' => 300 
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal terhubung ke WA Gateway.']);
        }
    }

    // ------------------------------------------------------------------
    // 2. FITUR LAMA: HALAMAN VERIFIKASI OTP TERPISAH (Backup)
    // ------------------------------------------------------------------
    
    public function show(Request $request)
    {
        $encryptedId = $request->query('id');

        if (!$encryptedId) {
            return redirect()->route('login')->with('error', 'ID User tidak ditemukan.');
        }

        return view('auth.verify-otp', ['encryptedId' => $encryptedId]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
            'user_id' => 'required'
        ]);

        try {
            $userId = Crypt::decryptString($request->user_id);
            $user = User::find($userId);

            if ($user && $request->otp == $user->otp_code) {
                
                Auth::login($user);
                $user->otp_code = null;
                $user->save();

                return redirect()->route('dashboard');
            }

        } catch (\Exception $e) {
            return back()->withErrors(['otp' => 'Terjadi kesalahan data user.']);
        }

        return back()->withErrors(['otp' => 'Kode OTP salah!']);
    }
}