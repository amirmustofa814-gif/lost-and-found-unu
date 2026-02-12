<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
   
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

   
    public function showUser($id)
    {
        // Ambil user beserta relasi datanya (Barang Hilang, Temuan, Klaim)
        $user = User::with(['lostItems', 'foundItems', 'claims.foundItem'])
                    ->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    
    public function destroyUser($id)
    {
        // 1. Cari User
        $user = User::with(['foundItems.images', 'claims'])->findOrFail($id);

        // 2. Cegah Admin Menghapus Dirinya Sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri saat sedang login!');
        }

        // 3. Hapus FOTO Barang Temuan User ini dari Storage
        foreach ($user->foundItems as $item) {
            foreach ($item->images as $image) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
            }
        }

        // 4. Hapus FOTO Bukti Klaim User ini dari Storage
        foreach ($user->claims as $claim) {
            if ($claim->proof_image_path && Storage::disk('public')->exists($claim->proof_image_path)) {
                Storage::disk('public')->delete($claim->proof_image_path);
            }
        }
        
        $user->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Pengguna dan seluruh datanya berhasil dihapus.');
    }
}