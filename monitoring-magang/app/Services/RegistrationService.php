<?php

namespace App\Services;

use App\Models\Registration;

class RegistrationService
{
    protected CisService $cisService;

    public function __construct(CisService $cisService)
    {
        $this->cisService = $cisService;
    }

    /**
     * Mengambil seluruh data pendaftaran dari database utama.
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
     * Memverifikasi mahasiswa melalui CIS,
     * kemudian menyimpan pendaftaran ke database utama.
     */
    public function create(array $data): array
    {
        $nim = $data['mahasiswa_id'] ?? null;

        if (empty($nim)) {
            return [
                'success' => false,
                'message' => 'Mahasiswa ID wajib diisi.',
                'status' => 422,
            ];
        }

        // Verifikasi identitas mahasiswa melalui CIS.
        $student = $this->cisService->findStudentByNim($nim);

        if (!$student) {
            return [
                'success' => false,
                'message' => 'Mahasiswa dengan NIM tersebut tidak ditemukan di CIS.',
                'status' => 404,
            ];
        }

        if (empty($student['nim']) || empty($student['nama'])) {
            return [
                'success' => false,
                'message' => 'Data identitas mahasiswa dari CIS tidak lengkap.',
                'status' => 422,
            ];
        }

        // Simpan langsung melalui model Registration.
        $registration = Registration::create([
            'mahasiswa_id' => $student['nim'],
            'nama' => $student['nama'],
            'tempat_magang' => $data['tempat_magang'] ?? '',
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
     * Memperbarui data pendaftaran pada database utama.
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
     * Menghapus data pendaftaran dari database utama.
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
