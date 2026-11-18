<?php

namespace App\Http\Middleware;

use App\Support\Peran;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contoh pemakaian: ->middleware('peran:upt_staff,pdlk_coordinator')
 * Akses diberikan bila PERAN AKTIF pengguna termasuk dalam daftar.
 */
class EnsurePeran
{
    public function handle(Request $request, Closure $next, string ...$peran): Response
    {
        if (!session()->has('cis_user')) {
            return redirect()->guest(route('login'));
        }

        if (!Peran::aktifSalahSatu(...$peran)) {
            abort(403, 'Halaman ini tidak tersedia untuk peran Anda.');
        }

        return $next($request);
    }
}
