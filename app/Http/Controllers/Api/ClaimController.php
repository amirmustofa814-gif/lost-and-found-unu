<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\FoundItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClaimController extends Controller
{
    /**
     * GET /api/claims
     * Menampilkan dashboard klaim (Klaim Masuk & Klaim Saya)
     */
    public function index()
    {
        // A. Klaim Masuk (Incoming): Orang lain klaim barang temuan SAYA
        $incomingClaims = Claim::whereHas('foundItem', function($q) {
            $q->where('user_id', Auth::id());
        })->with(['foundItem', 'user'])->latest()->get();

        // B. Klaim Keluar (Outgoing): SAYA klaim barang orang lain
        $myClaims = Claim::where('user_id', Auth::id())
                    ->with(['foundItem.primaryImage'])
                    ->latest()
                    ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'incoming_claims' => $incomingClaims, // Butuh diverifikasi
                'my_claims' => $myClaims              // Status pengajuan saya
            ]
        ]);
    }

    /**
     * POST /api/claims/{found_item_id}
     * Ajukan Klaim Baru
     */
    public function store(Request $request, $found_item_id)
    {
        $validator = Validator::make($request->all(), [
            'proof_description' => 'required|string|max:500', // Bukti rahasia
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $foundItem = FoundItem::find($found_item_id);
        if (!$foundItem) return response()->json(['message' => 'Barang tidak ditemukan'], 404);

        // Cek duplikasi
        $exists = Claim::where('found_item_id', $found_item_id)
                       ->where('user_id', Auth::id())->first();
        if ($exists) {
            return response()->json(['message' => 'Anda sudah mengajukan klaim ini'], 400);
        }

        try {
            DB::beginTransaction();
            
            // Simpan Klaim
            $claim = Claim::create([
                'found_item_id' => $found_item_id,
                'user_id' => Auth::id(),
                'proof_description' => $request->proof_description,
                'status' => 'pending',
                'created_by' => Auth::id()
            ]);

            // Update status barang jadi 'diklaim'
            $foundItem->update(['status' => 'diklaim']);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Klaim diajukan', 'data' => $claim], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/claims/{claim_id}/verify
     * Terima Klaim (Barang jadi milik pelapor)
     */
    public function verify($claim_id)
    {
        $claim = Claim::with('foundItem')->find($claim_id);
        if (!$claim) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        // Pastikan yang verifikasi adalah PENEMU ASLI
        if ($claim->foundItem->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            // 1. Terima Klaim ini
            $claim->update([
                'status' => 'verified',
                'verified_at' => now(),
                'verified_by' => Auth::user()->name
            ]);

            // 2. Update Barang jadi 'diambil' (SELESAI)
            $claim->foundItem->update(['status' => 'diambil']);

            // 3. Tolak klaim lain di barang yang sama otomatis
            Claim::where('found_item_id', $claim->found_item_id)
                 ->where('id', '!=', $claim->id)
                 ->update(['status' => 'rejected']);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Klaim diterima. Transaksi selesai.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/claims/{claim_id}/reject
     * Tolak Klaim
     */
    public function reject($claim_id)
    {
        $claim = Claim::with('foundItem')->find($claim_id);
        if (!$claim) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        if ($claim->foundItem->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            // 1. Tolak Klaim
            $claim->update(['status' => 'rejected']);

            // 2. Cek apakah masih ada klaim pending lain?
            $pendingCount = Claim::where('found_item_id', $claim->found_item_id)
                                 ->where('status', 'pending')->count();
            
            // Jika tidak ada klaim lain, kembalikan status barang jadi 'tersedia'
            if ($pendingCount == 0) {
                $claim->foundItem->update(['status' => 'tersedia']);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Klaim ditolak.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}