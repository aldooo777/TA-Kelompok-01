<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Services\CisService;
use App\Support\Prodi;
use App\Support\Sql;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Pengguna & peran: Staf UPT memilih peran yang sudah ada (roles)
 * untuk pengguna CIS (users) melalui tabel user_roles.
 * Pengguna yang belum pernah login dapat ditambahkan dari CIS.
 */
class PenggunaController extends Controller
{
    public function index(Request $request, CisService $cis)
    {
        // Pencarian ke CIS (pengguna yang belum pernah login)
        $jenis = $request->query('jenis');
        $kata = trim((string) $request->query('kata'));
        $hasilCis = collect();
        $errorCis = null;

        if (in_array($jenis, ['dosen', 'mahasiswa'], true) && mb_strlen($kata) >= 3) {
            try {
                if ($jenis === 'dosen') {
                    $daftar = $cis->cariDosen($kata);
                    // Jika CIS mengirim satu objek, jadikan array
                    if (isset($daftar['user_id'])) {
                        $daftar = [$daftar];
                    }
                    $hasilCis = collect($daftar)->take(20);
                } else {
                    $hasilCis = collect(array_filter([$cis->findStudentByNim($kata)]));
                }
            } catch (\Throwable $e) {
                report($e);
                $errorCis = 'Server CIS tidak dapat dihubungi. Gunakan jaringan kampus atau VPN.';
            }
        }

        // Daftar pengguna yang sudah tercatat di sistem
        $q = trim((string) $request->query('q'));
        $bind = [];
        $where = '';

        if ($q !== '') {
            $where = 'WHERE LOWER(u.name) LIKE ? OR LOWER(u.username) LIKE ? OR u.nim LIKE ?';
            $bind = ['%' . mb_strtolower($q) . '%', '%' . mb_strtolower($q) . '%', "%$q%"];
        }

        $pengguna = Sql::paginate(
            "SELECT u.*, sp.code AS prodi_code,
                    (SELECT STRING_AGG(r.name || COALESCE(' (' || p2.code || ')', ''), ', ' ORDER BY r.id)
                     FROM user_roles ur
                     JOIN roles r ON r.id = ur.role_id
                     LEFT JOIN study_programs p2 ON p2.id = ur.study_program_id
                     WHERE ur.user_id = u.id AND ur.is_active) AS daftar_peran
             FROM users u
             LEFT JOIN study_programs sp ON sp.id = u.study_program_id
             $where
             ORDER BY u.last_login_at DESC NULLS LAST, u.name",
            $bind, 25
        );

        return view('kelola.pengguna.index', compact('pengguna', 'hasilCis', 'errorCis', 'jenis', 'kata'));
    }

    /**
     * Mendaftarkan dosen/mahasiswa dari CIS tanpa harus login dahulu.
     * Data diambil ulang dari CIS di server (tidak percaya isian form).
     */
    public function tambahDariCis(Request $request, CisService $cis)
    {
        $data = $request->validate([
            'jenis' => ['required', 'in:dosen,mahasiswa'],
            'kata' => ['required', 'string', 'max:100'],
            'id' => ['required', 'string', 'max:30'],
        ]);

        try {
            if ($data['jenis'] === 'mahasiswa') {
                $cisData = $cis->findStudentByNim($data['id']);
            } else {
                $daftar = $cis->cariDosen($data['kata']);
                if (isset($daftar['user_id'])) {
                    $daftar = [$daftar];
                }
                $cisData = collect($daftar)->first(fn ($d) => (string) ($d['user_id'] ?? '') === $data['id']);
            }
        } catch (\Throwable $e) {
            report($e);

            return back()->with('warning', 'Server CIS tidak dapat dihubungi. Coba lagi.');
        }

        if (! $cisData || empty($cisData['email'])) {
            return back()->with('warning', 'Data tidak ditemukan di CIS.');
        }

        $email = strtolower(trim($cisData['email']));
        $cisId = $data['jenis'] === 'dosen' ? (int) $cisData['user_id'] : null;
        $nim = $data['jenis'] === 'mahasiswa' ? (string) $cisData['nim'] : null;

        // Cegah duplikat
        $ada = Sql::one(
            'SELECT id FROM users
             WHERE LOWER(email) = ?
                OR (cis_user_id = ?::bigint AND ?::bigint IS NOT NULL)
                OR (nim = ? AND ?::text IS NOT NULL)
             LIMIT 1',
            [$email, $cisId, $cisId, $nim, $nim]
        );

        if ($ada) {
            return redirect()->route('kelola.pengguna.edit', $ada->id)
                ->with('warning', 'Pengguna ini sudah terdaftar di sistem.');
        }

        // Hanya data yang dibutuhkan PDLK yang disimpan
        $id = Sql::insert('users', [
            'cis_user_id' => $cisId,
            'nim' => $nim,
            'name' => $cisData['nama'] ?? $email,
            'email' => $email,
            'cis_role' => $data['jenis'] === 'dosen' ? 'Dosen' : 'Mahasiswa',
            'study_program_id' => Prodi::dariTeksCis($cisData['prodi'] ?? null),
        ]);

        return redirect()->route('kelola.pengguna.edit', $id)
            ->with('success', 'Pengguna ditambahkan dari CIS. Silakan atur perannya.');
    }

    public function edit(int $id)
    {
        return view('kelola.pengguna.edit', [
            'user' => Sql::oneOrFail('SELECT * FROM users WHERE id = ?', [$id]),
            'peranUser' => Sql::all(
                'SELECT ur.id, ur.is_active, r.name AS peran, r.code, sp.code AS prodi_code, sp.name AS prodi_name
                 FROM user_roles ur
                 JOIN roles r ON r.id = ur.role_id
                 LEFT JOIN study_programs sp ON sp.id = ur.study_program_id
                 WHERE ur.user_id = ?
                 ORDER BY r.id', [$id]
            ),
            'roles' => Sql::all("SELECT id, code, name FROM roles WHERE is_active = true AND code <> 'student' ORDER BY id"),
            'prodiList' => Sql::all('SELECT id, code, level, name FROM study_programs WHERE is_active = true ORDER BY level, name'),
        ]);
    }

    public function tambahPeran(Request $request, int $id)
    {
        Sql::oneOrFail('SELECT id FROM users WHERE id = ?', [$id]);

        $data = $request->validate([
            'role_id' => ['required', Rule::exists('roles', 'id')->where('is_active', true)],
            'study_program_id' => ['nullable', 'exists:study_programs,id'],
        ]);

        $role = Sql::one('SELECT code FROM roles WHERE id = ?', [$data['role_id']]);

        if ($role->code === 'student') {
            return back()->with('warning', 'Peran Mahasiswa dikenali otomatis dari CIS.');
        }
        if ($role->code !== 'upt_staff' && empty($data['study_program_id'])) {
            return back()->withErrors(['study_program_id' => 'Peran koordinator wajib memilih program studi.']);
        }

        $prodi = $role->code === 'upt_staff' ? null : $data['study_program_id'];

        $ada = Sql::one(
            'SELECT id, is_active FROM user_roles WHERE user_id = ? AND role_id = ? AND study_program_id IS NOT DISTINCT FROM ?::bigint',
            [$id, $data['role_id'], $prodi]
        );

        if ($ada) {
            Sql::update('user_roles', $ada->id, ['is_active' => true]);
        } else {
            Sql::insert('user_roles', ['user_id' => $id, 'role_id' => $data['role_id'], 'study_program_id' => $prodi]);
        }

        return back()->with('success', 'Peran ditambahkan. Berlaku setelah pengguna login ulang.');
    }

    public function hapusPeran(int $id, int $userRoleId)
    {
        $ur = Sql::oneOrFail('SELECT id FROM user_roles WHERE id = ? AND user_id = ?', [$userRoleId, $id]);
        Sql::delete('user_roles', $ur->id);

        return back()->with('success', 'Peran dicabut. Berlaku setelah pengguna login ulang.');
    }
}
