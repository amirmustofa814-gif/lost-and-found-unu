<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\LostItemController;
use App\Http\Controllers\FoundItemController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\AdminController;
use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\Claim;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- HOMEPAGE ---
Route::get('/', function () {
    $lostItems = LostItem::where('status', 'dicari')
                         ->with('primaryImage')
                         ->latest()
                         ->get();
    return view('welcome', compact('lostItems'));
});

// --- DASHBOARD ---
Route::get('/dashboard', function () {
    
    // Cek Role User
    if (Auth::user()->role === 'admin') {
        // JIKA ADMIN: Hitung SEMUA data di database (Global Stats)
        $totalLost  = LostItem::where('status', 'dicari')->count();
        $totalFound = FoundItem::where('status', 'tersedia')->count();
        $myClaims   = Claim::count(); 
    } else {
        // JIKA MAHASISWA: Hitung data MILIK SENDIRI saja (Personal Stats)
        $totalLost  = LostItem::where('user_id', Auth::id())->where('status', 'dicari')->count();
        $totalFound = FoundItem::where('user_id', Auth::id())->where('status', 'tersedia')->count();
        $myClaims   = Claim::where('user_id', Auth::id())->count();
    }
    
    return view('dashboard', compact('totalLost', 'totalFound', 'myClaims'));
})->middleware(['auth', 'verified'])->name('dashboard');


// --- GRUP ROUTE YANG BUTUH LOGIN ---
Route::middleware('auth')->group(function () {
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- 1. RUTE BARANG HILANG ---
    Route::resource('lost', LostItemController::class);

    // --- 2. RUTE BARANG TEMUAN ---
    Route::resource('found', FoundItemController::class);

    // --- 3. RUTE KLAIM ---
    Route::resource('claims', ClaimController::class);
    
    // Route Khusus Verifikasi/Tolak Klaim
    Route::patch('/claims/{id}/verify', [ClaimController::class, 'verify'])->name('claims.verify');

    // --- 4. RUTE KECOCOKAN (SMART MATCH) ---
    Route::get('/matches', [MatchController::class, 'index'])->name('match.index');

}); 


// --- ROUTE KHUSUS ADMIN ---
// Pastikan Middleware Admin sesuai dengan nama class atau alias yang kamu buat
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    
    // Dashboard Admin
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Detail Profil 
    Route::get('/admin/users/{id}', [AdminController::class, 'showUser'])->name('admin.users.show');

    Route::delete('/admin/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
});

require __DIR__.'/auth.php';