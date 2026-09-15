<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CisService
{
    /**
     * Login ke CIS API.
     */
    public function login(
    string $username,
    string $password
): array {
    $response = Http::asForm()
        ->post(
            config('sdi.cis_api_url') . '/jwt-api/do-auth',
            [
                'username' => $username,
                'password' => $password,
            ]
        );

    $response->throw();

    return $response->json();
}

    /**
     * Mengambil JWT CIS.
     *
     * Token disimpan sementara di cache
     * agar tidak login ulang pada setiap request.
     */
    public function getToken(): string
{
    return Cache::remember(
        'cis_api_token',
        now()->addMinutes(30),
        function () {
            $login = $this->login(
                config('sdi.cis_api_username'),
                config('sdi.cis_api_password')
            );

            if (empty($login['token'])) {
                throw new \RuntimeException(
                    'Token CIS tidak ditemukan pada response login.'
                );
            }

            return $login['token'];
        }
    );
}

    /**
     * Mengambil data mahasiswa berdasarkan NIM.
     */
    public function getStudentByNim(
        string $nim,
        string $token
    ): array {
        $response = Http::withToken($token)
            ->acceptJson()
            ->get(
                config('sdi.cis_api_url')
                . '/library-api/get-student-by-nim',
                [
                    'nim' => $nim,
                ]
            );

        return [
            'status' => $response->status(),
            'body' => $response->json(),
        ];
    }

    public function findStudentByNim(string $nim): ?array
{
    $token = $this->getToken();

    $response = $this->getStudentByNim($nim, $token);

    $student = $response['body']['data'] ?? null;

    if ($response['status'] !== 200) {
        return null;
    }

    if (
        !is_array($student) ||
        empty($student['nim'])
    ) {
        return null;
    }

    if ((string) $student['nim'] !== (string) $nim) {
        return null;
    }

    return $student;
}
    }

