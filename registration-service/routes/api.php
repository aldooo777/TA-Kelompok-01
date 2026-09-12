<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;

Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'Registration Service berhasil berjalan',
        'service' => 'registration-service',
    ]);
});

Route::get('/registrations', [RegistrationController::class, 'index']);

Route::post('/registrations', [RegistrationController::class, 'store']);

Route::get('/registrations/{id}', [RegistrationController::class, 'show']);

Route::put('/registrations/{id}', [RegistrationController::class, 'update']);

Route::delete('/registrations/{id}', [RegistrationController::class, 'destroy']);
