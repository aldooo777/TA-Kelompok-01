
<?php

use Illuminate\Support\Facades\Route;
use App\Services\RegistrationService;
use App\Services\CisService;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrationController;

// Halaman utama langsung mengarah ke Login.
Route::redirect('/', '/login');

/*
|--------------------------------------------------------------------------
| Halaman Login
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.process');


/*
|--------------------------------------------------------------------------
| Dashboard berdasarkan Role
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function (
    RegistrationService $registrationService,
    CisService $cisService
) {
    $role = session('cis_user')['role'] ?? null;

    /*
    |--------------------------------------------------------------------------
    | Dashboard Mahasiswa
    |--------------------------------------------------------------------------
    */

    if ($role === 'Mahasiswa') {
        $cisUser = session('cis_user', []);

        $loginEmail = strtolower(trim(
            (string) ($cisUser['email'] ?? '')
        ));

        $registrations = $registrationService->getAll();
        $latestRegistration = null;

        // Cari pendaftaran mahasiswa berdasarkan email CIS.
        if ($loginEmail !== '') {
            $registeredNims = $registrations
                ->pluck('mahasiswa_id')
                ->filter()
                ->unique()
                ->values();

            foreach ($registeredNims as $registeredNim) {
                try {
                    $student = $cisService->findStudentByNim(
                        (string) $registeredNim
                    );
                } catch (\Throwable $e) {
                    // Lewati data jika CIS gagal diakses.
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
                    $latestRegistration = $registrations->first(
                        function ($registration) use ($registeredNim) {
                            return (string) $registration->mahasiswa_id
                                === (string) $registeredNim;
                        }
                    );

                    if ($latestRegistration) {
                        $cisUser['nim'] = (string) $registeredNim;
                        $cisUser['nama'] = $student['nama']
                            ?? $latestRegistration->nama;

                        session([
                            'cis_user' => $cisUser,
                        ]);
                    }

                    break;
                }
            }
        }

        return view('dashboard.mahasiswa', [
            'latestRegistration' => $latestRegistration,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Dosen
    |--------------------------------------------------------------------------
    */

    if ($role === 'Dosen') {
        $registrations = $registrationService->getAll();

        $registrationStats = [
            'total' => $registrations->count(),

            'menunggu' => $registrations
                ->where('status', 'menunggu')
                ->count(),

            'diterima' => $registrations
                ->where('status', 'diterima')
                ->count(),

            'ditolak' => $registrations
                ->where('status', 'ditolak')
                ->count(),
        ];

        return view(
            'dashboard.dosen',
            compact('registrationStats')
        );
    }

    abort(403, 'Role tidak memiliki dashboard.');

})->middleware('cis.auth')->name('dashboard');


/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Halaman Formulir Pendaftaran PDLK
|--------------------------------------------------------------------------
| Parameter jenis bersifat opsional.
| Tanpa jenis, formulir menggunakan jenis Magang.
|--------------------------------------------------------------------------
*/

Route::get('/registration/create/{jenis?}', [
    RegistrationController::class,
    'create',
])
    ->middleware('cis.auth')
    ->name('registration.create');


/*
|--------------------------------------------------------------------------
| Menyimpan Pendaftaran PDLK
|--------------------------------------------------------------------------
*/

Route::post('/registration', [
    RegistrationController::class,
    'store',
])
    ->middleware('cis.auth')
    ->name('registration.store');


/*
|--------------------------------------------------------------------------
| Endpoint Data Pendaftaran
|--------------------------------------------------------------------------
*/

Route::prefix('api')
    ->middleware('cis.auth')
    ->group(function () {

        Route::get('/registrations', [
            RegistrationController::class,
            'index',
        ]);

        Route::post('/registrations', [
            RegistrationController::class,
            'store',
        ]);

        Route::get('/registrations/{id}', [
            RegistrationController::class,
            'show',
        ]);

        Route::put('/registrations/{id}', [
            RegistrationController::class,
            'update',
        ]);

        Route::delete('/registrations/{id}', [
            RegistrationController::class,
            'destroy',
        ]);
    });


/*
|--------------------------------------------------------------------------
| Halaman Daftar Pendaftaran untuk Dosen
|--------------------------------------------------------------------------
*/

Route::get('/dosen/pendaftaran', [
    RegistrationController::class,
    'listPage',
])
    ->middleware('cis.auth')
    ->name('registration.index');


/*
|--------------------------------------------------------------------------
| Mengubah Status Pendaftaran
|--------------------------------------------------------------------------
*/

Route::patch('/dosen/pendaftaran/{id}/status', [
    RegistrationController::class,
    'updateStatus',
])
    ->middleware('cis.auth')
    ->name('registration.updateStatus');


/*
|--------------------------------------------------------------------------
| Halaman Pilihan Tujuh Jenis PDLK
|--------------------------------------------------------------------------
*/

Route::get('/pdlk', function () {
    if ((session('cis_user')['role'] ?? null) !== 'Mahasiswa') {
        abort(403, 'Menu PDLK hanya tersedia untuk Mahasiswa.');
    }

    return view('pdlk.index');

})
    ->middleware('cis.auth')
    ->name('pdlk.index');


/*
|--------------------------------------------------------------------------
| Memilih Jenis PDLK
|--------------------------------------------------------------------------
| Semua pilihan diarahkan ke formulir dengan jenis yang sesuai.
|--------------------------------------------------------------------------
*/

Route::get('/pdlk/pilih/{jenis}', function (string $jenis) {

    if ((session('cis_user')['role'] ?? null) !== 'Mahasiswa') {
        abort(403, 'Pendaftaran PDLK hanya tersedia untuk Mahasiswa.');
    }

    $pilihan = [
        'magang' => 'Magang',
        'kerja-praktik' => 'Kerja Praktik',
        'penelitian' => 'Penelitian',
        'pertukaran-pelajar' => 'Pertukaran Pelajar',
        'kkn' => 'Kuliah Kerja Nyata (KKN)',
        'studi-independen' => 'Studi Independen',
        'kewirausahaan' => 'Kewirausahaan',
    ];

    abort_unless(
        array_key_exists($jenis, $pilihan),
        404,
        'Jenis PDLK tidak ditemukan.'
    );

    return redirect()->route('registration.create', [
        'jenis' => $jenis,
    ]);

})
    ->middleware('cis.auth')
    ->name('pdlk.pilih');


/*
|--------------------------------------------------------------------------
| Monitoring Pertengahan PDLK
|--------------------------------------------------------------------------
*/

Route::get('/monitoring/pertengahan', function () {
    if ((session('cis_user')['role'] ?? null) !== 'Mahasiswa') {
        abort(403, 'Monitoring hanya tersedia untuk Mahasiswa.');
    }

    return view('monitoring.pertengahan');
})
    ->middleware(['cis.auth', 'pdlk.accepted'])
    ->name('monitoring.pertengahan');


/*
|--------------------------------------------------------------------------
| Monitoring Akhir PDLK
|--------------------------------------------------------------------------
*/

Route::get('/monitoring/akhir', function () {
    if ((session('cis_user')['role'] ?? null) !== 'Mahasiswa') {
        abort(403, 'Monitoring hanya tersedia untuk Mahasiswa.');
    }

    return view('monitoring.akhir');
})
    ->middleware(['cis.auth', 'pdlk.accepted'])
    ->name('monitoring.akhir');


/*
|--------------------------------------------------------------------------
| Halaman Utama Monitoring PDLK
|--------------------------------------------------------------------------
*/

Route::get('/monitoring', function () {
    if ((session('cis_user')['role'] ?? null) !== 'Mahasiswa') {
        abort(403, 'Monitoring hanya tersedia untuk Mahasiswa.');
    }

    return view('monitoring.index');
})
    ->middleware(['cis.auth', 'pdlk.accepted'])
    ->name('monitoring.index');
