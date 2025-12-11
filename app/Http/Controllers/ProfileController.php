<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
    
    public function updatePassword(Request $request): RedirectResponse
{
    $validated = $request->validateWithBag('updatePassword', [
        'current_password' => ['required', 'current_password'],
        'password' => ['required', \Illuminate\Validation\Rules\Password::defaults(), 'confirmed'],
    ]);

    $request->user()->update([
        'password' => bcrypt($validated['password']),
    ]);

    return Redirect::route('profile.edit')->with('status', 'password-updated');
}
    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Show the upgrade to UMKM form.
     */
    public function showUpgradeForm(Request $request): View
    {
        return view('umkm.upgrade');
    }

    /**
     * Upgrade the authenticated user to UMKM role.
     */
    public function upgradeToUmkm(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isUMKM()) {
            return Redirect::route('profile.edit')->with('success', 'Anda sudah menjadi Pedagang UMKM.');
        }

        // Assign role 'umkm' (sederhana — jika butuh verifikasi, tambahkan proses approval)
        $user->assignRole('umkm');

        return Redirect::route('profile.edit')->with('success', 'Akun berhasil diupgrade menjadi Pedagang UMKM.');
    }
}
