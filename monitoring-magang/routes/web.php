<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.process');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('cis.auth')->name('dashboard');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');
