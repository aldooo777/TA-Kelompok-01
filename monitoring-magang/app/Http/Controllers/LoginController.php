<?php

namespace App\Http\Controllers;

use App\Services\CisNewService;
use App\Services\CisService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(
        Request $request,
        CisService $cisService,
        CisNewService $cisNewService
    ) {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        try {
            /*
            |--------------------------------------------------------------------------
            | 1. Coba login melalui CIS baru
            |    Digunakan untuk akun Dosen
            |--------------------------------------------------------------------------
            */
            try {
                $response = $cisNewService->login(
                    $request->username,
                    $request->password
                );

                if ($response['result'] ?? false) {
                    $user = $response['user'];

                    session([
                        'cis_user' => $user,
                        'cis_token' => $response['token'],
                    ]);

                    return redirect()->route('dashboard');
                }
            } catch (\Throwable $e) {
                // Jika CIS baru gagal, lanjut coba CIS lama.
            }

            /*
            |--------------------------------------------------------------------------
            | 2. Coba login melalui CIS lama
            |    Digunakan untuk akun Mahasiswa
            |--------------------------------------------------------------------------
            */
            $response = $cisService->login(
                $request->username,
                $request->password
            );

            if (!($response['result'] ?? false)) {
                return back()
                    ->withErrors([
                        'username' =>
                            $response['success']
                            ?? $response['error']
                            ?? 'Username atau password salah.',
                    ])
                    ->withInput($request->only('username'));
            }

            // Data user dari CIS lama
            $user = $response['user'];

            /*
            |--------------------------------------------------------------------------
            | NIM sementara hanya untuk Mahasiswa
            |--------------------------------------------------------------------------
            */
            if (($user['role'] ?? null) === 'Mahasiswa') {
                $user['nim'] = '42324032';
            }

            session([
                'cis_user' => $user,
                'cis_token' => $response['token'],
            ]);

            return redirect()->route('dashboard');

        } catch (\Throwable $e) {
            return back()
                ->withErrors([
                    'username' => 'Login ke CIS gagal. Silakan coba lagi.',
                ])
                ->withInput($request->only('username'));
        }
    }

    public function logout(Request $request)
    {
        $request->session()->forget([
            'cis_user',
            'cis_token',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
