<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     * 
     * Middleware ini digunakan untuk membatasi akses berdasarkan role admin
     * Contoh penggunaan: Route::middleware(['auth:admin', 'admin.role:admin'])
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $admin = Auth::guard('admin')->user();

        // Cek apakah admin memiliki role yang diizinkan
        if (!in_array($admin->role, $roles)) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}