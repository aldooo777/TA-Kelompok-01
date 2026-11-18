<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Support\Label;
use App\Support\NimMahasiswa;
use App\Support\Query;
use App\Support\Sql;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Mahasiswa aktif memperbarui progres pelaksanaan:
 * stage (onboarding / ongoing), work_mode (onsite / online / remote), location.
 */
class ProgresController extends Controller
{
    public function edit(NimMahasiswa $nimMahasiswa)
    {
        return view('mahasiswa.progres', ['peserta' => $this->pesertaAktif($nimMahasiswa)]);
    }

    public function update(Request $request, NimMahasiswa $nimMahasiswa)
    {
        $peserta = $this->pesertaAktif($nimMahasiswa);

        $data = $request->validate([
            'stage' => ['required', Rule::in(array_keys(Label::STAGE))],
            'work_mode' => ['required', Rule::in(array_keys(Label::WORK_MODE))],
            'location' => ['required', 'string', 'max:200'],
        ], [], ['stage' => 'tahap', 'work_mode' => 'mode kerja', 'location' => 'lokasi']);

        Sql::update('pdlk_participants', $peserta->id, $data);

        return redirect()->route('dashboard')->with('success', 'Progres pelaksanaan berhasil diperbarui.');
    }

    private function pesertaAktif(NimMahasiswa $nimMahasiswa): object
    {
        $nim = $nimMahasiswa->sesiIni();

        return Sql::one(Query::PESERTA . " WHERE pp.nim = ? AND pp.status = 'active' AND pp.is_active ORDER BY pp.created_at DESC LIMIT 1", [$nim ?? ''])
            ?? abort(403, 'Progres hanya dapat diperbarui oleh mahasiswa aktif PDLK.');
    }
}
