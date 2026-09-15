<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCis
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('cis_user')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
