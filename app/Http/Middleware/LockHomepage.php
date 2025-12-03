<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LockHomepage
{
    /**
     * Handle an incoming request.
     * 
     * Middleware ini mengunci homepage agar tombol back browser
     * selalu menampilkan homepage yang sama
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user sudah login (admin/mahasiswa), redirect ke dashboard masing-masing
        if (Auth::guard('admin')->check()) {
            return $this->addNoCacheHeaders(
                redirect()->route('admin.dashboard')
            );
        }

        if (Auth::guard('student')->check()) {
            return $this->addNoCacheHeaders(
                redirect()->route('mahasiswa.dashboard')
            );
        }

        // Untuk guest user, set headers untuk lock homepage
        $response = $next($request);

        return $this->addNoCacheHeaders($response);
    }

    /**
     * Add no-cache headers to response
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

        if (method_exists($response, 'headers')) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        } elseif (method_exists($response, 'header')) {
            $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            $response->header('Pragma', 'no-cache');
            $response->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
            $response->header('X-Frame-Options', 'SAMEORIGIN');
            $response->header('X-Content-Type-Options', 'nosniff');
        }

        return $response;
    }
}