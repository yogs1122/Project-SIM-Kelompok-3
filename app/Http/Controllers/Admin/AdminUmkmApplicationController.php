<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UmkmApplication;
use Illuminate\Support\Facades\Redirect;

class AdminUmkmApplicationController extends Controller
{
    public function index()
    {
        $applications = UmkmApplication::latest()->paginate(20);
        return view('admin.umkm_applications.index', compact('applications'));
    }

    public function show(UmkmApplication $application)
    {
        return view('admin.umkm_applications.show', compact('application'));
    }

    public function approve(Request $request, UmkmApplication $application)
    {
        $request->validate(['admin_note' => 'nullable|string|max:2000']);

        $application->status = 'approved';
        $application->admin_note = $request->input('admin_note');
        $application->save();

        // Assign role to user
        $user = $application->user;
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('umkm');
        }

        return Redirect::route('admin.umkm_applications.index')->with('success', 'Aplikasi UMKM disetujui.');
    }

    public function reject(Request $request, UmkmApplication $application)
    {
        $request->validate(['admin_note' => 'required|string|max:2000']);

        $application->status = 'rejected';
        $application->admin_note = $request->input('admin_note');
        $application->save();

        return Redirect::route('admin.umkm_applications.index')->with('success', 'Aplikasi UMKM ditolak.');
    }
}
