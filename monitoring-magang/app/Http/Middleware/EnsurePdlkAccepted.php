<?php

namespace App\Http\Middleware;

use App\Services\CisService;
use App\Services\RegistrationService;
use Closure;
use Illuminate\Http\Request;

class EnsurePdlkAccepted
{
    protected RegistrationService $registrationService;

    protected CisService $cisService;

    public function __construct(
        RegistrationService $registrationService,
        CisService $cisService
    ) {
        $this->registrationService = $registrationService;
        $this->cisService = $cisService;
    }

    public function handle(Request $request, Closure $next)
    {
        // Middleware ini hanya berlaku untuk mahasiswa.
        if (
            (session('cis_user')['role'] ?? null) !== 'Mahasiswa'
        ) {
            abort(403, 'Monitoring hanya tersedia untuk Mahasiswa.');
        }

        $cisUser = session('cis_user', []);

        $loginEmail = strtolower(trim(
            (string) ($cisUser['email'] ?? '')
        ));

        $nim = trim(
            (string) ($cisUser['nim'] ?? '')
        );

        // Ambil data pendaftaran yang sudah tersimpan.
        $registrations = $this->registrationService->getAll();

        /*
         * Login CIS lama tidak selalu menyertakan NIM.
         * Jika NIM belum tersedia di session, cocokkan email
         * akun login dengan identitas mahasiswa dari CIS.
         */
        if ($nim === '' && $loginEmail !== '') {
            $registeredNims = $registrations
                ->pluck('mahasiswa_id')
                ->filter()
                ->unique()
                ->values();

            foreach ($registeredNims as $registeredNim) {
                try {
                    $student = $this->cisService->findStudentByNim(
                        (string) $registeredNim
                    );
                } catch (\Throwable $e) {
                    continue;
                }

                if (!$student) {
                    continue;
                }

                $studentEmail = strtolower(trim(
                    (string) ($student['email'] ?? '')
                ));

                if (
                    $studentEmail !== '' &&
                    $studentEmail === $loginEmail
                ) {
                    $nim = (string) $registeredNim;

                    $cisUser['nim'] = $nim;

                    if (!empty($student['nama'])) {
                        $cisUser['nama'] = $student['nama'];
                    }

                    session([
                        'cis_user' => $cisUser,
                    ]);

                    break;
                }
            }
        }

        // Identitas belum berhasil diverifikasi.
        if ($nim === '') {
            return redirect()
                ->route('pdlk.index')
                ->with(
                    'warning',
                    'Pendaftaran PDLK Anda belum dapat diverifikasi. Jika belum mendaftar, silakan pilih jenis kegiatan melalui menu Daftar PDLK terlebih dahulu. Jika sudah mendaftar, pastikan data mahasiswa Anda sesuai.'
                );
        }


/*
|--------------------------------------------------------------------------
| Ambil identitas lengkap mahasiswa dari CIS
|--------------------------------------------------------------------------
*/

try {
    $student = $this->cisService->findStudentByNim($nim);

    if (is_array($student)) {
        $cisUser['nim'] = (string) (
            $student['nim'] ?? $nim
        );

        $cisUser['nama'] = $student['nama']
            ?? ($cisUser['nama'] ?? '');

        $cisUser['email'] = $student['email']
            ?? ($cisUser['email'] ?? '');

        $cisUser['prodi'] = $student['prodi']
            ?? ($cisUser['prodi'] ?? '');

        session([
            'cis_user' => $cisUser,
        ]);
    }
} catch (\Throwable $e) {
    // Jika informasi tambahan dari CIS gagal diperoleh,
    // pemeriksaan status pendaftaran tetap dilanjutkan.
}

        // Cari pendaftaran terbaru milik mahasiswa tersebut.
        $latestRegistration = $registrations->first(
            function ($registration) use ($nim) {
                return (string) $registration->mahasiswa_id
                    === $nim;
            }
        );

        // Mahasiswa harus mendaftar sebelum melakukan monitoring.
        if (!$latestRegistration) {
            return redirect()
                ->route('pdlk.index')
                ->with(
                    'warning',
                    'Silakan daftarkan kegiatan PDLK terlebih dahulu sebelum melakukan monitoring.'
                );
        }

        $status = strtolower(
            (string) $latestRegistration->status
        );

        // Hanya pendaftaran berstatus diterima yang boleh lanjut.
        if ($status !== 'diterima') {
            return redirect()
                ->route('pdlk.index')
                ->with(
                    'warning',
                    'Monitoring belum dapat dilakukan karena pendaftaran PDLK Anda belum berstatus Diterima.'
                );
        }

        return $next($request);
    }
}
