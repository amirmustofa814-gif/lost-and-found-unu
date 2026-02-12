<?php

use App\Http\Controllers\OtpController;
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

// --- HOMEPAGE --- //
Route::get('/', function () {
    $lostItems = LostItem::where('status', 'dicari')
                         ->with('primaryImage')
                         ->latest()
                         ->get();
    return view('welcome', compact('lostItems'));
});

// --- ROUTE OTP (PUBLIC) --- //
Route::get('/verify-otp', [OtpController::class, 'show'])->name('otp.show');
Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.verify');
Route::post('/ajax-send-otp', [OtpController::class, 'sendOtpAjax'])->name('otp.send.ajax');


// --- DASHBOARD --- //
Route::get('/dashboard', function () {

    $user = Auth::user();

    // Cek Role User //
  if ($user->hasRole('admin')) {
        // JIKA ADMIN: Hitung SEMUA data //
        $totalLost  = LostItem::where('status', 'dicari')->count();
        $totalFound = FoundItem::where('status', 'tersedia')->count();
        $myClaims   = Claim::count(); 
    } else {
        // JIKA MAHASISWA: Hitung data MILIK SENDIRI //
        $totalLost  = LostItem::where('user_id', Auth::id())->where('status', 'dicari')->count();
        $totalFound = FoundItem::where('user_id', Auth::id())->where('status', 'tersedia')->count();
        $myClaims   = Claim::where('user_id', Auth::id())->count();
    }
    
    return view('dashboard', compact('totalLost', 'totalFound', 'myClaims'));

})->middleware(['auth', 'verified', 'prevent-back-history'])->name('dashboard');


// --- GRUP ROUTE YANG BUTUH LOGIN --- //
Route::middleware('auth')->group(function () {
    
    // Profile Routes //
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- RUTE  --- //
    Route::resource('lost', LostItemController::class);
    Route::resource('found', FoundItemController::class);
    Route::resource('claims', ClaimController::class);
    Route::patch('/claims/{id}/verify', [ClaimController::class, 'verify'])->name('claims.verify');
    Route::get('/matches', [MatchController::class, 'index'])->name('match.index');

}); 


// --- ROUTE KHUSUS ADMIN --- //
Route::middleware(['auth', 'role:admin'])->group(function () {
    
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/users/{id}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');

});

require __DIR__.'/auth.php';