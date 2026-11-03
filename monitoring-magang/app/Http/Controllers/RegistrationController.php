<?php

namespace App\Http\Controllers;

use App\Services\CisService;
use App\Services\RegistrationService;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    protected RegistrationService $registrationService;

    public function __construct(
        RegistrationService $registrationService
    ) {
        $this->registrationService = $registrationService;
    }

    /**
 * Mengubah status pendaftaran oleh Dosen.
 */
public function updateStatus(Request $request, $id)
{
    $this->ensureDosen();

    $validated = $request->validate([
        'status' => [
            'required',
            'in:diterima,ditolak',
        ],
    ]);

    $result = $this->registrationService->update(
        $id,
        $validated
    );

    if (!($result['success'] ?? false)) {
        return redirect()
            ->route('registration.index')
            ->withErrors([
                'registration' => $result['message']
                    ?? 'Status pendaftaran gagal diperbarui.',
            ]);
    }

    return redirect()
        ->route('registration.index')
        ->with(
            'success',
            $result['message']
                ?? 'Status pendaftaran berhasil diperbarui.'
        );
}

    /**
     * Memastikan pengguna memiliki role Dosen.
     */
    private function ensureDosen(): void
    {
        if ((session('cis_user')['role'] ?? null) !== 'Dosen') {
            abort(
                403,
                'Akses data pendaftaran hanya tersedia untuk Dosen.'
            );
        }
    }

    /**
     * Memastikan pengguna memiliki role Mahasiswa.
     */
    private function ensureMahasiswa(): void
    {
        if ((session('cis_user')['role'] ?? null) !== 'Mahasiswa') {
            abort(
                403,
                'Pendaftaran hanya tersedia untuk Mahasiswa.'
            );
        }
    }

    /**
     * Menampilkan formulir pendaftaran.
     */

/**
 * Menampilkan formulir sesuai jenis PDLK yang dipilih mahasiswa.
 */
public function create(?string $jenis = null)
{
    // Pastikan halaman ini hanya dapat dibuka oleh mahasiswa.
    if ((session('cis_user')['role'] ?? null) !== 'Mahasiswa') {
        abort(403, 'Pendaftaran PDLK hanya tersedia untuk Mahasiswa.');
    }

    // Daftar jenis PDLK yang tersedia.
    $jenisPdlk = [
        'magang' => 'Magang',
        'kerja-praktik' => 'Kerja Praktik',
        'penelitian' => 'Penelitian',
        'pertukaran-pelajar' => 'Pertukaran Pelajar',
        'kkn' => 'Kuliah Kerja Nyata (KKN)',
        'studi-independen' => 'Studi Independen',
        'kewirausahaan' => 'Kewirausahaan',
    ];

    // Jika jenis tidak diberikan, gunakan Magang.
    $jenis = strtolower($jenis ?? 'magang');

    // Tolak jenis PDLK yang tidak terdaftar.
    abort_unless(
        array_key_exists($jenis, $jenisPdlk),
        404,
        'Jenis PDLK tidak ditemukan.'
    );

    // Kirim jenis PDLK ke halaman formulir.
    return view('registration.create', [
        'jenisSlug' => $jenis,
        'jenisPdlk' => $jenisPdlk[$jenis],
    ]);
}


    /**
     * Mengambil seluruh data pendaftaran.
     * Hanya dapat diakses oleh Dosen.
     */
    public function index()
    {
        $this->ensureDosen();

        $registrations = $this->registrationService->getAll();

        return response()->json([
            'success' => true,
            'data' => $registrations,
        ]);
    }

    /**
 * Menampilkan halaman daftar pendaftaran untuk Dosen.
 */
public function listPage()
{
    $this->ensureDosen();

    $registrations = $this->registrationService->getAll();

    return view('registration.index', [
        'registrations' => $registrations,
    ]);
}

    /**
     * Mengambil satu data pendaftaran berdasarkan ID.
     * Hanya dapat diakses oleh Dosen.
     */
    public function show($id)
    {
        $this->ensureDosen();

        $registration = $this->registrationService->getById($id);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Data pendaftaran tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $registration,
        ]);
    }

    /**
     * Menyimpan pendaftaran mahasiswa.
     * NIM diverifikasi melalui CIS oleh RegistrationService.
     */

public function store(
    Request $request,
    CisService $cisService
) {
    // Pendaftaran hanya boleh dilakukan oleh Mahasiswa.
    $cisUser = session('cis_user', []);

    if (($cisUser['role'] ?? null) !== 'Mahasiswa') {
        abort(403, 'Pendaftaran hanya tersedia untuk Mahasiswa.');
    }

    // Validasi formulir pendaftaran.
    $validated = $request->validate([
        'mahasiswa_id' => [
            'required',
            'string',
            'max:50',
        ],
        'jenis_pdlk' => [
            'required',
            'string',
            'in:magang,kerja-praktik,penelitian,pertukaran-pelajar,kkn,studi-independen,kewirausahaan',
        ],
        'tempat_magang' => [
            'required',
            'string',
            'max:200',
        ],
    ]);

    // Verifikasi identitas mahasiswa melalui CIS.
    $student = $cisService->findStudentByNim(
        $validated['mahasiswa_id']
    );

    if (
        !$student ||
        empty($student['nim']) ||
        empty($student['nama']) ||
        empty($student['email'])
    ) {
        return back()
            ->withErrors([
                'registration' =>
                    'NIM tidak ditemukan atau data mahasiswa tidak valid.',
            ])
            ->withInput();
    }

    // Pastikan NIM sesuai dengan akun yang sedang login.
    $loginEmail = strtolower(trim(
        (string) ($cisUser['email'] ?? '')
    ));

    $studentEmail = strtolower(trim(
        (string) $student['email']
    ));

    if (
        $loginEmail === '' ||
        $studentEmail === '' ||
        $loginEmail !== $studentEmail
    ) {
        return back()
            ->withErrors([
                'registration' =>
                    'NIM tidak sesuai dengan akun mahasiswa yang sedang login.',
            ])
            ->withInput();
    }

    // Simpan identitas mahasiswa ke session.
    $cisUser['nim'] = $student['nim'];
    $cisUser['nama'] = $student['nama'];

    session([
        'cis_user' => $cisUser,
    ]);

    // Periksa pendaftaran yang sudah ada.
    $existingRegistration = $this->registrationService
        ->getAll()
        ->first(function ($registration) use ($student) {
            return (string) $registration->mahasiswa_id
                === (string) $student['nim'];
        });

    // Pertahankan aturan sementara: satu NIM hanya satu pendaftaran.
    if ($existingRegistration) {
        return redirect()
            ->route('registration.create')
            ->with(
                'success',
                'Identitas berhasil diverifikasi. Pendaftaran Anda sudah tercatat.'
            );
    }

    // Simpan data yang sudah diverifikasi.
    $result = $this->registrationService->create([
        'mahasiswa_id' => $student['nim'],
        'nama' => $student['nama'],
        'jenis_pdlk' => $validated['jenis_pdlk'],
        'tempat_magang' => $validated['tempat_magang'],
    ]);

    if ($result['success'] ?? false) {
        return redirect()
            ->route('registration.create')
            ->with(
                'success',
                $result['message'] ?? 'Pendaftaran berhasil disimpan.'
            );
    }

    return back()
        ->withErrors([
            'registration' => $result['message']
                ?? 'Pendaftaran gagal disimpan.',
        ])
        ->withInput();
}


    public function update(Request $request, $id)
    {
        $this->ensureDosen();

        $validated = $request->validate([
            'mahasiswa_id' => [
                'sometimes',
                'required',
                'string',
                'max:50',
            ],
            'nama' => [
                'sometimes',
                'required',
                'string',
                'max:150',
            ],
            'tempat_magang' => [
                'sometimes',
                'required',
                'string',
                'max:200',
            ],
            'status' => [
                'sometimes',
                'required',
                'string',
                'max:50',
            ],
        ]);

        $result = $this->registrationService->update(
            $id,
            $validated
        );

        return response()->json(
            $result,
            $result['status'] ?? 200
        );
    }

    /**
     * Menghapus data pendaftaran.
     * Hanya dapat diakses oleh Dosen.
     */
    public function destroy($id)
    {
        $this->ensureDosen();

        $result = $this->registrationService->delete($id);

        return response()->json(
            $result,
            $result['status'] ?? 200
        );
    }
}
