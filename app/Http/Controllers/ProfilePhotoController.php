<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class ProfilePhotoController extends Controller
{
    public function upload(Request $request)
    {
        $validated = $request->validate([
            'photo' => [
                'required',
                File::image()
                    ->min('1kb')
                    ->max('5mb')
                    ->extensions(['jpg', 'jpeg', 'png', 'webp']),
            ],
        ]);

        $user = Auth::user();

        // Delete old photo if exists
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Store new photo
        $path = $request->file('photo')->store('profile-photos', 'public');
        
        $user->update([
            'profile_photo_path' => $path,
        ]);

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    public function delete()
    {
        $user = Auth::user();

        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->update([
            'profile_photo_path' => null,
        ]);

        return back()->with('success', 'Foto profil berhasil dihapus!');
    }
}
