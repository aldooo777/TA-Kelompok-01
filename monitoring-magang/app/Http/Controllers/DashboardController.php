<?php

namespace App\Http\Controllers;

use App\Support\NimMahasiswa;
use App\Support\Peran;
use App\Support\Query;
use App\Support\Sql;

class DashboardController extends Controller
{
    public function __invoke(NimMahasiswa $nimMahasiswa)
    {
        return match (Peran::aktif()) {
            'student' => $this->mahasiswa($nimMahasiswa),
            'upt_staff', 'pdlk_coordinator', 'program_head' => $this->pengelola(),
            default => view('dashboard.tanpa-peran'),
        };
    }

    private function mahasiswa(NimMahasiswa $nimMahasiswa)
    {
        $user = Peran::user();
        $nim = $nimMahasiswa->sesiIni();

        $pendaftaranTerbaru = Sql::one(
            Query::PENDAFTARAN . ' WHERE ap.user_id = ? OR ap.nim = ? ORDER BY ap.created_at DESC LIMIT 1',
            [$user?->id ?? 0, $nim ?? '']
        );

        $pesertaAktif = $nim ? Sql::one(
            Query::PESERTA . " WHERE pp.nim = ? AND pp.is_active
             ORDER BY CASE pp.status WHEN 'active' THEN 0 WHEN 'completed' THEN 1 ELSE 2 END, pp.created_at DESC LIMIT 1",
            [$nim]
        ) : null;

        // Login lama tidak mengirim nama; gunakan nama dari data peserta.
        $cis = session('cis_user', []);
        $nama = $pesertaAktif?->name ?? $pendaftaranTerbaru?->name;

        if (empty($cis['nama']) && $nama) {
            $cis['nama'] = $nama;
            session(['cis_user' => $cis]);
            if ($user && $user->name !== $nama) {
                Sql::update('users', $user->id, ['name' => $nama]);
            }
        }

        $monev = $pesertaAktif && $pesertaAktif->status !== 'inactive'
            ? \App\Support\PesertaSaya::jadwalMonev($pesertaAktif)
            : collect();

        return view('dashboard.mahasiswa', compact('pendaftaranTerbaru', 'pesertaAktif', 'monev'));
    }

    private function pengelola()
    {
        $prodi = Peran::cakupanProdi();
        $batas = $prodi !== null ? ' AND study_program_id IN (' . (implode(',', array_map('intval', $prodi)) ?: '0') . ')' : '';

        $statistikPeserta = Sql::all("SELECT status, COUNT(*) AS jumlah FROM pdlk_participants WHERE is_active $batas GROUP BY status")
            ->pluck('jumlah', 'status');

        $staf = Peran::aktif() === 'upt_staff';

        return view('dashboard.pengelola', [
            'statistikPeserta' => $statistikPeserta,
            'statistikPendaftaran' => $staf
                ? Sql::all('SELECT status, COUNT(*) AS jumlah FROM applications WHERE is_active GROUP BY status')->pluck('jumlah', 'status')
                : null,
            'jumlahLowonganDibuka' => (int) Sql::value(
                'SELECT COUNT(*) FROM vacancy_details v JOIN announcements a ON a.id = v.announcement_id WHERE ' . Query::LOWONGAN_TERBIT . '
                 AND v.is_closed = false
                 AND (v.registration_start_date IS NULL OR v.registration_start_date <= CURRENT_DATE)
                 AND (v.registration_deadline IS NULL OR v.registration_deadline >= CURRENT_DATE)'
            ),
            'jumlahBerita' => (int) Sql::value('SELECT COUNT(*) FROM announcements a WHERE ' . Query::BERITA_TERBIT),
        ]);
    }
}
