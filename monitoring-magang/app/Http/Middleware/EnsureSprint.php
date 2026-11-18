<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** Halaman milik sprint yang belum aktif (config pdlk.sprint) dianggap tidak ada. */
class EnsureSprint
{
    public function handle(Request $request, Closure $next, string $sprint)
    {
        abort_unless(sprint((int) $sprint), 404);

        return $next($request);
    }
}
