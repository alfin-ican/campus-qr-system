<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login sebagai admin
        if (!Auth::guard('admin')->check()) {
            return $this->addNoCacheHeaders(
                redirect()->route('admin.login')
                    ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.')
            );
        }

        // Cek apakah akun admin aktif
        $admin = Auth::guard('admin')->user();
        if (!$admin->is_active) {
            Auth::guard('admin')->logout();
            return $this->addNoCacheHeaders(
                redirect()->route('admin.login')
                    ->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.')
            );
        }

        // Jika admin sudah login, redirect dari homepage ke dashboard
        if ($request->is('/') || $request->is('home')) {
            return $this->addNoCacheHeaders(
                redirect()->route('admin.dashboard')
            );
        }

        $response = $next($request);

        // Set headers untuk mencegah back history
        // Kecuali untuk StreamedResponse dan BinaryFileResponse (download files)
        return $this->addNoCacheHeaders($response);
    }

    /**
     * Add no-cache headers to response
     * Handle different response types
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addNoCacheHeaders(Response $response): Response
    {
        // Skip untuk StreamedResponse dan BinaryFileResponse (download files)
        if ($response instanceof StreamedResponse || $response instanceof BinaryFileResponse) {
            return $response;
        }

        // Cek apakah response memiliki method headers
        if (method_exists($response, 'headers')) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
        } elseif (method_exists($response, 'header')) {
            // Untuk RedirectResponse dan Response biasa
            $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            $response->header('Pragma', 'no-cache');
            $response->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
        }

        return $response;
    }
}