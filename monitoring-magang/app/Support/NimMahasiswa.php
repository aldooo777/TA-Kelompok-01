<?php

namespace App\Support;

use App\Services\CisService;

/**
 * Menentukan NIM mahasiswa yang sedang login.
 *
 * Login CIS lama tidak selalu mengirim NIM, sehingga NIM diverifikasi
 * dengan mencocokkan email akun login dengan data mahasiswa di CIS.
 * Setelah terverifikasi, NIM disimpan di tabel users.
 */
class NimMahasiswa
{
    public function __construct(private CisService $cis)
    {
    }

    public function sesiIni(): ?string
    {
        $nim = trim((string) (session('cis_user')['nim'] ?? ''));

        if ($nim !== '') {
            $this->lengkapiSekali(Peran::user(), $nim);

            return $nim;
        }

        $user = Peran::user();

        if ($user?->nim) {
            $this->simpan($user, $user->nim, null);
            session(['prodi_dicek' => true]);

            return $user->nim;
        }

        // Cocokkan dengan NIM mahasiswa aktif yang diinput manual oleh UPT/koordinator.
        $email = $this->emailLogin();

        if ($email === '' || !$user) {
            return null;
        }

        $kandidat = Sql::all('SELECT DISTINCT nim FROM pdlk_participants WHERE user_id IS NULL LIMIT 200')->pluck('nim');

        foreach ($kandidat as $nimKandidat) {
            try {
                $mhs = $this->cis->findStudentByNim((string) $nimKandidat);
            } catch (\Throwable $e) {
                continue;
            }

            if ($mhs && strtolower(trim((string) ($mhs['email'] ?? ''))) === $email) {
                $this->simpan($user, (string) $nimKandidat, $mhs['nama'] ?? null);

                return (string) $nimKandidat;
            }
        }

        return null;
    }

    /**
     * Verifikasi NIM yang diketik mahasiswa terhadap akun login.
     *
     * @return array{ok: bool, pesan?: string, mahasiswa?: array}
     */
    public function verifikasi(string $nim): array
    {
        try {
            $mhs = $this->cis->findStudentByNim($nim);
        } catch (\Throwable $e) {
            report($e);

            return ['ok' => false, 'pesan' => 'Data mahasiswa di CIS tidak dapat diakses. Coba lagi beberapa saat.'];
        }

        if (!$mhs || empty($mhs['email'])) {
            return ['ok' => false, 'pesan' => 'NIM tidak ditemukan di CIS.'];
        }

        if (strtolower(trim((string) $mhs['email'])) !== $this->emailLogin()) {
            return ['ok' => false, 'pesan' => 'NIM tidak sesuai dengan akun mahasiswa yang sedang login.'];
        }

        if ($user = Peran::user()) {
            $this->simpan($user, (string) $mhs['nim'], $mhs['nama'] ?? null);
        }

        return ['ok' => true, 'mahasiswa' => $mhs];
    }

    /**
     * Data input manual bisa belum memiliki prodi sehingga tidak tampil
     * di halaman koordinator. Lengkapi dari data mahasiswa di CIS.
     */
    public function lengkapiProdi(?object $user, string $nim): void
    {
        $perluDiisi = (bool) Sql::value(
            'SELECT EXISTS (SELECT 1 FROM pdlk_participants WHERE nim = ? AND study_program_id IS NULL)', [$nim]
        );

        if (!$perluDiisi && $user?->study_program_id) {
            return;
        }

        $prodiId = $user?->study_program_id;

        if (!$prodiId) {
            try {
                $mhs = $this->cis->findStudentByNim($nim);
            } catch (\Throwable $e) {
                return;
            }

            $prodiId = Prodi::dariTeksCis($mhs['prodi'] ?? null);
        }

        if (!$prodiId) {
            return;
        }

        \DB::update('UPDATE pdlk_participants SET study_program_id = ? WHERE nim = ? AND study_program_id IS NULL', [$prodiId, $nim]);

        if ($user && !$user->study_program_id) {
            Sql::update('users', $user->id, ['study_program_id' => $prodiId]);
        }
    }

    /** Cukup sekali per sesi agar tidak memanggil CIS di setiap halaman. */
    private function lengkapiSekali(?object $user, string $nim): void
    {
        if (session('prodi_dicek')) {
            return;
        }

        session(['prodi_dicek' => true]);
        $this->lengkapiProdi($user, $nim);
    }

    private function emailLogin(): string
    {
        return strtolower(trim((string) (session('cis_user')['email'] ?? '')));
    }

    private function simpan(object $user, string $nim, ?string $nama): void
    {
        if ($user->nim !== $nim || ($nama && $user->name !== $nama)) {
            Sql::update('users', $user->id, ['nim' => $nim] + ($nama ? ['name' => $nama] : []));
        }

        \DB::update('UPDATE pdlk_participants SET user_id = ? WHERE nim = ? AND user_id IS NULL', [$user->id, $nim]);

        $this->lengkapiProdi(Peran::user() ?? $user, $nim);

        $cis = session('cis_user', []);
        $cis['nim'] = $nim;
        if ($nama) {
            $cis['nama'] = $nama;
        }
        session(['cis_user' => $cis]);
    }
}
