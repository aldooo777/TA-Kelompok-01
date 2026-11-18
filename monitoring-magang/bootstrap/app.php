<?php

use App\Http\Middleware\EnsurePdlkAccepted;
use App\Http\Middleware\AuthenticateCis;
use App\Http\Middleware\EnsurePeran;
use App\Http\Middleware\MonevWajib;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'cis.auth' => AuthenticateCis::class,
        'pdlk.accepted' => EnsurePdlkAccepted::class,
        'peran' => EnsurePeran::class,
        'monev.wajib' => MonevWajib::class,
        'sprint' => \App\Http\Middleware\EnsureSprint::class,
    ]);

    // Pengunjung yang belum login diarahkan ke halaman login CIS.
    $middleware->redirectGuestsTo(fn () => route('login'));
})
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
