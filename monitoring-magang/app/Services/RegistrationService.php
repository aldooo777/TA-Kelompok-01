<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RegistrationService
{
    protected string $baseUrl;

    protected CisService $cisService;

    public function __construct(CisService $cisService)
    {
        $this->baseUrl = config('services.registration.url');
        $this->cisService = $cisService;
    }

    public function getAll()
    {
        return Http::get(
            $this->baseUrl . '/api/registrations'
        );
    }

    public function getById($id)
    {
        return Http::get(
            $this->baseUrl . '/api/registrations/' . $id
        );
    }

    public function create(array $data)
{
    /*
    |--------------------------------------------------------------------------
    | 1. Ambil NIM dari mahasiswa_id
    |--------------------------------------------------------------------------
    */

    $nim = $data['mahasiswa_id'] ?? null;

    if (!$nim) {
        return response()->json([
            'success' => false,
            'message' => 'Mahasiswa ID wajib diisi.',
        ], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Cek mahasiswa ke CIS
    |--------------------------------------------------------------------------
    */

    $student = $this->cisService->findStudentByNim($nim);

    if (!$student) {
        return response()->json([
            'success' => false,
            'message' => 'Mahasiswa dengan NIM tersebut tidak ditemukan di CIS.',
        ], 404);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Mahasiswa ditemukan
    |    Lanjutkan ke registration-service
    |--------------------------------------------------------------------------
    */

    return Http::post(
        $this->baseUrl . '/api/registrations',
        $data
    );
}

    public function update($id, array $data)
    {
        return Http::put(
            $this->baseUrl . '/api/registrations/' . $id,
            $data
        );
    }

    public function delete($id)
    {
        return Http::delete(
            $this->baseUrl . '/api/registrations/' . $id
        );
    }
}
