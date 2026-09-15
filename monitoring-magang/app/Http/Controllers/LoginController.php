<?php

namespace App\Http\Controllers;

use App\Services\CisService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request, CisService $cisService)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        try {
            $response = $cisService->login(
                $request->username,
                $request->password
            );

            if (!($response['result'] ?? false)) {
                return back()
                    ->withErrors([
                        'username' => $response['success'] ?? 'Login gagal.',
                    ])
                    ->withInput($request->only('username'));
            }

            // Data user dari CIS
            $user = $response['user'];

            // Untuk sementara, NIM mahasiswa diambil dari data yang kita miliki
            $user['nim'] = '42324032';

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
