<?php

namespace App\Http\Middleware;

use App\Support\NimMahasiswa;
use App\Support\Peran;
use App\Support\Query;
use App\Support\Sql;
use Closure;
use Illuminate\Http\Request;

/**
 * Monitoring hanya untuk mahasiswa yang tercatat sebagai mahasiswa
 * AKTIF PDLK (lulus pendaftaran atau diinput manual).
 */
class EnsurePdlkAccepted
{
    public function __construct(private NimMahasiswa $nimMahasiswa)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        if (Peran::aktif() !== 'student') {
            abort(403, 'Monitoring hanya tersedia untuk Mahasiswa.');
        }

        $nim = $this->nimMahasiswa->sesiIni();

        // Mahasiswa berstatus Selesai tetap dapat melihat monev yang sudah dikirim.
        $peserta = \App\Support\PesertaSaya::untukNim($nim, true);

        if (!$peserta) {
            return redirect()
                ->route('dashboard')
                ->with('warning', 'Monitoring hanya tersedia bagi mahasiswa aktif PDLK. Anda tercatat aktif setelah lulus seleksi atau didata oleh koordinator PDLK prodi / UPT PP-ESTEM.');
        }

        $cis = session('cis_user', []);
        if (empty($cis['prodi']) && $peserta->prodi_name) {
            $cis['prodi'] = trim($peserta->prodi_level . ' ' . $peserta->prodi_name);
            session(['cis_user' => $cis]);
        }

        return $next($request);
    }
}
