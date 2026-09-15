<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrationController;

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.process');

Route::get('/dashboard', function () {

    $role = session('cis_user')['role'] ?? null;

    if ($role === 'Mahasiswa') {
        return view('dashboard.mahasiswa');
    }

    if ($role === 'Dosen') {
        return view('dashboard.dosen');
    }

    abort(403, 'Role tidak memiliki dashboard.');

})->middleware('cis.auth')->name('dashboard');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');

    Route::get('/registration/create', [RegistrationController::class, 'create'])
    ->name('registration.create');

    Route::post('/registration', [RegistrationController::class, 'store'])
    ->name('registration.store');
