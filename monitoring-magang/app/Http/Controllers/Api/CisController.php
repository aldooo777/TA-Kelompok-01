<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CisService;

class CisController extends Controller
{
    public function login(CisService $cisService)
    {
        return response()->json(
            $cisService->login()
        );
    }

    public function getStudentByNim(
        string $nim,
        CisService $cisService
    ) {
        $token = $cisService->getToken();

        $response = $cisService->getStudentByNim(
            $nim,
            $token
        );

        return response()->json($response);
    }
}
