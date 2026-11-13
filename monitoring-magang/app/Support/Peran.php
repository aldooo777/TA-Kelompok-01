<?php

namespace App\Support;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;

/**
 * Pengelolaan peran pengguna PDLK.
 *
 * Login tetap melalui CIS. Peran ditentukan sebagai berikut:
 *  - Mahasiswa              : role CIS = "Mahasiswa"
 *  - Staf UPT PP-ESTEM      : diberikan lewat tabel user_roles
 *  - Koordinator PDLK Prodi : diberikan lewat user_roles (dengan prodi)
 *  - Koordinator Prodi      : diberikan lewat user_roles (dengan prodi)
 *  - Guest                  : pengunjung yang belum login
 *
 * Satu akun dapat memiliki beberapa peran; pengguna memilih peran aktif.
 */
class Peran
{
    /** Urutan prioritas peran aktif bawaan. */
    public const PRIORITAS = [
        Role::STAF_UPT,
        Role::KOORDINATOR_PDLK_PRODI,
        Role::KOORDINATOR_PRODI,
        Role::MAHASISWA,
    ];

    public const LABEL = [
        Role::MAHASISWA => 'Mahasiswa',
        Role::STAF_UPT => 'Staf UPT PP-ESTEM',
        Role::KOORDINATOR_PDLK_PRODI => 'Koordinator PDLK Prodi',
        Role::KOORDINATOR_PRODI => 'Koordinator Program Studi',
    ];

    /**
     * Simpan / perbarui identitas CIS ke tabel users.
     * Password CIS tidak pernah disimpan.
     */
    public static function sinkronDariCis(array $cisUser): User
    {
        $username = (string) ($cisUser['username'] ?? '');
        $email = strtolower(trim((string) ($cisUser['email'] ?? '')));

        $user = User::query()
            ->when(
                !empty($cisUser['user_id']),
                fn ($q) => $q->where('cis_user_id', $cisUser['user_id'])->orWhere('username', $username),
                fn ($q) => $q->where('username', $username)
            )
            ->first() ?? new User();

        $user->fill([
            'cis_user_id' => $cisUser['user_id'] ?? $user->cis_user_id,
            'username' => $username,
            'name' => $cisUser['nama'] ?? $cisUser['name'] ?? $user->name ?? $username,
            'email' => $email !== '' ? $email : ($user->email ?: $username . '@cis.local'),
            'cis_role' => $cisUser['role'] ?? null,
            'cis_jabatan' => isset($cisUser['jabatan'])
                ? (array) $cisUser['jabatan']
                : $user->cis_jabatan,
            'last_login_at' => now(),
        ]);

        $user->save();

        return $user;
    }

    /**
     * Hitung seluruh peran pengguna lalu simpan ke session.
     */
    public static function masuk(User $user): void
    {
        $peran = [];

        if (strcasecmp((string) $user->cis_role, 'Mahasiswa') === 0) {
            $peran[Role::MAHASISWA] = [];
        }

        UserRole::with('role')
            ->where('user_id', $user->id)
            ->get()
            ->each(function (UserRole $ur) use (&$peran) {
                $kode = $ur->role->kode;
                $peran[$kode] = $peran[$kode] ?? [];

                // null = berlaku untuk seluruh prodi
                $peran[$kode][] = $ur->program_studi_id;
            });

        $aktif = collect(self::PRIORITAS)->first(fn ($k) => array_key_exists($k, $peran));

        session([
            'pdlk_user_id' => $user->id,
            'pdlk_peran' => $peran,
            'peran_aktif' => $aktif,
        ]);

        // Simpan NIM mahasiswa yang sudah pernah terverifikasi.
        if ($user->nim) {
            $cis = session('cis_user', []);
            $cis['nim'] = $user->nim;
            session(['cis_user' => $cis]);
        }
    }

    public static function aktif(): ?string
    {
        return session('peran_aktif');
    }

    public static function label(?string $kode = null): string
    {
        $kode ??= self::aktif();

        return self::LABEL[$kode] ?? (session('cis_user')['role'] ?? 'Pengguna');
    }

    /** @return array<string, array<int|null>> */
    public static function semua(): array
    {
        return session('pdlk_peran', []);
    }

    public static function punya(string ...$kode): bool
    {
        return count(array_intersect($kode, array_keys(self::semua()))) > 0;
    }

    public static function aktifSalahSatu(string ...$kode): bool
    {
        return in_array(self::aktif(), $kode, true);
    }

    public static function gantiAktif(string $kode): bool
    {
        if (!self::punya($kode)) {
            return false;
        }

        session(['peran_aktif' => $kode]);

        return true;
    }

    public static function userId(): ?int
    {
        return session('pdlk_user_id');
    }

    public static function user(): ?User
    {
        $id = self::userId();

        return $id ? User::find($id) : null;
    }

    /**
     * ID prodi yang boleh dilihat oleh peran aktif.
     * Mengembalikan null bila tidak dibatasi (Staf UPT / cakupan institusi).
     *
     * @return array<int>|null
     */
    public static function cakupanProdi(): ?array
    {
        $kode = self::aktif();

        if ($kode === Role::STAF_UPT) {
            return null;
        }

        $ids = self::semua()[$kode] ?? [];

        if (in_array(null, $ids, true)) {
            return null;
        }

        return array_values(array_filter($ids));
    }
}
