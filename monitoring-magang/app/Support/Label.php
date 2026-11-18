<?php

namespace App\Support;

/** Label tampilan untuk nilai enum di database. */
class Label
{
    public const ROLE = [
        'student' => 'Mahasiswa',
        'upt_staff' => 'Staf UPT PP-ESTEM',
        'pdlk_coordinator' => 'Koordinator PDLK Prodi',
        'program_head' => 'Koordinator Program Studi',
    ];

    public const ANNOUNCEMENT_STATUS = ['draft' => 'Draf', 'published' => 'Terbit', 'archived' => 'Diarsipkan'];
    public const GALLERY_STATUS = ['draft' => 'Draf', 'published' => 'Terbit'];
    public const MEDIA_TYPE = ['image' => 'Gambar', 'video' => 'Video'];
    public const REGISTRATION_TYPE = ['internal' => 'Internal (formulir sistem)', 'external' => 'Eksternal (situs mitra)'];
    public const FIELD_TYPE = ['text' => 'Teks singkat', 'textarea' => 'Teks panjang', 'number' => 'Angka', 'url' => 'Tautan (URL)'];
    public const COOPERATION = ['cooperating' => 'Bekerja sama', 'non_cooperating' => 'Belum bekerja sama', 'in_process' => 'Dalam proses'];
    public const CONTACT_TYPE = [
        'email' => 'Email', 'whatsapp' => 'WhatsApp', 'instagram' => 'Instagram', 'phone' => 'Telepon',
        'address' => 'Alamat', 'website' => 'Website', 'other' => 'Lainnya',
    ];
    public const APPLICATION_STATUS = ['registered' => 'Terdaftar', 'processing' => 'Proses', 'passed' => 'Lulus', 'failed' => 'Tidak Lulus'];
    public const PARTICIPANT_STATUS = ['active' => 'Aktif', 'inactive' => 'Tidak Aktif', 'completed' => 'Selesai'];
    public const SOURCE = ['application' => 'Pendaftaran', 'manual' => 'Input manual'];
    public const PROGRAM_CATEGORY = ['flagship' => 'Flagship', 'independent' => 'Mandiri'];
    public const STAGE = ['onboarding' => 'Onboarding', 'ongoing' => 'Sedang berjalan'];
    public const WORK_MODE = ['onsite' => 'Onsite', 'online' => 'Online', 'remote' => 'Remote'];
    public const SEMESTER = ['ganjil' => 'Ganjil', 'genap' => 'Genap'];
    public const MONEV_TYPE = ['mid' => 'Monev Tengah', 'final' => 'Monev Akhir'];
    public const QUESTION_TYPE = [
        'text' => 'Jawaban singkat', 'textarea' => 'Jawaban panjang', 'scale' => 'Skala (angka)',
        'choice' => 'Pilihan ganda', 'file' => 'Unggah file / bukti',
    ];
    public const RESPONSE_STATUS = ['draft' => 'Draf', 'submitted' => 'Terkirim'];

    public static function of(array $map, ?string $value): string
    {
        return $value === null ? '-' : ($map[$value] ?? $value);
    }
}
