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
     * Menampilkan daftar barang hilang milik User (atau semua jika Admin)
     */
    public function index()
    {
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
            'phone_number'  => 'required|string|max:15',
            'description'   => 'required|string',
            'images.*'      => 'image|mimes:jpeg,png,jpg|max:2048' // Validasi banyak gambar
        ]);

        $lostItem = LostItem::create([
            'user_id'       => Auth::id(),
            'category_id'   => $request->category_id,
            'item_name'     => $request->item_name,
            'description'   => $request->description,
            'phone_number'  => $request->phone_number,
            'location_lost' => $request->location_lost, // Pastikan kolom database sesuai (location / location_lost)
            'date_lost'     => $request->date_lost,
            'status'        => 'dicari',
            'created_by'    => Auth::id(),
        ]);

        // Simpan Multiple Images
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
     * Tampilkan Detail Barang
     */
    public function show($id)
    {
        $lostItem = LostItem::with(['images', 'category'])->findOrFail($id);
        return view('lost.show', compact('lostItem'));
    }

    /**
     * Tampilkan Form Edit
     */
    public function edit($id)
    {
        $lostItem = LostItem::findOrFail($id);

        // Keamanan: Cek pemilik atau admin
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
        // dd($request->all());
        // Validasi Hak Akses
        if ($lostItem->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        // Validasi Input
        $request->validate([
            'item_name'     => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'location_lost' => 'required|string|max:255',
            'date_lost'     => 'required|date',
            'phone_number'  => 'required|string|max:15',
            'description'   => 'required|string',
            'image'         => 'nullable|image|max:2048', // Validasi Single Image (image bukan images)
        ]);

        // 1. Logika Ganti Foto (Jika user upload foto baru)
        if ($request->hasFile('image')) {
            // Hapus gambar lama dari storage & database
            if ($lostItem->primaryImage) {
                if (Storage::disk('public')->exists($lostItem->primaryImage->image_path)) {
                    Storage::disk('public')->delete($lostItem->primaryImage->image_path);
                }
                $lostItem->primaryImage()->delete();
            }

            // Simpan gambar baru
            $path = $request->file('image')->store('lost_images', 'public');
            
            // Masukkan ke database sebagai foto utama baru
            $lostItem->images()->create([
                'image_path' => $path, 
                'is_primary' => true
            ]);
        }

        // 2. Update Data Teks
        $lostItem->update([
            'item_name'     => $request->item_name,
            'category_id'   => $request->category_id,
            'location_lost' => $request->location_lost,
            'date_lost'     => $request->date_lost,
            'phone_number'  => $request->phone_number,
            'description'   => $request->description,
        ]);

        return redirect()->route('lost.show', $lostItem->id)->with('success', 'Laporan berhasil diperbarui!');
    }

    /**
     * Hapus Barang
     */
    public function destroy($id)
    {
        $lostItem = LostItem::findOrFail($id);

        // Keamanan
        if ($lostItem->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        // Hapus semua file gambar dari storage
        foreach($lostItem->images as $img) {
            if (Storage::disk('public')->exists($img->image_path)) {
                Storage::disk('public')->delete($img->image_path);
            }
        }

        // Hapus record dari database
        $lostItem->delete();

        return redirect()->route('lost.index')->with('success', 'Laporan berhasil dihapus.');
    }
}