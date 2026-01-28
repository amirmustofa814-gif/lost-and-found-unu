<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LostItem;
use App\Models\FoundItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatchController extends Controller
{
    /**
     * GET /api/matches
     * Mencari kecocokan otomatis antara Barang Hilang User vs Database Barang Temuan
     */
    public function index()
    {
        // 1. Ambil barang yang SAYA cari (status 'dicari')
        $myLostItems = LostItem::where('user_id', Auth::id())
                        ->where('status', 'dicari')
                        ->get();

        $results = [];

        // 2. Loop setiap barang hilang saya
        foreach ($myLostItems as $lost) {
            
            // 3. Query Pencarian ke tabel FoundItem
            $query = FoundItem::where('status', 'tersedia') // Hanya cari yang belum diambil
                        ->where('category_id', $lost->category_id); // Kategori WAJIB sama

            // 4. Logika Pencocokan Kata Kunci (Nama & Deskripsi)
            $keywords = explode(' ', $lost->item_name); // Pecah nama jadi kata-kata
            
            $query->where(function($q) use ($keywords) {
                foreach ($keywords as $word) {
                    // Abaikan kata pendek (di, ke, yg, dll)
                    if (strlen($word) > 2) {
                        $q->orWhere('item_name', 'LIKE', "%{$word}%")
                          ->orWhere('description', 'LIKE', "%{$word}%");
                    }
                }
            });

            // Eksekusi query
            $candidates = $query->with(['primaryImage', 'user'])->get();

            // 5. Jika ada kandidat cocok, masukkan ke hasil
            if ($candidates->count() > 0) {
                $results[] = [
                    'lost_item' => $lost,       // Barang yang saya cari
                    'candidates' => $candidates // Barang temuan yang mirip
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $results
        ]);
    }
}