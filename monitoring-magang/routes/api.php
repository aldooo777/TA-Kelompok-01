<?php

use App\Http\Controllers\Api\CisController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::post('/cis/login', [CisController::class, 'login'])
    ->name('api.cis.login');

    Route::get('/cis/mahasiswa/{nim}', [CisController::class, 'getStudentByNim'])
    ->name('api.cis.mahasiswa');


Route::post('/login', [LoginController::class, 'login'])
    ->name('api.login');
