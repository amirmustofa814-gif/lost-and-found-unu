<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\FoundItem;
use App\Models\LostItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
// use App\Notifications\NewClaimNotification; // Aktifkan jika sudah ada notifikasi
// use App\Notifications\ClaimStatusUpdated; 

class ClaimController extends Controller
{
    /**
     * Tampilkan Dashboard Klaim (Pusat Validasi)
     */
    public function index()
    {
        $user = Auth::user();

        // 1. KLAIM MASUK (incomingClaims)
        if ($user->role === 'admin') {
            // Admin melihat SEMUA klaim
            $incomingClaims = Claim::with(['foundItem.primaryImage', 'foundItem.user', 'user'])
                                   ->latest()
                                   ->get();
        } else {
            // User Biasa: Melihat klaim orang lain terhadap barang yang DIA temukan
            $incomingClaims = Claim::whereHas('foundItem', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with(['foundItem.primaryImage', 'user'])->latest()->get();
        }

        // 2. KLAIM SAYA (myClaims)
        $myClaims = Claim::where('user_id', $user->id)
                        ->with(['foundItem.primaryImage', 'foundItem.user'])
                        ->latest()
                        ->get();

        return view('claims.index', compact('incomingClaims', 'myClaims'));
    }

    /**
     * Proses Pengajuan Klaim
     */
    public function store(Request $request)
    {
        $request->validate([
            'found_item_id' => 'required|exists:found_items,id',
            'description'   => 'required|string',
            'proof_image'   => 'nullable|image|max:2048',
        ]);

        // Cek Spam
        $exists = Claim::where('user_id', Auth::id())
                       ->where('found_item_id', $request->found_item_id)
                       ->exists();
        if ($exists) {
            return back()->with('error', 'Anda sudah mengajukan klaim untuk barang ini.');
        }

        $foundItem = FoundItem::findOrFail($request->found_item_id);
        if ($foundItem->user_id == Auth::id()) {
            return back()->with('error', 'Anda tidak bisa mengklaim barang temuan sendiri.');
        }

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('claim_proofs', 'public');
        }

        Claim::create([
            'found_item_id'    => $request->found_item_id,
            'user_id'          => Auth::id(),
            'description'      => $request->description,
            'proof_image_path' => $proofPath,
            'status'           => 'pending',
        ]);
        
        return back()->with('success', 'Klaim berhasil diajukan! Tunggu respon dari penemu.');
    }

    /**
     * PROSES VERIFIKASI (Approve / Reject / Verify)
     */
    public function verify(Request $request, $id)
    {
        $claim = Claim::with('foundItem')->findOrFail($id);
        $foundItem = $claim->foundItem;
        $action = $request->input('action'); 

        // --- 1. PENEMU MENYETUJUI (APPROVE) ---
        if ($action === 'approve') {
            if (Auth::id() !== $foundItem->user_id) {
                abort(403, 'Hanya penemu barang yang berhak menyetujui tahap awal.');
            }
            $claim->update(['status' => 'approved']);
            return back()->with('success', 'Klaim disetujui! Menunggu Admin untuk memproses serah terima.');
        }

        // --- 2. ADMIN MENYELESAIKAN (VERIFY) ---
        if ($action === 'verify') {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Hanya Admin yang dapat memfinalisasi transaksi.');
            }

            $request->validate([
                'handover_photo' => 'required|image|max:4096'
            ]);

            $handoverPath = $request->file('handover_photo')->store('handover_proofs', 'public');

            DB::transaction(function() use ($claim, $foundItem, $handoverPath) {
                $claim->update([
                    'status' => 'verified',
                    'verified_at' => now(),
                    'verified_by' => Auth::id(),
                    'handover_photo_path' => $handoverPath
                ]);

                $foundItem->update(['status' => 'diambil']);

                Claim::where('found_item_id', $foundItem->id)
                     ->where('id', '!=', $claim->id)
                     ->update(['status' => 'rejected']);
            });

            return back()->with('success', 'Transaksi Selesai! Bukti tersimpan & status barang diperbarui.');
        }

        // --- 3. TOLAK KLAIM (REJECT) ---
        if ($action === 'reject') {
            if (Auth::user()->role !== 'admin' && Auth::id() !== $foundItem->user_id) {
                abort(403);
            }
            $claim->update(['status' => 'rejected']);
            return back()->with('success', 'Klaim telah DITOLAK.');
        }

        return back()->with('error', 'Aksi tidak dikenali.');
    }
}