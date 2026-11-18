<?php

namespace App\Http\Middleware;

use App\Support\NimMahasiswa;
use App\Support\Peran;
use App\Support\PesertaSaya;
use Closure;
use Illuminate\Http\Request;

/**
 * Seperti kuesioner di CIS: selama ada monev yang sudah dibuka dan belum
 * dikirim, halaman mahasiswa (dashboard dll.) terkunci dan diarahkan ke
 * formulir monev.
 */
class MonevWajib
{
    public function __construct(private NimMahasiswa $nimMahasiswa)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        if (sprint(3) && Peran::aktif() === 'student') {
            $peserta = PesertaSaya::untukNim($this->nimMahasiswa->sesiIni());
            $wajib = $peserta ? PesertaSaya::monevWajib($peserta) : null;

            if ($wajib) {
                $label = $wajib->type === 'mid' ? 'Monev Tengah' : 'Monev Akhir';

                return redirect()->route('monitoring.isi', $wajib->id)
                    ->with('warning', "Dashboard terkunci. Isi dan kirim {$label} terlebih dahulu (tenggat " . tgl($wajib->deadline_at, 'd F Y H:i') . ').');
            }
        }

        return $next($request);
    }
}
