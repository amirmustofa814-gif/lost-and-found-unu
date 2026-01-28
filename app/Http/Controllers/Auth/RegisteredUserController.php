<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            
            // 1. NIM / Identitas (Tetap Wajib & Unik)
            'nim' => ['required', 'string', 'max:20', 'unique:'.User::class],

            // 2. VALIDASI EMAIL (BEBAS / UMUM)
            // Saya sudah menghapus logika pembatasan domain.
            // Sekarang bisa pakai @gmail.com, @yahoo.com, dll.
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],

            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'nim' => $request->nim,   // Simpan NIM
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa',    // Default role
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}