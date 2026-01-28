<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\FoundItem;
use App\Models\LostItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NewClaimNotification; 
use App\Notifications\ClaimStatusUpdated;    

class ClaimController extends Controller
{
    /**
     * Tampilkan Dashboard Klaim (Pusat Validasi)
     */
    public function index()
    {
        $user = Auth::user();

        // --- LOGIKA ADMIN ---
        if ($user->role === 'admin') {
            // Admin melihat SEMUA klaim masuk
            $incomingClaims = Claim::with(['foundItem', 'user'])->latest()->get();
            
            // Gunakan collect([]) agar dianggap Collection kosong
            $myClaims = collect([]); 
        } 
        // --- LOGIKA USER BIASA ---
        else {
            // Incoming: Orang lain mengklaim barang saya
            $incomingClaims = Claim::whereHas('foundItem', function ($query) {
                $query->where('user_id', Auth::id());
            })->with(['foundItem', 'user'])->latest()->get();

            // Outgoing: Saya mengklaim barang orang lain
            $myClaims = Claim::where('user_id', Auth::id())
                            ->with(['foundItem'])
                            ->latest()
                            ->get();
        }

        return view('claims.index', compact('incomingClaims', 'myClaims'));
    }

    /**
     * Tampilkan Detail Klaim
     */
    public function show($id)
    {
        $claim = Claim::with(['foundItem', 'user', 'foundItem.user'])->findOrFail($id);

        $isRequester = $claim->user_id === Auth::id();
        $isFinder    = $claim->foundItem->user_id === Auth::id();
        $isAdmin     = Auth::user()->role === 'admin';

        if (!$isRequester && !$isFinder && !$isAdmin) {
            abort(403, 'Anda tidak berhak melihat data klaim ini.');
        }

        return view('claims.show', compact('claim', 'isRequester', 'isFinder'));
    }

    /**
     * Proses Pengajuan Klaim (+ Upload Foto Bukti)
     */
    public function store(Request $request)
    {
        $request->validate([
            'found_item_id' => 'required|exists:found_items,id',
            'description'   => 'required|string',
            'proof_image'   => 'nullable|image|max:2048',
        ]);

        $foundItem = FoundItem::findOrFail($request->found_item_id);
        if ($foundItem->user_id == Auth::id()) {
            return back()->with('error', 'Anda tidak bisa mengklaim barang yang Anda temukan sendiri.');
        }

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('claim_proofs', 'public');
        }

        $claim = Claim::create([
            'found_item_id'    => $request->found_item_id,
            'user_id'          => Auth::id(),
            'description'      => $request->description,
            'proof_image_path' => $proofPath,
            'status'           => 'pending',
        ]);

        // --- KIRIM NOTIFIKASI KE PENEMU BARANG ---
        $finder = $foundItem->user;
        if ($finder) {
            $finder->notify(new NewClaimNotification($claim));
        }

        return back()->with('success', 'Klaim berhasil diajukan! Tunggu verifikasi dari penemu.');
    }

    /**
     * Aksi Verifikasi (Terima dengan Foto Serah Terima ATAU Tolak)
     * Menggabungkan logika verify dan reject dalam satu fungsi
     */
    public function verify(Request $request, $id)
    {
        $claim = Claim::with('foundItem')->findOrFail($id);
        $foundItem = $claim->foundItem;

        // Cek Hak Akses (Hanya Admin atau Penemu Asli)
        if (Auth::user()->role !== 'admin' && Auth::id() !== $foundItem->user_id) {
            abort(403, 'Anda tidak berhak memproses klaim ini.');
        }

        // --- SKENARIO 1: TERIMA KLAIM (VERIFY) ---
        if ($request->action === 'verify') {
            
            // 1. Validasi Wajib Foto Serah Terima
            $request->validate([
                'handover_photo' => 'required|image|max:4096' // Maks 4MB
            ], [
                'handover_photo.required' => 'Foto serah terima wajib diupload sebagai bukti!'
            ]);

            DB::transaction(function() use ($request, $claim, $foundItem) {
                
                // 2. Simpan Foto Serah Terima
                $handoverPath = $request->file('handover_photo')->store('handover_proofs', 'public');

                // 3. Update Status Klaim
                $claim->update([
                    'status' => 'verified',
                    'verified_at' => now(),
                    'verified_by' => Auth::id(),
                    'handover_photo_path' => $handoverPath // Simpan path foto
                ]);

                // 4. Update Status Barang Temuan jadi 'diambil'
                $foundItem->update(['status' => 'diambil']);

                // 5. Update Status Barang Hilang si Pengklaim (Fitur Cerdas)
                LostItem::where('user_id', $claim->user_id)
                    ->where('category_id', $foundItem->category_id)
                    ->where('status', 'dicari')
                    ->update(['status' => 'ditemukan']);

                // 6. Otomatis tolak klaim lain untuk barang yang sama
                $otherClaims = Claim::where('found_item_id', $foundItem->id)
                    ->where('id', '!=', $claim->id)
                    ->get();
                
                foreach ($otherClaims as $otherClaim) {
                    $otherClaim->update(['status' => 'rejected']);
                    // Opsional: Kirim notifikasi penolakan ke user lain
                    $otherClaim->user->notify(new ClaimStatusUpdated($otherClaim, 'rejected'));
                }
            });

            // --- KIRIM NOTIFIKASI DITERIMA KE PENGKLAIM ---
            $claim->user->notify(new ClaimStatusUpdated($claim, 'verified'));

            return back()->with('success', '✅ Klaim DITERIMA! Foto serah terima berhasil disimpan & status barang diperbarui.');
        }

        // --- SKENARIO 2: TOLAK KLAIM (REJECT) ---
        if ($request->action === 'reject') {
            $claim->update(['status' => 'rejected']);
            
            // --- KIRIM NOTIFIKASI DITOLAK KE PENGKLAIM ---
            $claim->user->notify(new ClaimStatusUpdated($claim, 'rejected'));

            return back()->with('success', '❌ Klaim telah DITOLAK.');
        }

        return back();
    }
}