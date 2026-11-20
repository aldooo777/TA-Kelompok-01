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
        if ($periode = $request->query('periode')) {
            $where[] = 'v.period_id = ?';
            $bind[] = (int) $periode;
        }
        if (in_array($pendaftaran = $request->query('pendaftaran'), ['internal', 'external'], true)) {
            $where[] = 'v.registration_type = ?';
            $bind[] = $pendaftaran;
        }

        switch ($request->query('status')) {
            case 'dibuka':
                $where[] = 'v.is_closed = false AND (v.registration_start_date IS NULL OR v.registration_start_date <= CURRENT_DATE) AND (v.registration_deadline IS NULL OR v.registration_deadline >= CURRENT_DATE)';
                break;
            case 'segera_ditutup':
                $where[] = "v.is_closed = false AND (v.registration_start_date IS NULL OR v.registration_start_date <= CURRENT_DATE) AND v.registration_deadline BETWEEN CURRENT_DATE AND CURRENT_DATE + INTERVAL '7 days'";
                break;
            case 'akan_dibuka':
                $where[] = 'v.is_closed = false AND v.registration_start_date IS NOT NULL AND v.registration_start_date > CURRENT_DATE';
                break;
            case 'ditutup':
                $where[] = '(v.is_closed = true OR (v.registration_deadline IS NOT NULL AND v.registration_deadline < CURRENT_DATE))';
                break;
        }

        if (($q = trim((string) $request->query('q'))) !== '') {
            $where[] = '(LOWER(a.title) LIKE ? OR LOWER(COALESCE(v.position, \'\')) LIKE ? OR LOWER(p.name) LIKE ?)';
            array_push($bind, ...array_fill(0, 3, '%' . mb_strtolower($q) . '%'));
        }

        $orderSql = match ($request->query('urut')) {
            'terdekat' => 'v.registration_deadline ASC NULLS LAST, a.published_at DESC NULLS LAST',
            'kuota'    => 'COALESCE(v.quota, 0) DESC, a.published_at DESC NULLS LAST',
            default    => 'a.published_at DESC NULLS LAST, v.id DESC',
        };

        $lowongan = Sql::paginate(
            Query::LOWONGAN . ' WHERE ' . implode(' AND ', $where) . '
             ORDER BY CASE WHEN v.is_closed = false
                            AND (v.registration_start_date IS NULL OR v.registration_start_date <= CURRENT_DATE)
                            AND (v.registration_deadline IS NULL OR v.registration_deadline >= CURRENT_DATE)
                           THEN 0 ELSE 1 END,
                      ' . $orderSql,
            $bind, 12
        );

        return view('publik.lowongan-index', [
            'portal' => self::diPortal(),
            'lowongan' => $lowongan,
            'jenis' => Sql::all('SELECT slug, name FROM activity_types WHERE is_active = true ORDER BY display_order, name'),
            'periode' => Sql::all('SELECT id, name FROM periods WHERE is_active = true ORDER BY start_date DESC NULLS LAST, id DESC'),
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

        $mitra = Sql::one('SELECT * FROM partners WHERE id = ?', [$lowongan->partner_id]);

        $lowonganLainnya = Sql::all(
            Query::LOWONGAN . ' WHERE ' . Query::LOWONGAN_TERBIT . ' AND v.id <> ?
             ORDER BY CASE WHEN v.activity_type_id = ? THEN 0 ELSE 1 END,
                      a.published_at DESC NULLS LAST
             LIMIT 3',
            [$lowongan->id, $lowongan->activity_type_id]
        );

        return view('publik.lowongan-show', [
            'portal' => self::diPortal(),
            'lowongan' => $lowongan,
            'mitra' => $mitra,
            'lowonganLainnya' => $lowonganLainnya,
            'lampiran' => self::lampiran($lowongan->announcement_id),
            'hasil' => Sql::all(
                'SELECT a.title, a.slug FROM announcements a WHERE ' . str_replace("a.type = 'news'", "a.type = 'result'", Query::BERITA_TERBIT) . ' AND a.source_announcement_id = ?',
                [$lowongan->announcement_id]
            ),
        ]);
    }

    public function berita(Request $request)
    {
        $bolehHasil = sprint(2);
        $kategori = $request->query('kategori', 'all');
        if (! $bolehHasil || ! in_array($kategori, ['all', 'berita', 'hasil'], true)) {
            $kategori = $bolehHasil ? 'all' : 'berita';
        }

        $basePublish = str_replace("a.type = 'news'", '1=1', Query::BERITA_TERBIT);
        $allowedTypes = $bolehHasil ? "a.type IN ('news', 'result')" : "a.type = 'news'";

        $where = [$basePublish];
        $bind = [];

        if ($kategori === 'berita') {
            $where[] = "a.type = 'news'";
        } elseif ($kategori === 'hasil') {
            $where[] = "a.type = 'result'";
        } else {
            $where[] = $allowedTypes;
        }

        if (($q = trim((string) $request->query('q'))) !== '') {
            $where[] = '(LOWER(a.title) LIKE ? OR LOWER(COALESCE(a.summary, \'\')) LIKE ? OR LOWER(COALESCE(a.content, \'\')) LIKE ?)';
            array_push($bind, ...array_fill(0, 3, '%' . mb_strtolower($q) . '%'));
        }

        if (($arsip = $request->query('arsip')) && preg_match('/^\d{4}-\d{2}$/', $arsip)) {
            $where[] = "TO_CHAR(COALESCE(a.published_at, a.created_at), 'YYYY-MM') = ?";
            $bind[] = $arsip;
        } else {
            $arsip = null;
        }

        $pengumuman = Sql::paginate(
            "SELECT a.*,
                    (SELECT COUNT(*) FROM announcement_attachments att WHERE att.announcement_id = a.id AND att.is_active = true) AS jumlah_lampiran,
                    (SELECT att.file_name FROM announcement_attachments att WHERE att.announcement_id = a.id AND att.is_active = true ORDER BY att.id LIMIT 1) AS nama_lampiran
             FROM announcements a
             WHERE " . implode(' AND ', $where) . '
             ORDER BY COALESCE(a.published_at, a.created_at) DESC NULLS LAST, a.id DESC',
            $bind,
            10
        );

        $jumlahKategori = Sql::one(
            "SELECT COUNT(*) FILTER (WHERE $allowedTypes) AS semua,
                    COUNT(*) FILTER (WHERE a.type = 'news') AS berita,
                    COUNT(*) FILTER (WHERE a.type = 'result') AS hasil
             FROM announcements a
             WHERE $basePublish AND $allowedTypes"
        );

        $terbaru = Sql::all(
            "SELECT id, type, title, slug, published_at, created_at
             FROM announcements a
             WHERE $basePublish AND $allowedTypes
             ORDER BY COALESCE(a.published_at, a.created_at) DESC NULLS LAST, a.id DESC
             LIMIT 5"
        );

        $arsipBulan = Sql::all(
            "SELECT TO_CHAR(COALESCE(a.published_at, a.created_at), 'YYYY-MM') AS ym, COUNT(*) AS total
             FROM announcements a
             WHERE $basePublish AND $allowedTypes
             GROUP BY ym
             ORDER BY ym DESC
             LIMIT 6"
        );

        return view('publik.berita-index', [
            'pengumuman' => $pengumuman,
            'kategori' => $kategori,
            'q' => $q ?? '',
            'arsip' => $arsip,
            'bolehHasil' => $bolehHasil,
            'jumlahKategori' => $jumlahKategori,
            'terbaru' => $terbaru,
            'arsipBulan' => $arsipBulan,
        ]);
    }

    private static function dataSidebarPengumuman(bool $bolehHasil): array
    {
        $basePublish = str_replace("a.type = 'news'", '1=1', Query::BERITA_TERBIT);
        $allowedTypes = $bolehHasil ? "a.type IN ('news', 'result')" : "a.type = 'news'";

        $jumlahKategori = Sql::one(
            "SELECT COUNT(*) FILTER (WHERE $allowedTypes) AS semua,
                    COUNT(*) FILTER (WHERE a.type = 'news') AS berita,
                    COUNT(*) FILTER (WHERE a.type = 'result') AS hasil
             FROM announcements a
             WHERE $basePublish AND $allowedTypes"
        );

        $terbaru = Sql::all(
            "SELECT id, type, title, slug, published_at, created_at
             FROM announcements a
             WHERE $basePublish AND $allowedTypes
             ORDER BY COALESCE(a.published_at, a.created_at) DESC NULLS LAST, a.id DESC
             LIMIT 5"
        );

        return [$basePublish, $allowedTypes, $jumlahKategori, $terbaru];
    }

    public function beritaDetail(string $slug)
    {
        $bolehHasil = sprint(2);
        $berita = Sql::oneOrFail('SELECT * FROM announcements a WHERE ' . Query::BERITA_TERBIT . ' AND a.slug = ?', [$slug]);
        [$basePublish, $allowedTypes, $jumlahKategori, $terbaru] = self::dataSidebarPengumuman($bolehHasil);

        $urutan = Sql::all(
            "SELECT id, type, title, slug
             FROM announcements a
             WHERE $basePublish AND $allowedTypes
             ORDER BY COALESCE(a.published_at, a.created_at) DESC NULLS LAST, a.id DESC"
        );
        $idx = $urutan->search(fn ($item) => $item->id === $berita->id);

        return view('publik.berita-show', [
            'berita' => $berita,
            'lampiran' => self::lampiran($berita->id),
            'lainnya' => Sql::all('SELECT * FROM announcements a WHERE ' . Query::BERITA_TERBIT . ' AND a.id <> ? ORDER BY COALESCE(a.published_at, a.created_at) DESC NULLS LAST LIMIT 3', [$berita->id]),
            'bolehHasil' => $bolehHasil,
            'jumlahKategori' => $jumlahKategori,
            'terbaru' => $terbaru,
            'sebelumnya' => ($idx !== false && $idx > 0) ? $urutan[$idx - 1] : null,
            'berikutnya' => ($idx !== false && $idx < $urutan->count() - 1) ? $urutan[$idx + 1] : null,
        ]);
    }

    public function hasilDetail(string $slug)
    {
        $bolehHasil = sprint(2);
        $hasil = Sql::oneOrFail(
            'SELECT a.*, src.title AS lowongan_title, src.slug AS lowongan_slug,
                    v.location AS lowongan_location, p.name AS partner_name
             FROM announcements a
             LEFT JOIN announcements src ON src.id = a.source_announcement_id
             LEFT JOIN vacancy_details v ON v.announcement_id = src.id
             LEFT JOIN partners p ON p.id = v.partner_id
             WHERE ' . str_replace("a.type = 'news'", "a.type = 'result'", Query::BERITA_TERBIT) . ' AND a.slug = ?',
            [$slug]
        );
        [$basePublish, $allowedTypes, $jumlahKategori, $terbaru] = self::dataSidebarPengumuman($bolehHasil);

        $urutan = Sql::all(
            "SELECT id, type, title, slug
             FROM announcements a
             WHERE $basePublish AND $allowedTypes
             ORDER BY COALESCE(a.published_at, a.created_at) DESC NULLS LAST, a.id DESC"
        );
        $idx = $urutan->search(fn ($item) => $item->id === $hasil->id);

        return view('publik.hasil-show', [
            'hasil' => $hasil,
            'items' => Sql::all(
                'SELECT i.*, sp.level || \' \' || sp.name AS prodi FROM announcement_result_items i
                 LEFT JOIN study_programs sp ON sp.id = i.study_program_id
                 WHERE i.announcement_id = ? AND i.is_active ORDER BY i.display_order, i.id',
                [$hasil->id]
            ),
            'lampiran' => self::lampiran($hasil->id),
            'bolehHasil' => $bolehHasil,
            'jumlahKategori' => $jumlahKategori,
            'terbaru' => $terbaru,
            'sebelumnya' => ($idx !== false && $idx > 0) ? $urutan[$idx - 1] : null,
            'berikutnya' => ($idx !== false && $idx < $urutan->count() - 1) ? $urutan[$idx + 1] : null,
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

    public function mitra(Request $request)
    {
        $where = ['p.is_active = true'];
        $bind = [];

        if (($q = trim((string) $request->query('q'))) !== '') {
            $where[] = '(LOWER(p.name) LIKE ? OR LOWER(COALESCE(p.field, \'\')) LIKE ? OR LOWER(COALESCE(p.address, \'\')) LIKE ?)';
            array_push($bind, ...array_fill(0, 3, '%' . mb_strtolower($q) . '%'));
        }

        if (($bidang = trim((string) $request->query('bidang'))) !== '') {
            $where[] = 'TRIM(p.field) = ?';
            $bind[] = $bidang;
        }

        $status = $request->query('status', 'all');
        if (in_array($status, ['cooperating', 'in_process', 'non_cooperating'], true)) {
            $where[] = 'p.cooperation_status = ?';
            $bind[] = $status;
        } else {
            $status = 'all';
        }

        $mitra = Sql::paginate(
            'SELECT p.* FROM partners p WHERE ' . implode(' AND ', $where) . ' ORDER BY p.name ASC',
            $bind,
            12
        );

        $partnerIds = $mitra->getCollection()->pluck('id')->map('intval')->all();
        $lowonganPerMitra = collect();
        if ($partnerIds !== []) {
            $inIds = implode(',', $partnerIds);
            $lowonganPerMitra = Sql::all(
                Query::LOWONGAN . ' WHERE ' . Query::LOWONGAN_TERBIT . "
                 AND v.partner_id IN ($inIds)
                 AND v.is_closed = false
                 AND (v.registration_start_date IS NULL OR v.registration_start_date <= CURRENT_DATE)
                 AND (v.registration_deadline IS NULL OR v.registration_deadline >= CURRENT_DATE)
                 ORDER BY a.published_at DESC NULLS LAST"
            )->groupBy('partner_id');
        }

        $stat = Sql::one(
            "SELECT COUNT(*) AS total,
                    COUNT(*) FILTER (WHERE cooperation_status = 'cooperating') AS cooperating,
                    COUNT(*) FILTER (WHERE cooperation_status = 'in_process') AS in_process,
                    COUNT(DISTINCT NULLIF(TRIM(field), '')) AS jumlah_bidang
             FROM partners
             WHERE is_active = true"
        );

        $bidangList = Sql::all(
            "SELECT DISTINCT TRIM(field) AS field
             FROM partners
             WHERE is_active = true AND NULLIF(TRIM(field), '') IS NOT NULL
             ORDER BY field"
        );

        return view('publik.mitra', [
            'mitra' => $mitra,
            'lowonganPerMitra' => $lowonganPerMitra,
            'stat' => $stat,
            'bidangList' => $bidangList,
            'q' => $q ?? '',
            'bidang' => $bidang ?? '',
            'status' => $status,
        ]);
    }

    public function galeri(Request $request)
    {
        $where = ["g.is_active = true", "g.status = 'published'"];
        $bind = [];

        $tahun = $request->query('tahun');
        if ($tahun !== null && $tahun !== '' && ctype_digit((string) $tahun)) {
            $where[] = 'EXTRACT(YEAR FROM g.event_date) = ?';
            $bind[] = (int) $tahun;
        } else {
            $tahun = '';
        }

        if (($q = trim((string) $request->query('q'))) !== '') {
            $where[] = '(LOWER(g.title) LIKE ? OR LOWER(COALESCE(g.description, \'\')) LIKE ?)';
            array_push($bind, ...array_fill(0, 2, '%' . mb_strtolower($q) . '%'));
        }

        $galeri = Sql::paginate(
            "SELECT g.*,
                    (SELECT COUNT(*) FROM gallery_items i WHERE i.gallery_id = g.id AND i.is_active = true AND i.media_type = 'image') AS jumlah_foto,
                    (SELECT COUNT(*) FROM gallery_items i WHERE i.gallery_id = g.id AND i.is_active = true AND i.media_type = 'video') AS jumlah_video
             FROM galleries g
             WHERE " . implode(' AND ', $where) . '
             ORDER BY g.event_date DESC NULLS LAST, g.id DESC',
            $bind,
            9
        );

        $daftarTahun = Sql::all(
            "SELECT DISTINCT EXTRACT(YEAR FROM event_date)::int AS tahun
             FROM galleries
             WHERE is_active = true AND status = 'published' AND event_date IS NOT NULL
             ORDER BY tahun DESC"
        );

        return view('publik.galeri-index', [
            'galeri' => $galeri,
            'daftarTahun' => $daftarTahun,
            'tahun' => (string) $tahun,
            'q' => $q ?? '',
        ]);
    }

    public function galeriDetail(int $id)
    {
        $galeri = Sql::oneOrFail("SELECT * FROM galleries WHERE id = ? AND is_active = true AND status = 'published'", [$id]);
        $items = Sql::all('SELECT * FROM gallery_items WHERE gallery_id = ? AND is_active = true ORDER BY display_order, id', [$id]);

        $lainnya = Sql::all(
            "SELECT g.*,
                    (SELECT COUNT(*) FROM gallery_items i WHERE i.gallery_id = g.id AND i.is_active = true AND i.media_type = 'image') AS jumlah_foto,
                    (SELECT COUNT(*) FROM gallery_items i WHERE i.gallery_id = g.id AND i.is_active = true AND i.media_type = 'video') AS jumlah_video
             FROM galleries g
             WHERE g.is_active = true AND g.status = 'published' AND g.id <> ?
             ORDER BY g.event_date DESC NULLS LAST, g.id DESC
             LIMIT 3",
            [$id]
        );

        return view('publik.galeri-show', [
            'galeri' => $galeri,
            'items' => $items,
            'lainnya' => $lainnya,
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

    public function strukturOrganisasi()
    {
        return view('publik.struktur-organisasi', [
            'struktur' => Sql::all('SELECT * FROM organization_positions WHERE is_active = true ORDER BY display_order, id'),
        ]);
    }

    public function jenisPdlk()
    {
        return view('publik.jenis-pdlk', [
            'jenisKegiatan' => Sql::all('SELECT * FROM activity_types WHERE is_active = true ORDER BY display_order, name'),
            'jumlahMitra' => (int) Sql::value('SELECT COUNT(*) FROM partners WHERE is_active = true'),
        ]);
    }

    public function jenisPdlkDetail(string $slug)
    {
        $jenis = Sql::oneOrFail('SELECT * FROM activity_types WHERE is_active = true AND slug = ?', [$slug]);
        $semuaJenis = Sql::all('SELECT * FROM activity_types WHERE is_active = true ORDER BY display_order, name');

        $lowonganTerkait = Sql::all(
            Query::LOWONGAN . ' WHERE ' . Query::LOWONGAN_TERBIT . ' AND v.activity_type_id = ?
             ORDER BY CASE WHEN v.is_closed = false AND (v.registration_deadline IS NULL OR v.registration_deadline >= CURRENT_DATE) THEN 0 ELSE 1 END,
                      a.published_at DESC NULLS LAST
             LIMIT 3',
            [$jenis->id]
        );

        $jumlahLowonganAktif = (int) Sql::value(
            'SELECT COUNT(*) FROM vacancy_details v
             JOIN announcements a ON a.id = v.announcement_id
             WHERE ' . Query::LOWONGAN_TERBIT . '
               AND v.activity_type_id = ?
               AND v.is_closed = false
               AND (v.registration_start_date IS NULL OR v.registration_start_date <= CURRENT_DATE)
               AND (v.registration_deadline IS NULL OR v.registration_deadline >= CURRENT_DATE)',
            [$jenis->id]
        );

        $jumlahPeserta = (int) Sql::value(
            "SELECT COUNT(*) FROM pdlk_participants WHERE activity_type_id = ? AND is_active = true AND status = 'active'",
            [$jenis->id]
        );

        return view('publik.jenis-pdlk-show', [
            'jenis' => $jenis,
            'semuaJenis' => $semuaJenis,
            'lowonganTerkait' => $lowonganTerkait,
            'jumlahLowonganAktif' => $jumlahLowonganAktif,
            'jumlahPeserta' => $jumlahPeserta,
        ]);
    }

    public function rekapitulasi(Request $request)
    {
        $tahun = trim((string) $request->query('tahun', ''));
        $semester = trim((string) $request->query('semester', ''));

        $where = ["pp.is_active = true", "pp.status IN ('active', 'completed')"];
        $bind = [];

        if ($tahun !== '') {
            $where[] = "TRIM(COALESCE(pr.academic_year, '')) = ?";
            $bind[] = $tahun;
        }
        if ($semester !== '') {
            $where[] = "LOWER(TRIM(COALESCE(pr.semester, ''))) = LOWER(?)";
            $bind[] = $semester;
        }

        $whereSql = implode(' AND ', $where);

        $ringkasan = Sql::one(
            "SELECT COUNT(*) AS total,
                    COUNT(*) FILTER (WHERE pp.program_category = 'flagship') AS flagship,
                    COUNT(*) FILTER (WHERE pp.program_category = 'independent') AS mandiri,
                    COUNT(DISTINCT pp.partner_id) AS mitra_terlibat,
                    COUNT(DISTINCT pp.study_program_id) AS prodi_terlibat
             FROM pdlk_participants pp
             LEFT JOIN periods pr ON pr.id = pp.period_id
             WHERE $whereSql",
            $bind
        );

        $perJenis = Sql::all(
            "SELECT t.id, t.name, t.slug, COALESCE(cnt.total, 0) AS total
             FROM activity_types t
             LEFT JOIN (
                 SELECT pp.activity_type_id, COUNT(*) AS total
                 FROM pdlk_participants pp
                 LEFT JOIN periods pr ON pr.id = pp.period_id
                 WHERE $whereSql
                 GROUP BY pp.activity_type_id
             ) cnt ON cnt.activity_type_id = t.id
             WHERE t.is_active = true
             ORDER BY COALESCE(cnt.total, 0) DESC, t.display_order ASC, t.name ASC",
            $bind
        );

        $trenPeriode = Sql::all(
            "SELECT pr.id, pr.name,
                    COUNT(pp.id) AS total,
                    COUNT(pp.id) FILTER (WHERE pp.program_category = 'flagship') AS flagship,
                    COUNT(pp.id) FILTER (WHERE pp.program_category = 'independent') AS mandiri
             FROM periods pr
             LEFT JOIN pdlk_participants pp
                    ON pp.period_id = pr.id AND pp.is_active = true AND pp.status IN ('active', 'completed')
             WHERE pr.is_active = true
             GROUP BY pr.id, pr.name, pr.start_date
             ORDER BY pr.start_date DESC NULLS LAST, pr.id DESC
             LIMIT 6"
        )->reverse()->values();

        $prodiList = Sql::all('SELECT id, code, level, name FROM study_programs WHERE is_active = true ORDER BY level, name');
        $jenisList = Sql::all('SELECT id, name, slug FROM activity_types WHERE is_active = true ORDER BY display_order, name');

        $agregasiProdi = Sql::all(
            "SELECT pp.study_program_id, pp.activity_type_id, COUNT(*) AS total
             FROM pdlk_participants pp
             LEFT JOIN periods pr ON pr.id = pp.period_id
             WHERE $whereSql AND pp.study_program_id IS NOT NULL AND pp.activity_type_id IS NOT NULL
             GROUP BY pp.study_program_id, pp.activity_type_id",
            $bind
        );

        $matriksProdi = [];
        foreach ($agregasiProdi as $row) {
            $matriksProdi[$row->study_program_id][$row->activity_type_id] = (int) $row->total;
        }

        $topMitra = Sql::all(
            "SELECT p.id, p.name, p.field, COUNT(pp.id) AS total
             FROM pdlk_participants pp
             JOIN partners p ON p.id = pp.partner_id
             LEFT JOIN periods pr ON pr.id = pp.period_id
             WHERE $whereSql AND p.is_active = true
             GROUP BY p.id, p.name, p.field
             ORDER BY total DESC, p.name ASC
             LIMIT 10",
            $bind
        );

        $daftarTahun = Sql::all(
            "SELECT DISTINCT TRIM(academic_year) AS academic_year
             FROM periods
             WHERE is_active = true AND NULLIF(TRIM(academic_year), '') IS NOT NULL
             ORDER BY academic_year DESC"
        );

        $daftarSemester = Sql::all(
            "SELECT DISTINCT TRIM(semester) AS semester
             FROM periods
             WHERE is_active = true AND NULLIF(TRIM(semester), '') IS NOT NULL
             ORDER BY semester ASC"
        );

        return view('publik.rekapitulasi', [
            'tahun' => $tahun,
            'semester' => $semester,
            'daftarTahun' => $daftarTahun,
            'daftarSemester' => $daftarSemester,
            'ringkasan' => $ringkasan,
            'perJenis' => $perJenis,
            'trenPeriode' => $trenPeriode,
            'prodiList' => $prodiList,
            'jenisList' => $jenisList,
            'matriksProdi' => $matriksProdi,
            'topMitra' => $topMitra,
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
