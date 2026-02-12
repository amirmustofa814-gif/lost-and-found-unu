<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class OtpController extends Controller
{
    /**
     * Endpoint Verifikasi OTP
     * Pastikan hanya ada SATU blok #[OA\Post] di bawah ini
     */
    #[OA\Post(
        path: '/api/verify-otp',
        tags: ['Auth'],
        summary: 'Verifikasi Kode OTP',
        description: 'Masukkan kode OTP dari WhatsApp untuk verifikasi akun.',
        security: [['bearerAuth' => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['otp_code'],
            properties: [
                new OA\Property(property: 'otp_code', type: 'string', example: '123456', description: 'Kode 6 digit dari WA')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Verifikasi Berhasil',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', example: 'Nomor WhatsApp berhasil diverifikasi!'),
                new OA\Property(property: 'status', example: true)
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Kode OTP Salah / Kadaluarsa')]
    public function verify(Request $request)
    {
        // --- LOGIKA VERIFIKASI ---
        // Contoh sederhana:
        if ($request->otp_code == '123456') {
            return response()->json(['message' => 'Sukses Verifikasi']);
        }
        
        return response()->json(['message' => 'Kode Salah'], 400);
    }
}