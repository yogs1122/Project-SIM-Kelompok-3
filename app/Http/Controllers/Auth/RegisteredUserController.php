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
use App\Models\Wallet;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required','string','unique:users,phone','regex:/^\+?\d{9,15}$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:user,umkm'], // tambah validasi role
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(), // ✅ TAMBAH BARIS INI
        ]);

        // Buat wallet otomatis yang account_number = phone (digits only)
        $phoneDigits = preg_replace('/\D+/', '', $request->phone);
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
            'account_number' => $phoneDigits,
        ]);

        // Assign role ke user
        $user->assignRole($request->role);

        // OPTIONAL: Kamu bisa KOMENTARI baris ini jika mau
        // event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}