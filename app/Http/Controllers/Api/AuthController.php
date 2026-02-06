<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register User Baru
     */
    public function register(Request $request)
    {
        // 1. Validasi Input //
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'nim'       => 'required|unique:users',
            'email'     => 'required|email|unique:users',
            'phone_number' => 'required',
            'password'  => 'required|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Buat Kode OTP //
        $otp = rand(100000, 999999);

        // 3. Buat User Baru //
        $user = User::create([
            'name'      => $request->name,
            'nim'       => $request->nim,
            'email'     => $request->email,
            'phone_number' => $request->phone_number,
            'password'  => Hash::make($request->password),
            'role'      => 'mahasiswa',
            'otp_code'  => $otp, 
        ]);
       
        try {
            Http::post('http://localhost:3000/send/message', [
                'phone' => $user->phone_number,
                'message' => "Halo {$user->name}, Kode Verifikasi OTP Anda: *{$otp}*",
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal kirim WA di API: ' . $e->getMessage());
        }

        // 4. Buat Token API //
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Return Response JSON //
        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil. Kode OTP dikirim ke WhatsApp.',
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }

    // >>(Fungsi login, logout, userProfile tetap sama, tidak perlu diubah)<< //
    
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Login gagal. Email atau password salah.'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil. Token telah dihapus.'
        ]);
    }

    public function userProfile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    }
}