<?php

use Illuminate\Support\Carbon;

if (!function_exists('tgl')) {
    /** Format tanggal dari kolom database (string) ke bahasa Indonesia. */
    function tgl(mixed $nilai, string $format = 'd F Y'): string
    {
        return $nilai ? Carbon::parse($nilai)->translatedFormat($format) : '';
    }
}

if (!function_exists('file_url')) {
    /** URL publik untuk file di storage/app/public. */
    function file_url(?string $path): ?string
    {
        // asset() mengikuti alamat yang sedang dibuka (mis. 127.0.0.1:8000),
        // sehingga tidak bergantung pada APP_URL di .env.
        return $path ? asset('storage/' . ltrim($path, '/')) : null;
    }
}

if (!function_exists('jabatan_cis')) {
    /**
     * Teks jabatan dari kolom users.cis_position (JSON dari CIS). Bentuk data CIS
     * bisa berupa teks, daftar teks, atau daftar objek bertingkat; yang diambil
     * hanya nama jabatannya.
     */
    function jabatan_cis(?string $json): string
    {
        $data = json_decode((string) $json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return trim((string) $json);
        }

        $hasil = [];
        $ambil = function ($nilai) use (&$ambil, &$hasil) {
            if (is_array($nilai)) {
                foreach (['jabatan', 'nama_jabatan', 'struktur_jabatan', 'nama', 'name'] as $kunci) {
                    if (isset($nilai[$kunci]) && is_scalar($nilai[$kunci])) {
                        $hasil[] = trim((string) $nilai[$kunci]);

                        return;
                    }
                }
                foreach ($nilai as $isi) {
                    $ambil($isi);
                }
            } elseif (is_string($nilai) && trim($nilai) !== '' && !is_numeric($nilai)) {
                $hasil[] = trim($nilai);
            }
        };
        $ambil($data);

        return implode(', ', array_unique(array_filter($hasil)));
    }
}

if (!function_exists('sprint')) {
    /** Fitur milik sprint $n sudah diaktifkan (PDLK_SPRINT di .env). */
    function sprint(int $n): bool
    {
        return (int) config('pdlk.sprint', 1) >= $n;
    }
}

if (!function_exists('lowongan_dibuka')) {
    /**
     * Lowongan masih menerima pendaftaran. $v berisi kolom:
     * status, is_closed, registration_start_date, registration_deadline.
     */
    function lowongan_dibuka(object $v): bool
    {
        $hariIni = now()->toDateString();

        return ($v->status ?? null) === 'published'
            && !($v->is_closed ?? false)
            && (empty($v->registration_start_date) || $v->registration_start_date <= $hariIni)
            && (empty($v->registration_deadline) || $v->registration_deadline >= $hariIni);
    }
}

if (!function_exists('ringkas')) {
    function ringkas(?string $teks, int $panjang = 160): string
    {
        return \Illuminate\Support\Str::limit(strip_tags((string) $teks), $panjang);
    }
}
