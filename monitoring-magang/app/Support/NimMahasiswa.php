<?php

namespace App\Support;

use App\Models\PesertaPdlk;
use App\Models\User;
use App\Services\CisService;

/**
 * Menentukan NIM mahasiswa yang sedang login.
 *
 * Login CIS lama tidak selalu mengirim NIM, sehingga NIM diverifikasi
 * dengan mencocokkan email akun login dengan data mahasiswa di CIS.
 * Setelah terverifikasi, NIM disimpan di tabel users agar pencarian
 * ke CIS tidak perlu diulang.
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
            $this->lengkapiProdiSekali(Peran::user(), $nim);

            return $nim;
        }

        $user = Peran::user();

        if ($user?->nim) {
            $this->simpan($user, $user->nim, null);
            session(['prodi_dicek' => true]);

            return $user->nim;
        }

        // Coba cocokkan dengan NIM mahasiswa aktif yang diinput UPT/koordinator.
        $email = $this->emailLogin();

        if ($email === '' || !$user) {
            return null;
        }

        $kandidat = PesertaPdlk::query()
            ->whereNull('user_id')
            ->pluck('nim')
            ->unique()
            ->take(200);

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
     * Data lama / input manual bisa belum memiliki prodi sehingga tidak
     * tampil di halaman koordinator. Lengkapi dari data mahasiswa di CIS.
     */
    public function lengkapiProdi(?User $user, string $nim): void
    {
        $perluDiisi = PesertaPdlk::where('nim', $nim)->whereNull('program_studi_id')->exists();

        if (!$perluDiisi && $user?->program_studi_id) {
            return;
        }

        $prodiId = $user?->program_studi_id;

        if (!$prodiId) {
            try {
                $mhs = $this->cis->findStudentByNim($nim);
            } catch (\Throwable $e) {
                return;
            }

            $prodiId = \App\Models\ProgramStudi::dariTeksCis($mhs['prodi'] ?? null)?->id;
        }

        if (!$prodiId) {
            return;
        }

        PesertaPdlk::where('nim', $nim)->whereNull('program_studi_id')->update(['program_studi_id' => $prodiId]);

        if ($user && !$user->program_studi_id) {
            $user->forceFill(['program_studi_id' => $prodiId])->save();
        }
    }

    /** Cukup sekali per sesi agar tidak memanggil CIS di setiap halaman. */
    private function lengkapiProdiSekali(?User $user, string $nim): void
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

    private function simpan(User $user, string $nim, ?string $nama): void
    {
        if ($user->nim !== $nim || ($nama && $user->name !== $nama)) {
            $user->forceFill(['nim' => $nim] + ($nama ? ['name' => $nama] : []))->save();
        }

        PesertaPdlk::where('nim', $nim)->whereNull('user_id')->update(['user_id' => $user->id]);

        $this->lengkapiProdi($user, $nim);

        $cis = session('cis_user', []);
        $cis['nim'] = $nim;
        if ($nama) {
            $cis['nama'] = $nama;
        }
        session(['cis_user' => $cis]);
    }
}
