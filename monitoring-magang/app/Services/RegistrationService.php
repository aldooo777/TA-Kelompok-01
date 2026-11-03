<?php

namespace App\Services;

use App\Models\Registration;

class RegistrationService
{
    /**
     * Mengambil seluruh data pendaftaran.
     */
    public function getAll()
    {
        return Registration::query()
            ->latest()
            ->get();
    }

    /**
     * Mengambil satu data pendaftaran berdasarkan ID.
     */
    public function getById($id): ?Registration
    {
        return Registration::find($id);
    }

    /**
     * Menyimpan pendaftaran mahasiswa yang identitasnya
     * sudah diverifikasi oleh RegistrationController.
     */
    public function create(array $data): array
    {
        // Periksa kelengkapan data.
        $requiredFields = [
            'mahasiswa_id',
            'nama',
            'jenis_pdlk',
            'tempat_magang',
        ];

        foreach ($requiredFields as $field) {
            if (
                !isset($data[$field]) ||
                trim((string) $data[$field]) === ''
            ) {
                return [
                    'success' => false,
                    'message' => "Kolom {$field} wajib diisi.",
                    'status' => 422,
                ];
            }
        }

        // Jenis kegiatan yang diizinkan.
        $allowedTypes = [
            'magang',
            'kerja-praktik',
            'penelitian',
            'pertukaran-pelajar',
            'kkn',
            'studi-independen',
            'kewirausahaan',
        ];

        if (!in_array($data['jenis_pdlk'], $allowedTypes, true)) {
            return [
                'success' => false,
                'message' => 'Jenis kegiatan PDLK tidak valid.',
                'status' => 422,
            ];
        }

        // Simpan pendaftaran ke database utama.
        $registration = Registration::create([
            'mahasiswa_id' => $data['mahasiswa_id'],
            'nama' => $data['nama'],
            'jenis_pdlk' => $data['jenis_pdlk'],
            'tempat_magang' => $data['tempat_magang'],
            'status' => 'menunggu',
        ]);

        return [
            'success' => true,
            'message' => 'Pendaftaran berhasil disimpan.',
            'data' => $registration,
            'status' => 201,
        ];
    }

    /**
     * Memperbarui data pendaftaran.
     */
    public function update($id, array $data): array
    {
        $registration = Registration::find($id);

        if (!$registration) {
            return [
                'success' => false,
                'message' => 'Data pendaftaran tidak ditemukan.',
                'status' => 404,
            ];
        }

        $registration->fill($data);
        $registration->save();

        return [
            'success' => true,
            'message' => 'Data pendaftaran berhasil diperbarui.',
            'data' => $registration,
            'status' => 200,
        ];
    }

    /**
     * Menghapus data pendaftaran.
     */
    public function delete($id): array
    {
        $registration = Registration::find($id);

        if (!$registration) {
            return [
                'success' => false,
                'message' => 'Data pendaftaran tidak ditemukan.',
                'status' => 404,
            ];
        }

        $registration->delete();

        return [
            'success' => true,
            'message' => 'Data pendaftaran berhasil dihapus.',
            'status' => 200,
        ];
    }
}
