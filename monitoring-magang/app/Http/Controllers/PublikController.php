<?php

namespace App\Http\Controllers;

use App\Support\Query;
use App\Support\Sql;
use Illuminate\Http\Request;

/**
 * Halaman yang dapat dibuka tanpa login (Guest).
 * Aksi lanjutan seperti mendaftar diarahkan ke login CIS.
 */
class PublikController extends Controller
{
    public function beranda()
    {
        $periodeTren = Sql::all(
            "SELECT pr.id, pr.name, COUNT(pp.id) AS total
             FROM periods pr
             LEFT JOIN pdlk_participants pp ON pp.period_id = pr.id AND pp.is_active = true
             WHERE pr.is_active = true
             GROUP BY pr.id, pr.name, pr.start_date
             ORDER BY pr.start_date DESC NULLS LAST, pr.id DESC
             LIMIT 4"
        )->reverse()->values();

        $pesertaAktif = Sql::one(
            "SELECT COUNT(*) AS total,
                    COUNT(*) FILTER (WHERE program_category = 'flagship') AS flagship,
                    COUNT(*) FILTER (WHERE program_category = 'independent') AS independent
             FROM pdlk_participants
             WHERE is_active = true AND status = 'active'"
        );

        $beritaItems = Sql::all(
            "SELECT a.*, 'berita' AS kategori_tab
             FROM announcements a
             WHERE " . Query::BERITA_TERBIT . "
             ORDER BY a.published_at DESC NULLS LAST LIMIT 5"
        );

        $hasilItems = sprint(2)
            ? Sql::all(
                "SELECT a.*, 'seleksi' AS kategori_tab
                 FROM announcements a
                 WHERE " . str_replace("a.type = 'news'", "a.type = 'result'", Query::BERITA_TERBIT) . "
                 ORDER BY a.published_at DESC NULLS LAST LIMIT 5"
            )
            : collect();

        $pengumuman = $beritaItems->merge($hasilItems)
            ->sortByDesc('published_at')
            ->take(5)
            ->values();

        $mitraStat = Sql::one(
            "SELECT COUNT(*) AS total,
                    COUNT(*) FILTER (WHERE cooperation_status = 'cooperating') AS cooperating,
                    COUNT(*) FILTER (WHERE cooperation_status = 'in_process') AS in_process
             FROM partners WHERE is_active = true"
        );

        return view('publik.beranda', [
            'lowongan' => Sql::all(Query::LOWONGAN . ' WHERE ' . Query::LOWONGAN_TERBIT . ' ORDER BY a.published_at DESC NULLS LAST, v.id DESC LIMIT 3'),
            'pengumuman' => $pengumuman,
            'jenisKegiatan' => Sql::all('SELECT * FROM activity_types WHERE is_active = true ORDER BY display_order, name'),
            'struktur' => Sql::all('SELECT * FROM organization_positions WHERE is_active = true ORDER BY display_order, position_name LIMIT 6'),
            'mitra' => Sql::all('SELECT * FROM partners WHERE is_active = true ORDER BY name LIMIT 8'),
            'mitraStat' => $mitraStat,
            'galeri' => Sql::all("SELECT * FROM galleries WHERE is_active = true AND status = 'published' ORDER BY event_date DESC NULLS LAST, id DESC LIMIT 6"),
            'periodeTren' => $periodeTren,
            'pesertaAktif' => $pesertaAktif,
            'statistik' => [
                'lowongan' => (int) Sql::value(
                    'SELECT COUNT(*) FROM vacancy_details v JOIN announcements a ON a.id = v.announcement_id WHERE ' . Query::LOWONGAN_TERBIT . '
                     AND v.is_closed = false
                     AND (v.registration_start_date IS NULL OR v.registration_start_date <= CURRENT_DATE)
                     AND (v.registration_deadline IS NULL OR v.registration_deadline >= CURRENT_DATE)'
                ),
                'mitra' => (int) ($mitraStat->total ?? 0),
                'bentuk' => (int) Sql::value('SELECT COUNT(*) FROM activity_types WHERE is_active = true'),
                'peserta' => (int) ($pesertaAktif->total ?? 0),
            ],
        ]);
    }

    public function lowongan(Request $request)
    {
        $where = [Query::LOWONGAN_TERBIT];
        $bind = [];

        if ($slug = $request->query('jenis')) {
            $where[] = 't.slug = ?';
            $bind[] = $slug;
        }
        if (($q = trim((string) $request->query('q'))) !== '') {
            $where[] = '(LOWER(a.title) LIKE ? OR LOWER(COALESCE(v.position, \'\')) LIKE ? OR LOWER(p.name) LIKE ?)';
            array_push($bind, ...array_fill(0, 3, '%' . mb_strtolower($q) . '%'));
        }

        $lowongan = Sql::paginate(
            Query::LOWONGAN . ' WHERE ' . implode(' AND ', $where) . '
             ORDER BY CASE WHEN v.is_closed = false AND (v.registration_deadline IS NULL OR v.registration_deadline >= CURRENT_DATE) THEN 0 ELSE 1 END,
                      a.published_at DESC NULLS LAST',
            $bind, 12
        );

        return view('publik.lowongan-index', [
            'portal' => self::diPortal(),
            'lowongan' => $lowongan,
            'jenis' => Sql::all('SELECT slug, name FROM activity_types WHERE is_active = true ORDER BY display_order'),
            'q' => $q ?? '',
        ]);
    }

    /**
     * Halaman lowongan juga dibuka dari portal (sudah login) agar pengguna tetap
     * berada di tampilan dashboard; isinya sama dengan halaman publik.
     */
    private static function diPortal(): bool
    {
        return request()->routeIs('portal.lowongan.*');
    }

    public function lowonganDetail(string $slug)
    {
        $lowongan = self::cariLowongan($slug);

        return view('publik.lowongan-show', [
            'portal' => self::diPortal(),
            'lowongan' => $lowongan,
            'lampiran' => self::lampiran($lowongan->announcement_id),
            'hasil' => Sql::all(
                'SELECT a.title, a.slug FROM announcements a WHERE ' . str_replace("a.type = 'news'", "a.type = 'result'", Query::BERITA_TERBIT) . ' AND a.source_announcement_id = ?',
                [$lowongan->announcement_id]
            ),
        ]);
    }

    public function berita()
    {
        return view('publik.berita-index', [
            'berita' => Sql::paginate('SELECT * FROM announcements a WHERE ' . Query::BERITA_TERBIT . ' ORDER BY a.published_at DESC NULLS LAST', [], 12),
        ]);
    }

    public function beritaDetail(string $slug)
    {
        $berita = Sql::oneOrFail('SELECT * FROM announcements a WHERE ' . Query::BERITA_TERBIT . ' AND a.slug = ?', [$slug]);

        return view('publik.berita-show', [
            'berita' => $berita,
            'lampiran' => self::lampiran($berita->id),
            'lainnya' => Sql::all('SELECT * FROM announcements a WHERE ' . Query::BERITA_TERBIT . ' AND a.id <> ? ORDER BY a.published_at DESC LIMIT 3', [$berita->id]),
        ]);
    }

    public function hasil()
    {
        return view('publik.hasil-index', [
            'hasil' => Sql::paginate(
                "SELECT a.*, (SELECT COUNT(*) FROM announcement_result_items i WHERE i.announcement_id = a.id AND i.is_active) AS jumlah
                 FROM announcements a WHERE " . str_replace("a.type = 'news'", "a.type = 'result'", Query::BERITA_TERBIT) . "
                 ORDER BY a.published_at DESC NULLS LAST", [], 12
            ),
        ]);
    }

    public function hasilDetail(string $slug)
    {
        $hasil = Sql::oneOrFail(
            'SELECT a.*, src.title AS lowongan_title, src.slug AS lowongan_slug FROM announcements a
             LEFT JOIN announcements src ON src.id = a.source_announcement_id
             WHERE ' . str_replace("a.type = 'news'", "a.type = 'result'", Query::BERITA_TERBIT) . ' AND a.slug = ?', [$slug]
        );

        return view('publik.hasil-show', [
            'hasil' => $hasil,
            'items' => Sql::all(
                'SELECT i.*, sp.level || \' \' || sp.name AS prodi FROM announcement_result_items i
                 LEFT JOIN study_programs sp ON sp.id = i.study_program_id
                 WHERE i.announcement_id = ? AND i.is_active ORDER BY i.display_order, i.id', [$hasil->id]
            ),
            'lampiran' => self::lampiran($hasil->id),
        ]);
    }

    public function galeri()
    {
        return view('publik.galeri-index', [
            'galeri' => Sql::paginate(
                "SELECT g.*, (SELECT COUNT(*) FROM gallery_items i WHERE i.gallery_id = g.id AND i.is_active) AS jumlah_item
                 FROM galleries g WHERE g.is_active = true AND g.status = 'published'
                 ORDER BY g.event_date DESC NULLS LAST, g.id DESC", [], 12
            ),
        ]);
    }

    public function galeriDetail(int $id)
    {
        return view('publik.galeri-show', [
            'galeri' => Sql::oneOrFail("SELECT * FROM galleries WHERE id = ? AND is_active = true AND status = 'published'", [$id]),
            'items' => Sql::all('SELECT * FROM gallery_items WHERE gallery_id = ? AND is_active = true ORDER BY display_order, id', [$id]),
        ]);
    }

    public function tentang()
    {
        return view('publik.tentang', [
            'jenisKegiatan' => Sql::all('SELECT * FROM activity_types WHERE is_active = true ORDER BY display_order, name'),
            'struktur' => Sql::all('SELECT * FROM organization_positions WHERE is_active = true ORDER BY display_order, position_name'),
            'kontak' => Sql::all('SELECT * FROM contacts WHERE is_active = true ORDER BY display_order, name'),
        ]);
    }

    public static function cariLowongan(string $slug): object
    {
        return Sql::oneOrFail(Query::LOWONGAN . ' WHERE ' . Query::LOWONGAN_TERBIT . ' AND a.slug = ?', [$slug]);
    }

    public static function lampiran(int $announcementId): \Illuminate\Support\Collection
    {
        return Sql::all('SELECT * FROM announcement_attachments WHERE announcement_id = ? AND is_active = true ORDER BY id', [$announcementId]);
    }
}
