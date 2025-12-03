<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Set headers untuk mencegah back
                $response = match($guard) {
                    'admin' => redirect()->route('admin.dashboard'),
                    'student' => redirect()->route('mahasiswa.dashboard'),
                    default => redirect(RouteServiceProvider::HOME),
                };

                // Tambahkan header untuk mencegah cache
                return $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                                ->header('Pragma', 'no-cache')
                                ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
            }
        }

        return $next($request);
    }
}