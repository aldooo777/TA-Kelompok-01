<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Kelola\GaleriController;
use App\Http\Controllers\Kelola\HasilController;
use App\Http\Controllers\Kelola\JadwalMonevController;
use App\Http\Controllers\Kelola\KuesionerController;
use App\Http\Controllers\Kelola\LowonganController;
use App\Http\Controllers\Kelola\MasterController;
use App\Http\Controllers\Kelola\PendaftaranController as KelolaPendaftaranController;
use App\Http\Controllers\Kelola\PenggunaController;
use App\Http\Controllers\Kelola\PostinganController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Mahasiswa\PendaftaranController;
use App\Http\Controllers\Mahasiswa\MonevController;
use App\Http\Controllers\Mahasiswa\ProgresController;
use App\Http\Controllers\PesertaPdlkController;
use App\Http\Controllers\PublikController;
use App\Http\Controllers\RekapMonevController;
use App\Support\MasterData;
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
// Sprint 2 (PB-06): pengumuman hasil seleksi
Route::middleware('sprint:2')->group(function () {
    Route::get('/pengumuman-hasil', [PublikController::class, 'hasil'])->name('hasil.index');
    Route::get('/pengumuman-hasil/{slug}', [PublikController::class, 'hasilDetail'])->name('hasil.show');
});
Route::get('/galeri', [PublikController::class, 'galeri'])->name('galeri.index');
Route::get('/galeri/{id}', [PublikController::class, 'galeriDetail'])->whereNumber('id')->name('galeri.show');
Route::get('/tentang-pdlk', [PublikController::class, 'tentang'])->name('tentang');
Route::get('/struktur-organisasi', [PublikController::class, 'strukturOrganisasi'])->name('struktur');
Route::get('/jenis-pdlk', [PublikController::class, 'jenisPdlk'])->name('jenis-pdlk');
Route::get('/jenis-pdlk/{slug}', [PublikController::class, 'jenisPdlkDetail'])->name('jenis-pdlk.show');
Route::get('/mitra', [PublikController::class, 'mitra'])->name('mitra');
Route::get('/rekapitulasi-data', [PublikController::class, 'rekapitulasi'])->name('rekapitulasi');

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

    Route::get('/dashboard', DashboardController::class)->middleware('monev.wajib')->name('dashboard');

    // Pengguna dengan lebih dari satu peran dapat berganti peran aktif.
    Route::post('/peran/{kode}', function (string $kode) {
        abort_unless(Peran::gantiAktif($kode), 403);

        return redirect()->route('dashboard')->with('success', 'Peran aktif: ' . Peran::label());
    })->name('peran.ganti');

    Route::redirect('/pdlk', '/portal/lowongan')->name('pdlk.index');

    // Lowongan di dalam tampilan dashboard (isi sama dengan halaman publik)
    Route::middleware('monev.wajib')->group(function () {
        Route::get('/portal/lowongan', [PublikController::class, 'lowongan'])->name('portal.lowongan.index');
        Route::get('/portal/lowongan/{slug}', [PublikController::class, 'lowonganDetail'])->name('portal.lowongan.show');
    });

    /*
    | Mahasiswa
    */
    Route::middleware(['peran:student', 'monev.wajib'])->group(function () {
        Route::get('/lowongan/{slug}/daftar', [PendaftaranController::class, 'create'])->name('mahasiswa.pendaftaran.create');
        Route::post('/lowongan/{slug}/daftar', [PendaftaranController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('mahasiswa.pendaftaran.store');
        Route::get('/pendaftaran-saya/{id}/dokumen', [PendaftaranController::class, 'dokumen'])->whereNumber('id')->name('mahasiswa.pendaftaran.dokumen');

        // Sprint 2: PB-05 progres pendaftaran, PB-09 progres pelaksanaan
        Route::middleware('sprint:2')->group(function () {
                Route::get('/pendaftaran-saya', [PendaftaranController::class, 'index'])->name('mahasiswa.pendaftaran.index');
                Route::get('/progres', [ProgresController::class, 'edit'])->name('mahasiswa.progres.edit');
                Route::put('/progres', [ProgresController::class, 'update'])->name('mahasiswa.progres.update');
        });
    });

    // Monev tidak ikut dikunci karena justru halaman yang harus diisi.
    // Sprint 3 (PB-11)
    Route::middleware(['sprint:3', 'peran:student', 'pdlk.accepted'])->group(function () {
        Route::get('/monitoring', [MonevController::class, 'index'])->name('monitoring.index');
        Route::get('/monitoring/{jadwalId}', [MonevController::class, 'isi'])->whereNumber('jadwalId')->name('monitoring.isi');
        Route::post('/monitoring/{jadwalId}', [MonevController::class, 'simpan'])->whereNumber('jadwalId')->name('monitoring.simpan');
    });

    /*
    | Mahasiswa aktif PDLK - Staf UPT & koordinator (dibatasi per prodi)
    */
    // Sprint 2 (PB-07, PB-08) dan Sprint 3 (PB-12, PB-13)
    Route::middleware(['sprint:2', 'peran:upt_staff,pdlk_coordinator,program_head'])->group(function () {
        Route::get('/peserta-pdlk', [PesertaPdlkController::class, 'index'])->name('peserta.index');
        Route::get('/peserta-pdlk/{id}', [PesertaPdlkController::class, 'show'])->whereNumber('id')->name('peserta.show');
    });

    Route::middleware(['sprint:3', 'peran:upt_staff,pdlk_coordinator,program_head'])->group(function () {
        Route::get('/rekap-monev', [RekapMonevController::class, 'index'])->name('rekap-monev.index');
        Route::get('/rekap-monev/jawaban/{responseId}', [RekapMonevController::class, 'jawaban'])->whereNumber('responseId')->name('rekap-monev.jawaban');
        Route::get('/rekap-monev/{jadwalId}/ekspor', [RekapMonevController::class, 'ekspor'])->whereNumber('jadwalId')->name('rekap-monev.ekspor');
    });

    Route::middleware(['sprint:2', 'peran:upt_staff,pdlk_coordinator'])->group(function () {
        Route::get('/peserta-pdlk/tambah', [PesertaPdlkController::class, 'create'])->name('peserta.create');
        Route::post('/peserta-pdlk', [PesertaPdlkController::class, 'store'])->name('peserta.store');
        Route::get('/peserta-pdlk/{id}/ubah', [PesertaPdlkController::class, 'edit'])->whereNumber('id')->name('peserta.edit');
        Route::put('/peserta-pdlk/{id}', [PesertaPdlkController::class, 'update'])->whereNumber('id')->name('peserta.update');
    });

    Route::patch('/peserta-pdlk/{id}/tidak-aktif', [PesertaPdlkController::class, 'nonaktifkan'])
        ->whereNumber('id')
        ->middleware(['sprint:2', 'peran:upt_staff'])
        ->name('peserta.nonaktifkan');

    /*
    | Staf UPT PP-ESTEM
    */
    Route::middleware('peran:upt_staff')->prefix('kelola')->name('kelola.')->group(function () {
        Route::resource('postingan', PostinganController::class)->except('show')->parameters(['postingan' => 'id']);
        Route::resource('lowongan', LowonganController::class)->except('show')->parameters(['lowongan' => 'id']);
        Route::resource('hasil', HasilController::class)->except('show')->parameters(['hasil' => 'id'])->middleware('sprint:2');

        // Sprint 3 (PB-10): kuesioner & jadwal monev
        Route::middleware('sprint:3')->group(function () {
            Route::resource('kuesioner', KuesionerController::class)->only(['index', 'create', 'store', 'edit', 'update'])->parameters(['kuesioner' => 'id']);
            Route::patch('kuesioner/{id}/status', [KuesionerController::class, 'toggle'])->name('kuesioner.toggle');
            Route::post('kuesioner/{id}/pertanyaan', [KuesionerController::class, 'simpanPertanyaan'])->name('kuesioner.pertanyaan.store');
            Route::put('kuesioner/{id}/pertanyaan/{pertanyaanId}', [KuesionerController::class, 'simpanPertanyaan'])->name('kuesioner.pertanyaan.update');
            Route::delete('kuesioner/{id}/pertanyaan/{pertanyaanId}', [KuesionerController::class, 'hapusPertanyaan'])->name('kuesioner.pertanyaan.destroy');
            Route::resource('jadwal-monev', JadwalMonevController::class)->except('show')->parameters(['jadwal-monev' => 'id']);
        });

        Route::resource('galeri', GaleriController::class)->except('show')->parameters(['galeri' => 'id']);
        Route::delete('galeri/{id}/item/{itemId}', [GaleriController::class, 'hapusItem'])->name('galeri.item.destroy');

        // Sprint 2 (PB-06): pengelolaan pendaftar
        Route::middleware('sprint:2')->group(function () {
            Route::get('pendaftaran', [KelolaPendaftaranController::class, 'index'])->name('pendaftaran.index');
            Route::get('pendaftaran/ekspor', [KelolaPendaftaranController::class, 'ekspor'])->name('pendaftaran.ekspor');
            Route::patch('pendaftaran/status-massal', [KelolaPendaftaranController::class, 'updateStatusMassal'])->name('pendaftaran.status-massal');
            Route::patch('pendaftaran/{id}/status', [KelolaPendaftaranController::class, 'updateStatus'])->whereNumber('id')->name('pendaftaran.status');
            Route::get('pendaftaran/{id}/dokumen', [KelolaPendaftaranController::class, 'dokumen'])->whereNumber('id')->name('pendaftaran.dokumen');
        });

        Route::get('pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
        Route::get('pengguna/{id}', [PenggunaController::class, 'edit'])->whereNumber('id')->name('pengguna.edit');
        Route::post('pengguna/{id}/peran', [PenggunaController::class, 'tambahPeran'])->whereNumber('id')->name('pengguna.peran.store');
        Route::delete('pengguna/{id}/peran/{userRoleId}', [PenggunaController::class, 'hapusPeran'])->whereNumber('id')->name('pengguna.peran.destroy');

        // Data master: program-studi, periode, jenis-kegiatan, mitra, struktur-organisasi, kontak, peran
        Route::prefix('master/{master}')->name('master.')->whereIn('master', array_keys(MasterData::all()))->group(function () {
            Route::get('/', [MasterController::class, 'index'])->name('index');
            Route::get('/tambah', [MasterController::class, 'create'])->name('create');
            Route::post('/', [MasterController::class, 'store'])->name('store');
            Route::get('/{id}/ubah', [MasterController::class, 'edit'])->whereNumber('id')->name('edit');
            Route::put('/{id}', [MasterController::class, 'update'])->whereNumber('id')->name('update');
            Route::patch('/{id}/status', [MasterController::class, 'toggle'])->whereNumber('id')->name('toggle');
        });
    });
});
