<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        
        if ($user->roles()->where('name', $role)->exists()) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses!');
    }
}