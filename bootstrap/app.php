<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'student' => \App\Http\Middleware\StudentMiddleware::class,
            'admin.role' => \App\Http\Middleware\CheckAdminRole::class,
            'log.admin' => \App\Http\Middleware\LogAdminActivity::class,
            'prevent.back' => \App\Http\Middleware\PreventBackHistory::class,
            'lock.homepage' => \App\Http\Middleware\LockHomepage::class,
        ]);

        // Redirect if authenticated
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin/*')) {
                return route('admin.login');
            }
            if ($request->is('mahasiswa/*')) {
                return route('mahasiswa.login');
            }
            return route('home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();