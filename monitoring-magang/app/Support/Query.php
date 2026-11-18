<?php

namespace App\Support;

/** Potongan query SQL yang dipakai di beberapa halaman. */
class Query
{
    /** Lowongan: vacancy_details + announcements + partners + activity_types + periods. */
    public const LOWONGAN = "
        SELECT v.id, v.announcement_id, v.partner_id, v.activity_type_id, v.period_id,
               v.position, v.location, v.quota, v.criteria, v.registration_type, v.external_url,
               v.registration_start_date, v.registration_deadline, v.is_closed, v.created_at,
               a.title, a.slug, a.summary, a.content, a.cover_image, a.status, a.published_at, a.expired_at,
               p.name AS partner_name, t.name AS activity_name, t.slug AS activity_slug,
               pr.name AS period_name
        FROM vacancy_details v
        JOIN announcements a ON a.id = v.announcement_id
        JOIN partners p ON p.id = v.partner_id
        JOIN activity_types t ON t.id = v.activity_type_id
        LEFT JOIN periods pr ON pr.id = v.period_id";

    /**
     * Syarat lowongan tampil di halaman publik.
     * clock_timestamp() = jam saat ini (bukan jam awal transaksi seperti CURRENT_TIMESTAMP),
     * dalam zona waktu koneksi (Asia/Jakarta, lihat config/database.php).
     */
    public const LOWONGAN_TERBIT = "
        v.is_active = true AND a.is_active = true AND a.type = 'vacancy' AND a.status = 'published'
        AND (a.published_at IS NULL OR a.published_at <= clock_timestamp()::timestamp)
        AND (a.expired_at IS NULL OR a.expired_at > clock_timestamp()::timestamp)";

    /** Syarat berita tampil di halaman publik. */
    public const BERITA_TERBIT = "
        a.is_active = true AND a.type = 'news' AND a.status = 'published'
        AND (a.published_at IS NULL OR a.published_at <= clock_timestamp()::timestamp)
        AND (a.expired_at IS NULL OR a.expired_at > clock_timestamp()::timestamp)";

    /** Peserta PDLK + prodi + jenis kegiatan + mitra + periode. */
    public const PESERTA = "
        SELECT pp.*, sp.code AS prodi_code, sp.level AS prodi_level, sp.name AS prodi_name,
               t.name AS activity_name, p.name AS partner_name, pr.name AS period_name
        FROM pdlk_participants pp
        LEFT JOIN study_programs sp ON sp.id = pp.study_program_id
        LEFT JOIN activity_types t ON t.id = pp.activity_type_id
        LEFT JOIN partners p ON p.id = pp.partner_id
        LEFT JOIN periods pr ON pr.id = pp.period_id";

    /** Pendaftaran + lowongan + prodi. */
    public const PENDAFTARAN = "
        SELECT ap.*, a.title AS vacancy_title, a.slug AS vacancy_slug, p.name AS partner_name,
               t.name AS activity_name, sp.code AS prodi_code, sp.level AS prodi_level, sp.name AS prodi_name
        FROM applications ap
        JOIN vacancy_details v ON v.id = ap.vacancy_id
        JOIN announcements a ON a.id = v.announcement_id
        JOIN partners p ON p.id = v.partner_id
        JOIN activity_types t ON t.id = v.activity_type_id
        LEFT JOIN study_programs sp ON sp.id = ap.study_program_id";

    public static function opsi(string $table, string $label = 'name', string $order = 'name'): \Illuminate\Support\Collection
    {
        return Sql::all("SELECT id, $label AS label FROM $table WHERE is_active = true ORDER BY $order");
    }

    public static function opsiProdi(?array $hanyaId = null): \Illuminate\Support\Collection
    {
        $where = $hanyaId !== null ? ' AND id IN (' . (implode(',', array_map('intval', $hanyaId)) ?: '0') . ')' : '';

        return Sql::all("SELECT id, level || ' ' || name AS label FROM study_programs WHERE is_active = true$where ORDER BY level, name");
    }

    /** Cari mitra berdasarkan nama; buat baru bila belum ada. Mengembalikan id. */
    public static function mitraDariNama(string $nama): int
    {
        $nama = trim(preg_replace('/\s+/', ' ', $nama));
        $id = Sql::value('SELECT id FROM partners WHERE LOWER(name) = LOWER(?)', [$nama]);

        return $id ? (int) $id : Sql::insert('partners', ['name' => $nama]);
    }

    public static function slugUnik(string $judul): string
    {
        $dasar = \Illuminate\Support\Str::slug($judul) ?: 'pengumuman';
        $slug = $dasar;
        $i = 2;

        while (Sql::value('SELECT EXISTS (SELECT 1 FROM announcements WHERE slug = ?)', [$slug])) {
            $slug = $dasar . '-' . $i++;
        }

        return $slug;
    }
}
