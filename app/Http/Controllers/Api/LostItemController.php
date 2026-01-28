<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LostItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LostItemController extends Controller
{
    /**
     * GET /api/lost-items
     * Menampilkan semua barang hilang (terbaru diatas)
     */
    public function index()
    {
        $lostItems = LostItem::with(['user', 'category', 'primaryImage'])
                        ->where('status', 'dicari') // Hanya tampilkan yang masih dicari
                        ->latest()
                        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $lostItems
        ]);
    }

    /**
     * POST /api/lost-items
     * Membuat laporan kehilangan baru + Upload Foto
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'item_name'     => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'date_lost'     => 'required|date',
            'location_lost' => 'nullable|string|max:255',
            'description'   => 'required|string',
            'images'        => 'nullable|array',         // Array foto
            'images.*'      => 'image|mimes:jpeg,png,jpg|max:2048' // Validasi tiap foto
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // 2. Simpan Data Barang
            $lostItem = LostItem::create([
                'user_id'       => Auth::id(), // Ambil ID dari Token yang login
                'category_id'   => $request->category_id,
                'item_name'     => $request->item_name,
                'description'   => $request->description,
                'location_lost' => $request->location_lost,
                'date_lost'     => $request->date_lost,
                'status'        => 'dicari',
                'created_by'    => Auth::id(),
            ]);

            // 3. Proses Upload Multiple Image
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    // Simpan fisik file
                    $path = $image->store('lost_images', 'public');

                    // Simpan ke database item_images
                    $lostItem->images()->create([
                        'image_path' => $path,
                        'is_primary' => $index === 0 // Foto pertama jadi cover
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Laporan kehilangan berhasil dibuat.',
                'data' => $lostItem->load('images')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/lost-items/{id}
     * Lihat detail satu barang
     */
    public function show($id)
    {
        $lostItem = LostItem::with(['user', 'category', 'images'])->find($id);

        if (!$lostItem) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $lostItem
        ]);
    }

    /**
     * PUT /api/lost-items/{id}
     * Update data (misal ganti status jadi selesai)
     */
    public function update(Request $request, $id)
    {
        $lostItem = LostItem::find($id);

        if (!$lostItem) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        // Pastikan yang edit adalah pemiliknya atau admin
        if ($lostItem->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        // Update Status atau Info
        $lostItem->update($request->only([
            'item_name', 'description', 'location_lost', 'status'
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diperbarui',
            'data' => $lostItem
        ]);
    }

    /**
     * DELETE /api/lost-items/{id}
     * Hapus laporan
     */
    public function destroy($id)
    {
        $lostItem = LostItem::find($id);

        if (!$lostItem) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        if ($lostItem->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        // Hapus Foto Fisik dulu (Opsional, biar bersih)
        foreach($lostItem->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }

        $lostItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan berhasil dihapus'
        ]);
    }
}