<?php

namespace App\Support;


/**
 * Pengelolaan peran pengguna PDLK.
 *
 * Login tetap melalui CIS. Peran ditentukan sebagai berikut:
 *  - student          : role CIS = "Mahasiswa" (tidak disimpan di user_roles)
 *  - upt_staff        : dari tabel user_roles
 *  - pdlk_coordinator : dari user_roles (dengan study_program_id)
 *  - program_head     : dari user_roles (dengan study_program_id)
 *  - Guest            : pengunjung yang belum login
 *
 * Satu akun dapat memiliki beberapa peran; pengguna memilih peran aktif.
 */
class Peran
{
    /** Urutan prioritas peran aktif bawaan. */
    public const PRIORITAS = [
        'upt_staff',
        'pdlk_coordinator',
        'program_head',
        'student',
    ];

    public const LABEL = [
        'student' => 'Mahasiswa',
        'upt_staff' => 'Staf UPT PP-ESTEM',
        'pdlk_coordinator' => 'Koordinator PDLK Prodi',
        'program_head' => 'Koordinator Program Studi',
    ];

    /**
     * Simpan / perbarui identitas CIS ke tabel users. Password CIS tidak disimpan.
     * Mengembalikan id users.
     */
    public static function sinkronDariCis(array $cisUser): int
    {
        $username = (string) ($cisUser['username'] ?? '');
        $email = strtolower(trim((string) ($cisUser['email'] ?? ''))) ?: null;
        $cisId = $cisUser['user_id'] ?? null;

        $user = Sql::one(
            'SELECT id, name, email, cis_position FROM users
             WHERE (cis_user_id = ?::bigint AND ?::bigint IS NOT NULL)
                OR username = ?
                OR LOWER(email) = ?
             LIMIT 1',
            [$cisId, $cisId, $username, $email]
        );

        $data = [
            'cis_user_id' => $cisId,
            'username' => $username,
            'name' => $cisUser['nama'] ?? $cisUser['name'] ?? $user?->name ?? $username,
            'email' => $email ?? $user?->email,
            'cis_role' => $cisUser['role'] ?? null,
            'cis_position' => isset($cisUser['jabatan']) ? (array) $cisUser['jabatan'] : json_decode((string) $user?->cis_position, true),
            'last_login_at' => now(),
        ];

        if ($user) {
            Sql::update('users', $user->id, $data);

            return (int) $user->id;
        }

        return Sql::insert('users', $data);
    }

    /** Hitung seluruh peran pengguna lalu simpan ke session. */
    public static function masuk(int $userId): void
    {
        $user = Sql::one('SELECT id, nim, cis_role FROM users WHERE id = ?', [$userId]);
        $peran = [];

        if (strcasecmp((string) $user->cis_role, 'Mahasiswa') === 0) {
            $peran['student'] = [];
        }

        $baris = Sql::all(
            'SELECT r.code, ur.study_program_id
             FROM user_roles ur
             JOIN roles r ON r.id = ur.role_id
             WHERE ur.user_id = ? AND ur.is_active = true AND r.is_active = true',
            [$userId]
        );

        foreach ($baris as $b) {
            $peran[$b->code] = $peran[$b->code] ?? [];
            // null = berlaku untuk seluruh prodi
            $peran[$b->code][] = $b->study_program_id === null ? null : (int) $b->study_program_id;
        }

        $aktif = collect(self::PRIORITAS)->first(fn ($k) => array_key_exists($k, $peran));

        session([
            'pdlk_user_id' => (int) $user->id,
            'pdlk_peran' => $peran,
            'peran_aktif' => $aktif,
        ]);

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

    public static function user(): ?object
    {
        $id = self::userId();

        return $id ? Sql::one('SELECT * FROM users WHERE id = ?', [$id]) : null;
    }

    /**
     * ID prodi yang boleh dilihat oleh peran aktif.
     * null = tidak dibatasi (Staf UPT / cakupan institusi).
     *
     * @return array<int>|null
     */
    public static function cakupanProdi(): ?array
    {
        $kode = self::aktif();

        if ($kode === 'upt_staff') {
            return null;
        }

        $ids = self::semua()[$kode] ?? [];

        if (in_array(null, $ids, true)) {
            return null;
        }

        return array_values(array_filter($ids));
    }
}
