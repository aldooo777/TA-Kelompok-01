<?php

namespace App\Http\Middleware;

use App\Models\PesertaPdlk;
use App\Models\Role;
use App\Support\NimMahasiswa;
use App\Support\Peran;
use Closure;
use Illuminate\Http\Request;

/**
 * Monitoring hanya untuk mahasiswa yang tercatat sebagai
 * mahasiswa AKTIF PDLK (lulus seleksi atau didata sebagai PDLK mandiri).
 */
class EnsurePdlkAccepted
{
    public function __construct(private NimMahasiswa $nimMahasiswa)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        if (Peran::aktif() !== Role::MAHASISWA) {
            abort(403, 'Monitoring hanya tersedia untuk Mahasiswa.');
        }

        $nim = $this->nimMahasiswa->sesiIni();

        $peserta = $nim
            ? PesertaPdlk::with('programStudi')->where('nim', $nim)->where('status', PesertaPdlk::AKTIF)->latest()->first()
            : null;

        if (!$peserta) {
            return redirect()
                ->route('dashboard')
                ->with('warning', 'Monitoring hanya tersedia bagi mahasiswa aktif PDLK. Anda tercatat aktif setelah lulus seleksi atau didata oleh koordinator PDLK prodi / UPT PP-ESTEM.');
        }

        // Isian identitas pada form monitoring.
        $cis = session('cis_user', []);
        if (empty($cis['prodi']) && $peserta->programStudi) {
            $cis['prodi'] = $peserta->programStudi->label;
            session(['cis_user' => $cis]);
        }

        return $next($request);
    }
}
