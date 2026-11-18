<?php

namespace App\Http\Controllers;

use App\Services\CisNewService;
use App\Services\CisService;
use App\Support\Peran;
use Illuminate\Http\Client\ConnectionException;
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

                    $loginCisBaru = $user;
                }
            } catch (\Throwable $e) {
                // Jika CIS baru gagal, lanjut coba CIS lama.
            }

            if (isset($loginCisBaru)) {
                return $this->selesaiLogin($request, $loginCisBaru);
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

            session([
                'cis_user' => $user,
                'cis_token' => $response['token'],
            ]);

            return $this->selesaiLogin($request, $user);

        } catch (ConnectionException $e) {
            // Server CIS utama tidak terjangkau (biasanya hanya dapat diakses dari jaringan kampus).
            report($e);

            return back()
                ->withErrors([
                    'username' => 'Server CIS (' . (parse_url((string) config('sdi.cis_api_url'), PHP_URL_HOST) ?: 'CIS')
                        . ') tidak dapat dihubungi dari jaringan ini. Gunakan jaringan kampus IT Del atau VPN, lalu coba lagi.',
                ])
                ->withInput($request->only('username'));
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withErrors([
                    'username' => 'Login ke CIS gagal. Silakan coba lagi.',
                ])
                ->withInput($request->only('username'));
        }
    }

    /**
     * Simpan identitas ke tabel users, hitung peran PDLK, lalu arahkan
     * ke halaman yang tadinya ingin dibuka (misalnya form pendaftaran).
     */
    private function selesaiLogin(Request $request, array $user)
    {
        $request->session()->regenerate();

        Peran::masuk(Peran::sinkronDariCis($user));

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        $request->session()->forget([
            'cis_user',
            'cis_token',
            'pdlk_user_id',
            'pdlk_peran',
            'peran_aktif',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda');
    }
}
