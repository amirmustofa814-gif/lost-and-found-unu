<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Import Model
use App\Models\FoundItem;
use App\Models\ItemImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FoundItemController extends Controller
{
    /**
     * Menyimpan data barang temuan baru (Store).
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'item_name'       => 'required|string|max:255',
            'category_id'     => 'nullable|exists:categories,id',
            'description'     => 'required|string',
            'location_found'  => 'required|string',
            'date_found'      => 'required|date',
            'current_position'=> 'required|string', // Contoh: "Pos Satpam"
            // Validasi untuk upload banyak gambar
            'images'          => 'nullable|array',
            'images.*'        => 'image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        // Jika validasi gagal, kembalikan error
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Simpan Data Barang ke Database
        $foundItem = FoundItem::create([
            'user_id'          => Auth::id(), // Ambil ID user yang sedang login
            'category_id'      => $request->category_id,
            'item_name'        => $request->item_name,
            'description'      => $request->description,
            'location_found'   => $request->location_found,
            'date_found'       => $request->date_found,
            'current_position' => $request->current_position,
            'status'           => 'tersedia', // Default status
        ]);

        // 3. Proses Upload Gambar (Multiple)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Simpan file ke storage (folder: public/found-items)
                $path = $image->store('found-items', 'public');

                // Simpan path-nya ke tabel item_images
                ItemImage::create([
                    'found_item_id' => $foundItem->id,
                    'image_path'    => $path,
                ]);
            }
        }

        // 4. Load relasi gambar agar muncul di response
        $foundItem->load('images');

        // 5. Berikan Response JSON Sukses
        return response()->json([
            'status'  => 'success',
            'message' => 'Laporan barang temuan berhasil dibuat.',
            'data'    => $foundItem,
        ], 201);
    }
}