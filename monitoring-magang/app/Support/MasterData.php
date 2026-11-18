<?php

namespace App\Support;

/**
 * Definisi halaman CRUD data master. Setiap entri memetakan satu tabel
 * di database (database/sql/skema_pdlk.sql) ke form dan tabel daftar.
 *
 * Tipe field: text, textarea, number, date, url, enum, image.
 */
class MasterData
{
    public static function all(): array
    {
        return [
            'program-studi' => [
                'table' => 'study_programs',
                'title' => 'Program Studi',
                'order' => 'level, name',
                'fields' => [
                    'code' => ['label' => 'Kode', 'type' => 'text', 'required' => true, 'unique' => true, 'max' => 20, 'help' => 'Contoh: D3TI'],
                    'name' => ['label' => 'Nama program studi', 'type' => 'text', 'required' => true, 'max' => 150],
                    'level' => ['label' => 'Jenjang', 'type' => 'enum', 'options' => ['D3' => 'D3', 'D4' => 'D4', 'S1' => 'S1'], 'required' => true],
                ],
                'list' => ['code', 'level', 'name'],
            ],

            'periode' => [
                'table' => 'periods',
                'title' => 'Periode PDLK',
                'order' => 'start_date DESC',
                'fields' => [
                    'name' => ['label' => 'Nama periode', 'type' => 'text', 'required' => true, 'max' => 100, 'help' => 'Contoh: PDLK Genap 2025/2026'],
                    'academic_year' => ['label' => 'Tahun akademik', 'type' => 'text', 'required' => true, 'max' => 9, 'help' => 'Contoh: 2025/2026'],
                    'semester' => ['label' => 'Semester', 'type' => 'enum', 'options' => Label::SEMESTER, 'required' => true],
                    'start_date' => ['label' => 'Tanggal mulai', 'type' => 'date', 'required' => true],
                    'end_date' => ['label' => 'Tanggal selesai', 'type' => 'date', 'required' => true, 'after' => 'start_date'],
                ],
                'list' => ['name', 'academic_year', 'semester', 'start_date', 'end_date'],
            ],

            'jenis-kegiatan' => [
                'table' => 'activity_types',
                'title' => 'Jenis Kegiatan PDLK',
                'order' => 'display_order, name',
                'fields' => [
                    'name' => ['label' => 'Nama', 'type' => 'text', 'required' => true, 'max' => 100],
                    'slug' => ['label' => 'Slug (untuk URL)', 'type' => 'text', 'required' => true, 'unique' => true, 'max' => 50, 'help' => 'Huruf kecil dan tanda hubung, mis. kerja-praktik'],
                    'summary' => ['label' => 'Ringkasan', 'type' => 'text', 'max' => 255],
                    'description' => ['label' => 'Deskripsi', 'type' => 'textarea'],
                    'duration_text' => ['label' => 'Durasi', 'type' => 'text', 'max' => 100, 'help' => 'Contoh: 1 semester (± 4–6 bulan)'],
                    'objectives' => ['label' => 'Tujuan', 'type' => 'textarea'],
                    'icon' => ['label' => 'Ikon (emoji)', 'type' => 'text', 'max' => 50, 'help' => 'Contoh: 💼'],
                    'image' => ['label' => 'Gambar', 'type' => 'image', 'folder' => 'jenis-kegiatan'],
                    'display_order' => ['label' => 'Urutan tampil', 'type' => 'number', 'min' => 0],
                ],
                'list' => ['display_order', 'icon', 'name', 'summary'],
            ],

            'mitra' => [
                'table' => 'partners',
                'title' => 'Mitra',
                'order' => 'name',
                'fields' => [
                    'name' => ['label' => 'Nama mitra', 'type' => 'text', 'required' => true, 'unique' => true, 'max' => 200],
                    'field' => ['label' => 'Bidang usaha', 'type' => 'text', 'max' => 150],
                    'address' => ['label' => 'Alamat', 'type' => 'text', 'max' => 255],
                    'website' => ['label' => 'Website', 'type' => 'url', 'max' => 255],
                    'pic_name' => ['label' => 'Nama PIC', 'type' => 'text', 'max' => 150],
                    'pic_contact' => ['label' => 'Kontak PIC', 'type' => 'text', 'max' => 100],
                    'cooperation_status' => ['label' => 'Status kerja sama', 'type' => 'enum', 'options' => Label::COOPERATION],
                ],
                'list' => ['name', 'field', 'pic_name', 'cooperation_status'],
            ],

            'struktur-organisasi' => [
                'table' => 'organization_positions',
                'title' => 'Struktur Organisasi',
                'order' => 'display_order, position_name',
                'fields' => [
                    'position_name' => ['label' => 'Jabatan', 'type' => 'text', 'required' => true, 'max' => 150],
                    'officer_name' => ['label' => 'Nama pejabat', 'type' => 'text', 'max' => 150],
                    'description' => ['label' => 'Tugas / deskripsi', 'type' => 'textarea'],
                    'display_order' => ['label' => 'Urutan tampil', 'type' => 'number', 'min' => 0],
                ],
                'list' => ['display_order', 'position_name', 'officer_name'],
            ],

            'kontak' => [
                'table' => 'contacts',
                'title' => 'Kontak',
                'order' => 'display_order, name',
                'fields' => [
                    'name' => ['label' => 'Nama', 'type' => 'text', 'required' => true, 'max' => 100, 'help' => 'Contoh: Email UPT PP-ESTEM'],
                    'contact_type' => ['label' => 'Jenis kontak', 'type' => 'enum', 'options' => Label::CONTACT_TYPE, 'required' => true],
                    'contact_value' => ['label' => 'Isi kontak', 'type' => 'text', 'required' => true, 'max' => 255, 'help' => 'Alamat email, nomor, akun, atau URL'],
                    'icon' => ['label' => 'Ikon (emoji)', 'type' => 'text', 'max' => 50],
                    'image' => ['label' => 'Gambar', 'type' => 'image', 'folder' => 'kontak'],
                    'display_order' => ['label' => 'Urutan tampil', 'type' => 'number', 'min' => 0],
                ],
                'list' => ['display_order', 'name', 'contact_type', 'contact_value'],
            ],

            'peran' => [
                'table' => 'roles',
                'title' => 'Peran',
                'order' => 'id',
                'no_create' => true,
                'fields' => [
                    'code' => ['label' => 'Kode', 'type' => 'text', 'readonly' => true, 'help' => 'Kode dipakai aplikasi, tidak dapat diubah.'],
                    'name' => ['label' => 'Nama peran', 'type' => 'text', 'required' => true, 'max' => 100],
                ],
                'list' => ['code', 'name'],
                'note' => 'Peran ditambah oleh developer bila ada fitur/aturan akses baru. Mahasiswa dikenali otomatis dari CIS.',
            ],
        ];
    }

    public static function get(string $key): array
    {
        $def = self::all()[$key] ?? abort(404);

        return $def + ['key' => $key];
    }

    /** Nilai tampilan sebuah kolom pada tabel daftar. */
    public static function tampil(array $field, mixed $nilai): string
    {
        return match ($field['type']) {
            'enum' => Label::of($field['options'], $nilai),
            'date' => tgl($nilai, 'd/m/Y'),
            default => (string) ($nilai ?? ''),
        };
    }
}
