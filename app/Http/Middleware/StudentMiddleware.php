<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login sebagai mahasiswa
        if (!Auth::guard('student')->check()) {
            return $this->addNoCacheHeaders(
                redirect()->route('mahasiswa.login')
                    ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.')
            );
        }

        // Cek apakah akun mahasiswa aktif
        $student = Auth::guard('student')->user();
        if (!$student->is_active) {
            Auth::guard('student')->logout();
            return $this->addNoCacheHeaders(
                redirect()->route('mahasiswa.login')
                    ->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.')
            );
        }

        // Jika mahasiswa sudah login, redirect dari homepage ke dashboard
        if ($request->is('/') || $request->is('home')) {
            return $this->addNoCacheHeaders(
                redirect()->route('mahasiswa.dashboard')
            );
        }

        $response = $next($request);

        return $this->addNoCacheHeaders($response);
    }

    /**
     * Add no-cache headers to response
     */
    protected function addNoCacheHeaders(Response $response): Response
    {
        if ($response instanceof StreamedResponse || $response instanceof BinaryFileResponse) {
            return $response;
        }

        if (method_exists($response, 'headers')) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
        } elseif (method_exists($response, 'header')) {
            $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            $response->header('Pragma', 'no-cache');
            $response->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
        }

        return $response;
    }
}