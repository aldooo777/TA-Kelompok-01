<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Kelola\LowonganController;
use App\Http\Controllers\Kelola\PendaftaranController as KelolaPendaftaranController;
use App\Http\Controllers\Kelola\PostinganController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Mahasiswa\PendaftaranController;
use App\Http\Controllers\PesertaPdlkController;
use App\Http\Controllers\PublikController;
use App\Support\Peran;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Publik (Guest) - tanpa login
|--------------------------------------------------------------------------
*/

Route::get('/', [PublikController::class, 'beranda'])->name('beranda');
Route::get('/lowongan', [PublikController::class, 'lowongan'])->name('lowongan.index');
Route::get('/lowongan/{slug}', [PublikController::class, 'lowonganDetail'])->name('lowongan.show');
Route::get('/berita', [PublikController::class, 'berita'])->name('berita.index');
Route::get('/berita/{slug}', [PublikController::class, 'beritaDetail'])->name('berita.show');
Route::get('/tentang-pdlk', [PublikController::class, 'tentang'])->name('tentang');

/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return session()->has('cis_user') ? redirect()->route('dashboard') : view('login');
})->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('login.process');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Portal (wajib login)
|--------------------------------------------------------------------------
*/

Route::middleware('cis.auth')->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Pengguna dengan lebih dari satu peran dapat berganti peran aktif.
    Route::post('/peran/{kode}', function (string $kode) {
        abort_unless(Peran::gantiAktif($kode), 403);

        return redirect()->route('dashboard')->with('success', 'Peran aktif: ' . Peran::label());
    })->name('peran.ganti');

    // Tautan lama "Daftar PDLK" diarahkan ke daftar lowongan.
    Route::redirect('/pdlk', '/lowongan')->name('pdlk.index');

    /*
    |----------------------------------------------------------------------
    | Mahasiswa
    |----------------------------------------------------------------------
    */
    Route::middleware('peran:mahasiswa')->group(function () {
        Route::get('/lowongan/{slug}/daftar', [PendaftaranController::class, 'create'])
            ->name('mahasiswa.pendaftaran.create');
        Route::post('/lowongan/{slug}/daftar', [PendaftaranController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('mahasiswa.pendaftaran.store');
        Route::get('/pendaftaran-saya', [PendaftaranController::class, 'index'])
            ->name('mahasiswa.pendaftaran.index');
        Route::get('/pendaftaran-saya/{pendaftaran}/dokumen', [PendaftaranController::class, 'dokumen'])
            ->name('mahasiswa.pendaftaran.dokumen');

        Route::middleware('pdlk.accepted')->group(function () {
            Route::view('/monitoring', 'monitoring.index')->name('monitoring.index');
            Route::view('/monitoring/pertengahan', 'monitoring.pertengahan')->name('monitoring.pertengahan');
            Route::view('/monitoring/akhir', 'monitoring.akhir')->name('monitoring.akhir');
        });
    });

    /*
    |----------------------------------------------------------------------
    | Mahasiswa aktif PDLK - Staf UPT & koordinator (dibatasi per prodi)
    |----------------------------------------------------------------------
    */
    Route::middleware('peran:staf_upt,koordinator_pdlk_prodi,koordinator_prodi')->group(function () {
        Route::get('/peserta-pdlk', [PesertaPdlkController::class, 'index'])->name('peserta.index');
    });

    /*
    |----------------------------------------------------------------------
    | Staf UPT PP-ESTEM
    |----------------------------------------------------------------------
    */
    Route::middleware('peran:staf_upt')->prefix('kelola')->name('kelola.')->group(function () {
        Route::resource('postingan', PostinganController::class)
            ->except('show')
            ->parameters(['postingan' => 'postingan']);

        Route::resource('lowongan', LowonganController::class)
            ->except('show')
            ->parameters(['lowongan' => 'lowongan']);

        Route::get('pendaftaran', [KelolaPendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::get('pendaftaran/ekspor', [KelolaPendaftaranController::class, 'ekspor'])->name('pendaftaran.ekspor');
        Route::patch('pendaftaran/status-massal', [KelolaPendaftaranController::class, 'updateStatusMassal'])->name('pendaftaran.status-massal');
        Route::patch('pendaftaran/{pendaftaran}/status', [KelolaPendaftaranController::class, 'updateStatus'])->name('pendaftaran.status');
        Route::get('pendaftaran/{pendaftaran}/dokumen', [KelolaPendaftaranController::class, 'dokumen'])->name('pendaftaran.dokumen');
    });

    Route::patch('/peserta-pdlk/{peserta}/tidak-aktif', [PesertaPdlkController::class, 'nonaktifkan'])
        ->middleware('peran:staf_upt')
        ->name('peserta.nonaktifkan');
});
