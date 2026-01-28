<?php

namespace App\Http\Controllers;

use App\Models\LostItem;
use App\Models\FoundItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatchController extends Controller
{
    /**
     * Menampilkan halaman kecocokan cerdas
     */
    public function index()
    {
        // 1. TENTUKAN DATA BARANG HILANG MANA YANG MAU DICEK
        if (Auth::user()->role === 'admin') {
            // JIKA ADMIN: Ambil SEMUA barang hilang yang masih dicari (Global Scan)
            // Kita load relasi 'user' agar admin tahu itu barang milik siapa
            $lostItems = LostItem::where('status', 'dicari')
                            ->with('user') 
                            ->latest()
                            ->get();
        } else {
            // JIKA USER: Hanya ambil barang hilang miliknya sendiri
            $lostItems = LostItem::where('user_id', Auth::id())
                            ->where('status', 'dicari')
                            ->latest()
                            ->get();
        }

        $results = [];

        // 2. Loop setiap barang hilang untuk cari jodohnya
        foreach ($lostItems as $lost) {
            
            // 3. Cari barang temuan yang KATEGORINYA SAMA & STATUSNYA TERSEDIA
            $query = FoundItem::where('status', 'tersedia')
                            ->where('category_id', $lost->category_id);

            // 4. Logika Pencocokan Kata Kunci (Nama & Deskripsi)
            // Contoh: "Dompet Hitam" -> Cari yang ada kata "Dompet" ATAU "Hitam"
            $keywords = explode(' ', $lost->item_name);
            
            $query->where(function($q) use ($keywords) {
                foreach ($keywords as $word) {
                    // Abaikan kata pendek (di, ke, yg, dll) agar hasil relevan (minimal 3 huruf)
                    if (strlen($word) > 2) {
                        $q->orWhere('item_name', 'LIKE', "%{$word}%")
                          ->orWhere('description', 'LIKE', "%{$word}%");
                    }
                }
            });

            // Eksekusi query
            $candidates = $query->with('primaryImage')->get();

            // 5. Jika ada kandidat cocok, masukkan ke hasil
            if ($candidates->count() > 0) {
                $results[] = [
                    'lost_item'  => $lost,      // Barang yang dicari
                    'candidates' => $candidates // Daftar barang temuan yang mirip
                ];
            }
        }

        return view('match.index', compact('results'));
    }
}