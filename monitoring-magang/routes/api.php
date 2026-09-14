<?php

use App\Http\Controllers\Api\CisController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::post('/cis/login', [CisController::class, 'login'])
    ->name('api.cis.login');

    Route::get('/cis/mahasiswa/{nim}', [CisController::class, 'getStudentByNim'])
    ->name('api.cis.mahasiswa');
    
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Gateway berhasil berjalan',
        'service' => 'api-gateway',
    ]);
});

Route::get('/registration/test', function () {
    $response = Http::get(
        config('services.registration.url') . '/api/test'
    );

    return response()->json([
        'success' => true,
        'gateway' => 'api-gateway',
        'registration-service' => $response->json(),
    ]);
});

Route::get('/registrations', [RegistrationController::class, 'index']);
Route::post('/registrations', [RegistrationController::class, 'store']);
Route::get('/registrations/{id}', [RegistrationController::class, 'show']);
Route::put('/registrations/{id}', [RegistrationController::class, 'update']);
Route::delete('/registrations/{id}', [RegistrationController::class, 'destroy']);
