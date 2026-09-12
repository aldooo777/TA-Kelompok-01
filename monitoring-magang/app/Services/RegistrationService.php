<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RegistrationService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.registration.url');
    }

    public function getAll()
    {
        return Http::get($this->baseUrl . '/api/registrations');
    }

    public function getById($id)
    {
        return Http::get($this->baseUrl . '/api/registrations/' . $id);
    }

    public function create(array $data)
    {
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
