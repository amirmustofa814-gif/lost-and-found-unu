<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\Claim;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Tampilkan Dashboard Admin Utama
     */
    public function index()
    {
        // Ambil Statistik untuk Dashboard Admin
        $totalUsers  = User::count();
        $totalLost   = LostItem::count();
        $totalFound  = FoundItem::count();
        $totalClaims = Claim::count();

        // Ambil Data Terbaru (misal 5 user terakhir)
        $latestUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact('totalUsers', 'totalLost', 'totalFound', 'totalClaims', 'latestUsers'));
    }

    /**
     * Tampilkan Detail Profil User & Riwayat Aktivitas
     */
    public function showUser($id)
    {
        // Ambil user beserta relasi datanya (Barang Hilang, Temuan, Klaim)
        // Menggunakan with() agar query lebih efisien (Eager Loading)
        $user = User::with(['lostItems', 'foundItems', 'claims.foundItem'])
                    ->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }
    /**
     * Hapus User & Bersihkan Data Terkait
     */
    public function destroyUser($id)
    {
        // 1. Cari User
        $user = User::with(['foundItems.images', 'claims'])->findOrFail($id);

        // 2. Cegah Admin Menghapus Dirinya Sendiri
        if ($user->id === \Illuminate\Support\Facades\Auth::id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri saat sedang login!');
        }

        // 3. Hapus FOTO Barang Temuan User ini dari Storage
        foreach ($user->foundItems as $item) {
            foreach ($item->images as $image) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($image->image_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
                }
            }
        }

        // 4. Hapus FOTO Bukti Klaim User ini dari Storage
        foreach ($user->claims as $claim) {
            if ($claim->proof_image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($claim->proof_image_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($claim->proof_image_path);
            }
        }

        // 5. Hapus User dari Database (Data relasi akan terhapus otomatis jika diset ON DELETE CASCADE di migrasi, 
        // tapi jika tidak, Laravel akan menanganinya jika model relasi dikonfigurasi dengan benar)
        $user->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Pengguna dan seluruh datanya berhasil dihapus.');
    }
}