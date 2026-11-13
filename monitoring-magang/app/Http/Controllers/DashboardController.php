<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use App\Models\Pendaftaran;
use App\Models\PesertaPdlk;
use App\Models\Postingan;
use App\Models\Role;
use App\Support\NimMahasiswa;
use App\Support\Peran;

class DashboardController extends Controller
{
    public function __invoke(NimMahasiswa $nimMahasiswa)
    {
        return match (Peran::aktif()) {
            Role::MAHASISWA => $this->mahasiswa($nimMahasiswa),
            Role::STAF_UPT, Role::KOORDINATOR_PDLK_PRODI, Role::KOORDINATOR_PRODI => $this->pengelola(),
            default => view('dashboard.tanpa-peran'),
        };
    }

    private function mahasiswa(NimMahasiswa $nimMahasiswa)
    {
        $user = Peran::user();
        $nim = $nimMahasiswa->sesiIni();

        $pendaftaranTerbaru = Pendaftaran::with('lowongan.mitra')
            ->where(function ($q) use ($user, $nim) {
                $q->where('user_id', $user?->id ?? 0);
                if ($nim) {
                    $q->orWhere('nim', $nim);
                }
            })
            ->latest()
            ->first();

        $pesertaAktif = $nim
            ? PesertaPdlk::with(['mitra', 'jenisKegiatan'])->where('nim', $nim)
                ->orderByRaw("CASE status WHEN 'aktif' THEN 0 WHEN 'selesai' THEN 1 ELSE 2 END")
                ->latest()
                ->first()
            : null;

        // Login  lama tidak mengirim nama; gunakan nama dari data peserta.
        $cis = session('cis_user', []);
        $nama = $pesertaAktif?->nama ?? $pendaftaranTerbaru?->nama;

        if (empty($cis['nama']) && $nama) {
            $cis['nama'] = $nama;
            session(['cis_user' => $cis]);

            if ($user && $user->name !== $nama) {
                $user->forceFill(['name' => $nama])->save();
            }
        }

        return view('dashboard.mahasiswa', compact('pendaftaranTerbaru', 'pesertaAktif'));
    }

    private function pengelola()
    {
        $prodi = Peran::cakupanProdi();

        $peserta = PesertaPdlk::query()->when($prodi !== null, fn ($q) => $q->whereIn('program_studi_id', $prodi));

        $statistikPeserta = [
            PesertaPdlk::AKTIF => (clone $peserta)->where('status', PesertaPdlk::AKTIF)->count(),
            PesertaPdlk::SELESAI => (clone $peserta)->where('status', PesertaPdlk::SELESAI)->count(),
            PesertaPdlk::TIDAK_AKTIF => (clone $peserta)->where('status', PesertaPdlk::TIDAK_AKTIF)->count(),
        ];

        $statistikPendaftaran = Peran::aktif() === Role::STAF_UPT
            ? Pendaftaran::selectRaw('status, COUNT(*) as jumlah')->groupBy('status')->pluck('jumlah', 'status')
            : null;

        return view('dashboard.pengelola', [
            'statistikPeserta' => $statistikPeserta,
            'statistikPendaftaran' => $statistikPendaftaran,
            'jumlahLowonganDibuka' => Lowongan::terbit()->get()->filter->dibuka->count(),
            'jumlahPostingan' => Postingan::where('is_published', true)->count(),
        ]);
    }
}
