<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TestController extends Controller
{
    /**
     * Endpoint untuk SEMUA USER (Mahasiswa & Admin)
     */
    #[OA\Get(
        path: '/api/test/user-area', 
        tags: ['RBAC Test'],
        summary: 'Cek Profil (Semua Role)',
        description: 'Bisa diakses oleh admin maupun mahasiswa.',
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: 'Berhasil masuk',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', example: 'Halo, ini profil anda'),
                new OA\Property(property: 'role', example: 'mahasiswa')
            ]
        )
    )]
    public function userOnly(Request $request) 
    {
        // Pakai operator '??' biar tidak error jika kolom role belum ada
        $role = $request->user()->role ?? 'mahasiswa'; 

        return response()->json([
            'message' => 'Halo, ' . $request->user()->name,
            'role' => $role
        ]);
    }

    /**
     * Endpoint KHUSUS ADMIN
     */
    #[OA\Get(
        path: '/api/test/admin-area',
        tags: ['RBAC Test'],
        summary: 'Area Terlarang (Hanya Admin)',
        description: 'Jika anda login sebagai mahasiswa, anda akan mendapat error 403.',
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(response: 200, description: 'Sukses: Anda adalah Admin')]
    #[OA\Response(response: 403, description: 'Forbidden: Akses Ditolak (Bukan Admin)')]
    public function adminOnly(Request $request) 
    {
        // Debugging: Cek role user saat ini (default ke 'user' jika null)
        $userRole = $request->user()->role ?? 'user';

        if ($userRole !== 'admin') {
            return response()->json([
                'message' => 'AKSES DITOLAK! Role anda terdeteksi sebagai: ' . $userRole,
                'hint' => 'Pastikan kolom role di database users isinya admin'
            ], 403);
        }

        return response()->json(['message' => 'Selamat datang di Dashboard Admin']);
    }
}