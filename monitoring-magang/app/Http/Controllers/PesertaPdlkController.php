<?php

namespace App\Http\Controllers;

use App\Models\JenisKegiatan;
use App\Models\PesertaPdlk;
use App\Models\ProgramStudi;
use App\Models\Role;
use App\Support\Peran;
use Illuminate\Http\Request;

/**
 * Daftar mahasiswa aktif PDLK untuk Staf UPT dan koordinator.
 * Koordinator hanya melihat mahasiswa di program studinya.
 *
 * Tidak ada fitur "batal magang". Untuk kondisi force majeure
 * (mis. sakit) Staf UPT mengubah status menjadi Tidak Aktif.
 */
class PesertaPdlkController extends Controller
{
    public function index(Request $request)
    {
        $cakupan = Peran::cakupanProdi();
        $q = trim((string) $request->query('q'));

        $peserta = PesertaPdlk::with(['programStudi', 'jenisKegiatan', 'mitra'])
            ->when($cakupan !== null, fn ($w) => $w->whereIn('program_studi_id', $cakupan))
            ->when($request->query('prodi'), fn ($w, $id) => $w->where('program_studi_id', $id))
            ->when($request->query('jenis'), fn ($w, $id) => $w->where('jenis_kegiatan_id', $id))
            ->when($request->query('status'), fn ($w, $s) => $w->where('status', $s))
            ->when($q !== '', fn ($w) => $w->where(function ($x) use ($q) {
                $x->whereRaw('LOWER(nama) LIKE ?', ['%' . mb_strtolower($q) . '%'])->orWhere('nim', 'like', "%{$q}%");
            }))
            ->orderBy('nama')
            ->paginate(25)
            ->withQueryString();

        return view('peserta-pdlk.index', [
            'peserta' => $peserta,
            'prodiList' => ProgramStudi::query()
                ->when($cakupan !== null, fn ($w) => $w->whereIn('id', $cakupan))
                ->orderBy('jenjang')->orderBy('nama')->get(),
            'jenisList' => JenisKegiatan::orderBy('urutan')->get(),
            'bolehUbahStatus' => Peran::aktif() === Role::STAF_UPT,
        ]);
    }

    public function nonaktifkan(Request $request, PesertaPdlk $peserta)
    {
        $data = $request->validate([
            'alasan' => ['required', 'string', 'min:5', 'max:1000'],
        ], ['alasan.required' => 'Alasan force majeure wajib diisi.']);

        if ($peserta->status !== PesertaPdlk::AKTIF) {
            return back()->with('warning', 'Hanya mahasiswa berstatus Aktif yang dapat diubah menjadi Tidak Aktif.');
        }

        $peserta->ubahStatus(PesertaPdlk::TIDAK_AKTIF, $data['alasan'], Peran::userId());

        return back()->with('success', "{$peserta->nama} sekarang berstatus Tidak Aktif.");
    }
}
