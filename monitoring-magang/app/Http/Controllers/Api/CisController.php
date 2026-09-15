<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CisService;
use Illuminate\Http\Request;

class CisController extends Controller
{
    public function login(
        Request $request,
        CisService $cisService
    ) {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $response = $cisService->login(
            $request->username,
            $request->password
        );

        return response()->json($response);
    }

    public function getStudentByNim(
        string $nim,
        CisService $cisService
    ) {
        $student = $cisService->findStudentByNim($nim);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $student,
        ]);
    }
}
