<?php

namespace App\Http\Controllers;

use App\Models\LostItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LostItemController extends Controller
{
    /**
     * Menampilkan daftar barang hilang milik User
     */
    public function index()
    {
        // LOGIKA: Admin lihat semua, User lihat punya sendiri
        if (Auth::user()->role === 'admin') {
            $lostItems = LostItem::with('primaryImage')->latest()->get();
        } else {
            $lostItems = LostItem::where('user_id', Auth::id())
                                 ->with('primaryImage')
                                 ->latest()
                                 ->get();
        }

        return view('lost.index', compact('lostItems'));
    }

    /**
     * Form Tambah Laporan
     */
    public function create()
    {
        $categories = Category::all();
        return view('lost.create', compact('categories'));
    }

    /**
     * Simpan Data Baru (Store)
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_name'     => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'date_lost'     => 'required|date',
            'location_lost' => 'required|string|max:255',
            'phone_number'  => 'required|string|max:15', // Validasi Nomor HP
            'description'   => 'required|string',
            'images.*'      => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $lostItem = LostItem::create([
            'user_id'       => Auth::id(),
            'category_id'   => $request->category_id,
            'item_name'     => $request->item_name,
            'description'   => $request->description,
            'phone_number'  => $request->phone_number, // Simpan Nomor HP
            'location_lost' => $request->location_lost,
            'date_lost'     => $request->date_lost,
            'status'        => 'dicari',
            'created_by'    => Auth::id(),
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('lost_images', 'public');
                $lostItem->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index === 0 
                ]);
            }
        }

        return redirect()->route('lost.index')->with('success', 'Laporan kehilangan berhasil dibuat.');
    }

    /**
     * Tampilkan Form Edit
     */
    public function edit($id)
    {
        $lostItem = LostItem::findOrFail($id);

        // Keamanan: Cek apakah yang edit adalah pemilik asli atau admin
        if ($lostItem->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengedit laporan ini.');
        }

        return view('lost.edit', compact('lostItem'));
    }

    /**
     * Proses Update Data
     */
    public function update(Request $request, $id)
    {
        $lostItem = LostItem::findOrFail($id);

        // Validasi lagi hak akses
        if ($lostItem->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'item_name'     => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'location_lost' => 'required|string|max:255',
            'date_lost'     => 'required|date',
            'phone_number'  => 'required|string|max:15', // Validasi Nomor HP
            'description'   => 'required|string',
            'image'         => 'nullable|image|max:2048', // Validasi single image untuk edit
        ]);

        // Logika Update Gambar (Jika ada upload baru)
        if ($request->hasFile('image')) {
            // 1. Hapus gambar lama dari storage & database jika ada
            if ($lostItem->primaryImage) {
                Storage::disk('public')->delete($lostItem->primaryImage->image_path);
                $lostItem->primaryImage()->delete();
            }

            // 2. Upload gambar baru
            $path = $request->file('image')->store('lost_images', 'public');
            
            // 3. Simpan ke tabel relasi images (Set sebagai primary)
            $lostItem->images()->create([
                'image_path' => $path, 
                'is_primary' => true
            ]);
        }

        // Update Data Teks termasuk Nomor HP
        $lostItem->update([
            'item_name'     => $request->item_name,
            'category_id'   => $request->category_id,
            'location_lost' => $request->location_lost,
            'date_lost'     => $request->date_lost,
            'phone_number'  => $request->phone_number, // Update Nomor HP
            'description'   => $request->description,
        ]);

        return redirect()->route('lost.show', $lostItem->id)->with('success', 'Laporan berhasil diperbarui!');
    }

    /**
     * Detail Barang
     */
    public function show($id)
    {
        $lostItem = LostItem::with(['images', 'category'])->findOrFail($id);
        return view('lost.show', compact('lostItem'));
    }

    /**
     * Hapus Barang
     */
    public function destroy($id)
    {
        $lostItem = LostItem::findOrFail($id);

        // Update Keamanan: Izinkan Admin juga untuk menghapus
        if ($lostItem->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        foreach($lostItem->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }

        $lostItem->delete();

        return redirect()->route('lost.index')->with('success', 'Laporan berhasil dihapus.');
    }
}