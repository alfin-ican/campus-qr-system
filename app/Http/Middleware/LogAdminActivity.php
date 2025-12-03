<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogAdminActivity
{
    /**
     * Handle an incoming request.
     * Middleware ini mencatat aktivitas admin untuk audit trail
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya log untuk admin yang sudah login
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            
            // Log hanya untuk action tertentu (POST, PUT, DELETE)
            if (in_array($request->method(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
                try {
                    Log::channel('single')->info('Admin Activity', [
                        'admin_id' => $admin->admin_id,
                        'admin_name' => $admin->name,
                        'method' => $request->method(),
                        'url' => $request->fullUrl(),
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'timestamp' => now()->toDateTimeString(),
                    ]);
                } catch (\Exception $e) {
                    // Silent fail - don't break the request if logging fails
                }
            }
        }

        return $response;
    }
}