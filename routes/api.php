<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LostItemController;
use App\Http\Controllers\Api\FoundItemController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\ClaimController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Di sini kita mendaftarkan semua endpoint API.
| Semua route di sini otomatis diawali dengan prefix "/api".
| Contoh akses: http://localhost:8000/api/login
|
*/

// ========================================================================
// 1. PUBLIC ROUTES (Bisa diakses tanpa Login)
// ========================================================================

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// ========================================================================
// 2. PROTECTED ROUTES (Harus Login / Punya Token)
// ========================================================================

Route::middleware('auth:sanctum')->group(function () {

    // --- AUTH & USER PROFILE ---
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'userProfile']);

    // --- LOST ITEMS (Barang Hilang) ---
    // GET /api/lost-items       -> Lihat semua
    // POST /api/lost-items      -> Tambah baru
    // GET /api/lost-items/{id}  -> Lihat detail
    // PUT /api/lost-items/{id}  -> Update
    // DELETE /api/lost-items/{id} -> Hapus
    Route::apiResource('lost-items', LostItemController::class);

    // --- FOUND ITEMS (Barang Temuan) ---
    Route::apiResource('found-items', FoundItemController::class);

    // --- MATCHING SYSTEM (Kecocokan Cerdas) ---
    Route::get('/matches', [MatchController::class, 'index']);

    // --- CLAIMS (Transaksi Klaim) ---
    Route::get('/claims', [ClaimController::class, 'index']);                 // List klaim (masuk & keluar)
    Route::post('/claims/{found_item_id}', [ClaimController::class, 'store']); // Ajukan klaim
    Route::post('/claims/{claim_id}/verify', [ClaimController::class, 'verify']); // Terima klaim
    Route::post('/claims/{claim_id}/reject', [ClaimController::class, 'reject']); // Tolak klaim

    // --- MASTER DATA ---
    Route::get('/categories', function() {
        return response()->json(\App\Models\Category::all());
    });

});