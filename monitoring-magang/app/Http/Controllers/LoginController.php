<?php

namespace App\Http\Controllers;

use App\Services\CisService;
use App\Support\Peran;
use Illuminate\Http\Client\ConnectionException;
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
            /*
            |--------------------------------------------------------------------------
            | Login melalui API CIS (cis.del.ac.id)
            | Berlaku untuk semua akun: mahasiswa, dosen, dan staf.
            |--------------------------------------------------------------------------
            */
            $response = $cisService->login(
                $request->username,
                $request->password
            );

            if (!($response['result'] ?? false)) {
                $pesan = trim((string) ($response['error'] ?? ''));

                return back()
                    ->withErrors([
                        'username' => $pesan !== '' ? $pesan : 'Username atau password salah.',
                    ])
                    ->withInput($request->only('username'));
            }

            $user = $response['user'];

            session([
                'cis_user' => $user,
                'cis_token' => $response['token'],
            ]);

            return $this->selesaiLogin($request, $user);

        } catch (ConnectionException $e) {
            // Server CIS tidak terjangkau (biasanya hanya dapat diakses dari jaringan kampus).
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
