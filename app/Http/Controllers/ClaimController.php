<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\FoundItem;
use App\Models\LostItem; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClaimController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $incomingClaims = Claim::with(['foundItem.primaryImage', 'foundItem.user', 'user'])->latest()->get();
        } else {
            $incomingClaims = Claim::whereHas('foundItem', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with(['foundItem.primaryImage', 'user'])->latest()->get();
        }

        $myClaims = Claim::where('user_id', $user->id)
                        ->with(['foundItem.primaryImage', 'foundItem.user'])
                        ->latest()->get();

        return view('claims.index', compact('incomingClaims', 'myClaims'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'found_item_id' => 'required|exists:found_items,id',
            'description'   => 'required|string',
            'proof_image'   => 'nullable|image|max:2048',
        ]);

        $exists = Claim::where('user_id', Auth::id())->where('found_item_id', $request->found_item_id)->exists();
        if ($exists) return back()->with('error', 'Anda sudah mengajukan klaim untuk barang ini.');

        $foundItem = FoundItem::findOrFail($request->found_item_id);
        if ($foundItem->user_id == Auth::id()) return back()->with('error', 'Anda tidak bisa mengklaim barang temuan sendiri.');

        $proofPath = $request->hasFile('proof_image') ? $request->file('proof_image')->store('claim_proofs', 'public') : null;

        Claim::create([
            'found_item_id'    => $request->found_item_id,
            'user_id'          => Auth::id(),
            'description'      => $request->description,
            'proof_image_path' => $proofPath,
            'status'           => 'pending',
        ]);
        
        return back()->with('success', 'Klaim berhasil diajukan!');
    }

    /**
     * PROSES VERIFIKASI (Approve / Upload / Verify / Reject)
     */
  public function verify(Request $request, $id)
    {
        $claim = Claim::with('foundItem')->findOrFail($id);
        $foundItem = $claim->foundItem;
        $action = $request->input('action'); 

        // 1. APPROVE
        if ($action === 'approve') {
            if (Auth::id() !== $foundItem->user_id) abort(403);
            $claim->update(['status' => 'approved']);
            return back()->with('success', 'Klaim disetujui! Silakan upload bukti serah terima.');
        }

        // 2. UPLOAD
        if ($action === 'upload') {
            if (Auth::id() !== $foundItem->user_id) abort(403);
            $request->validate(['handover_photo' => 'required|image|max:4096']);
            $path = $request->file('handover_photo')->store('handover_proofs', 'public');
            $claim->update(['handover_photo_path' => $path]);
            return back()->with('success', 'Foto bukti berhasil diupload! Menunggu Admin.');
        }

        // 3. VERIFY (Admin Selesaikan)
        if ($action === 'verify') {
            if (Auth::user()->role !== 'admin') abort(403);

            if (!$claim->handover_photo_path) {
                return back()->with('error', 'Gagal! Penemu belum mengupload foto bukti.');
            }

            DB::transaction(function() use ($claim, $foundItem) {
                // A. Update Status Klaim
                $claim->update([
                    'status' => 'verified',
                    'verified_at' => now(),
                    'verified_by' => Auth::id(),
                ]);

                // B. Update Status Barang Temuan
                $foundItem->update(['status' => 'diambil']);

                // C. UPDATE STATUS BARANG HILANG (DIPERBAIKI)
                // Cari laporan kehilangan milik user ini yang masih 'dicari' (paling baru)
                // Kita hapus filter kategori agar lebih fleksibel
                $matchedLostItem = LostItem::where('user_id', $claim->user_id)
                    ->where('status', 'dicari')
                    ->latest() 
                    ->first();

                if ($matchedLostItem) {
                    // Update ke 'selesai' (Pastikan enum di database support 'selesai')
                    // Jika enum kamu 'ditemukan', ganti 'selesai' jadi 'ditemukan'
                    $matchedLostItem->update(['status' => 'selesai']);
                }
                
                // D. Tolak klaim lain
                Claim::where('found_item_id', $foundItem->id)
                     ->where('id', '!=', $claim->id)
                     ->update(['status' => 'rejected']);
            });

            return back()->with('success', 'Transaksi Selesai! Status barang hilang telah diupdate.');
        }

        // 4. REJECT
        if ($action === 'reject') {
            if (Auth::user()->role !== 'admin' && Auth::id() !== $foundItem->user_id) abort(403);
            $claim->update(['status' => 'rejected']);
            return back()->with('success', 'Klaim DITOLAK.');
        }

        return back();
    }
}