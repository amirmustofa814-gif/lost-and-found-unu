<?php

namespace App\Http\Controllers;

use App\Models\FoundItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FoundItemController extends Controller
{
    /**
     * Tampilkan Daftar Barang Temuan (Fitur Search & Filter)
     */
    public function index(Request $request)
    {
        // 1. Query Dasar: Ambil barang yang statusnya 'tersedia'
        $query = FoundItem::with(['category', 'images'])
                    ->where('status', 'tersedia');

        // 2. Filter Pencarian Nama
        if ($request->filled('search')) {
            $query->where('item_name', 'like', '%' . $request->search . '%');
        }

        // 3. Filter Kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 4. Eksekusi Query (Pagination 9 item per halaman)
        $foundItems = $query->latest()->paginate(9)->withQueryString();

        // 5. Ambil Kategori untuk Dropdown Filter
        $categories = Category::all();

        return view('found.index', compact('foundItems', 'categories'));
    }

    /**
     * Form lapor penemuan barang
     */
    public function create()
    {
        $categories = Category::all();
        return view('found.create', compact('categories'));
    }

    /**
     * Simpan data penemuan baru (VALIDASI 2-5 FOTO)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'item_name'      => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'date_found'     => 'required|date',
            'time_found'     => 'required', 
            'location_found' => 'required|string|max:255',
            'phone_number'   => 'required|string|max:15',
            'description'    => 'required|string',
            
            // --- ATURAN BARU: ARRAY FOTO MIN 2 MAX 5 ---
            'images'         => 'required|array|min:2|max:5', 
            'images.*'       => 'image|mimes:jpeg,png,jpg|max:2048'
        ], [
            // Pesan Error Custom
            'images.required' => 'Wajib mengupload foto barang bukti.',
            'images.min'      => 'Harap upload minimal 2 foto agar kondisi barang terlihat jelas.',
            'images.max'      => 'Maksimal hanya boleh mengupload 5 foto.',
            'images.*.image'  => 'File harus berupa gambar (JPG/PNG).',
            'images.*.max'    => 'Ukuran foto terlalu besar (Maksimal 2MB per foto).'
        ]);

        // 2. Simpan ke Database
        $foundItem = FoundItem::create([
            'user_id'        => Auth::id(),
            'category_id'    => $request->category_id,
            'item_name'      => $request->item_name,
            'description'    => $request->description,
            'phone_number'   => $request->phone_number,
            'location_found' => $request->location_found,
            'date_found'     => $request->date_found,
            'time_found'     => $request->time_found,
            'status'         => 'tersedia',
            'created_by'     => Auth::id(),
        ]);

        // 3. Simpan Foto (Looping Array)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('found_images', 'public');
                $foundItem->images()->create([
                    'image_path' => $path,
                    // Foto urutan pertama (index 0) otomatis jadi foto utama
                    'is_primary' => $index === 0 
                ]);
            }
        }

        return redirect()->route('found.index')->with('success', 'Laporan penemuan berhasil dibuat! ✅');
    }

    /**
     * Lihat detail barang temuan
     */
    public function show($id)
    {
        $foundItem = FoundItem::with(['images', 'category', 'user'])->findOrFail($id);
        return view('found.show', compact('foundItem'));
    }

    /**
     * Tampilkan Form Edit
     */
    public function edit($id)
    {
        $foundItem = FoundItem::findOrFail($id);

        // Keamanan: Cek apakah yang edit adalah pemilik asli atau admin
        if ($foundItem->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengedit laporan ini.');
        }

        $categories = Category::all(); 
        return view('found.edit', compact('foundItem', 'categories'));
    }

    /**
     * Proses Update Data
     */
    public function update(Request $request, $id)
    {
        $foundItem = FoundItem::findOrFail($id);

        // Validasi Hak Akses
        if ($foundItem->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'item_name'      => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'location_found' => 'required|string|max:255',
            'date_found'     => 'required|date',
            'time_found'     => 'required', 
            'phone_number'   => 'required|string|max:15',
            'description'    => 'required|string',
            'image'          => 'nullable|image|max:2048', 
        ]);

        // Logika Update Gambar (Single Replace)
        if ($request->hasFile('image')) {
            if ($foundItem->primaryImage) {
                Storage::disk('public')->delete($foundItem->primaryImage->image_path);
                $foundItem->primaryImage()->delete();
            }
            $path = $request->file('image')->store('found_images', 'public');
            $foundItem->images()->create(['image_path' => $path, 'is_primary' => true]);
        }

        // Update Data Teks
        $foundItem->update([
            'item_name'      => $request->item_name,
            'category_id'    => $request->category_id,
            'location_found' => $request->location_found,
            'date_found'     => $request->date_found,
            'time_found'     => $request->time_found, 
            'phone_number'   => $request->phone_number,
            'description'    => $request->description,
        ]);

        return redirect()->route('found.show', $foundItem->id)->with('success', 'Laporan berhasil diperbarui!');
    }

    /**
     * Hapus Barang Temuan
     */
    public function destroy($id)
    {
        $foundItem = FoundItem::findOrFail($id);

        // Keamanan: Hanya Pemilik atau Admin
        if ($foundItem->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        // Hapus file gambar dari storage
        foreach($foundItem->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }

        $foundItem->delete();

        return redirect()->route('found.index')->with('success', 'Laporan penemuan berhasil dihapus.');
    }
}