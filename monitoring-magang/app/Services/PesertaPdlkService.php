<?php

namespace App\Services;

use App\Models\Pendaftaran;
use App\Models\PesertaPdlk;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Logika mahasiswa aktif PDLK (tabel peserta_pdlk).
 */
class PesertaPdlkService
{
    /**
     * Mengubah status pendaftaran. Bila menjadi "lulus", mahasiswa otomatis
     * dicatat sebagai mahasiswa aktif PDLK.
     *
     * @return array{ok: bool, pesan: string}
     */
    public function ubahStatusPendaftaran(Pendaftaran $p, string $status, ?string $catatan, ?int $olehUserId): array
    {
        if ($p->status === Pendaftaran::LULUS && $status !== Pendaftaran::LULUS && $p->pesertaPdlk()->exists()) {
            return [
                'ok' => false,
                'pesan' => "Pendaftaran {$p->nama} sudah lulus dan tercatat sebagai mahasiswa aktif. "
                    . 'Tidak ada pembatalan; untuk kondisi force majeure ubah statusnya menjadi Tidak Aktif di menu Mahasiswa Aktif.',
            ];
        }

        DB::transaction(function () use ($p, $status, $catatan, $olehUserId) {
            $p->update([
                'status' => $status,
                'catatan' => $catatan ?? $p->catatan,
                'diproses_oleh' => $olehUserId,
                'diproses_at' => now(),
            ]);

            if ($status === Pendaftaran::LULUS) {
                $this->buatDariPendaftaran($p, $olehUserId);
            }
        });

        return ['ok' => true, 'pesan' => "Status {$p->nama} menjadi " . Pendaftaran::STATUS[$status] . '.'];
    }

    public function buatDariPendaftaran(Pendaftaran $p, ?int $olehUserId): PesertaPdlk
    {
        $p->loadMissing('lowongan');

        $peserta = PesertaPdlk::firstOrCreate(
            ['pendaftaran_id' => $p->id],
            [
                'nim' => $p->nim,
                'nama' => $p->nama,
                'status' => PesertaPdlk::AKTIF,
                'sumber' => 'lowongan',
                'user_id' => $p->user_id ?? User::where('nim', $p->nim)->value('id'),
                'program_studi_id' => $p->program_studi_id,
                'jenis_kegiatan_id' => $p->lowongan->jenis_kegiatan_id,
                'mitra_id' => $p->lowongan->mitra_id,
                'posisi' => $p->lowongan->posisi,
                'diinput_oleh' => $olehUserId,
            ]
        );

        if ($peserta->wasRecentlyCreated) {
            $peserta->riwayatStatus()->create([
                'status_lama' => null,
                'status_baru' => PesertaPdlk::AKTIF,
                'alasan' => 'Lulus seleksi: ' . $p->lowongan->judul,
                'diubah_oleh' => $olehUserId,
            ]);
        }

        return $peserta;
    }
}
