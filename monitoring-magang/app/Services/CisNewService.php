<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CisNewService
{
    public function login(
        string $username,
        string $password
    ): array {
        $response = Http::asMultipart()
            ->post(
                config('sdi.cis_new_api_url') . '/jwt-api/do-auth',
                [
                    [
                        'name' => 'username',
                        'contents' => $username,
                    ],
                    [
                        'name' => 'password',
                        'contents' => $password,
                    ],
                ]
            );

        $response->throw();

        return $response->json();
    }
}
