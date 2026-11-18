<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Support\Lampiran;
use App\Support\Query;
use App\Support\Sql;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Pengumuman hasil seleksi: announcements (type = 'result') yang mengacu ke
 * lowongan asal (source_announcement_id) + daftar mahasiswa yang lulus
 * (announcement_result_items).
 */
class HasilController extends Controller
{
    public function index()
    {
        return view('kelola.hasil.index', [
            'hasil' => Sql::paginate(
                "SELECT a.*, src.title AS lowongan_title,
                        (SELECT COUNT(*) FROM announcement_result_items i WHERE i.announcement_id = a.id AND i.is_active) AS jumlah
                 FROM announcements a
                 LEFT JOIN announcements src ON src.id = a.source_announcement_id
                 WHERE a.type = 'result' AND a.is_active
                 ORDER BY a.created_at DESC", [], 15
            ),
        ]);
    }

    public function create(Request $request)
    {
        $sumber = $request->query('lowongan') ? $this->lowonganAsal((int) $request->query('lowongan')) : null;

        return view('kelola.hasil.form', [
            'hasil' => null,
            'items' => $sumber ? $this->lulusDariPendaftaran($sumber->id) : collect(),
            'sumber' => $sumber,
            'lampiran' => collect(),
        ] + $this->opsi());
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        $id = Sql::transaction(function () use ($request, $data) {
            $id = Sql::insert('announcements', $data['pengumuman'] + [
                'type' => 'result',
                'slug' => Query::slugUnik($data['pengumuman']['title']),
                'published_at' => $data['pengumuman']['status'] === 'published' ? now() : null,
            ]);
            $this->simpanItem($id, $data['items']);
            Lampiran::simpan($request, $id);

            return $id;
        });

        return redirect()->route('kelola.hasil.edit', $id)->with('success', 'Pengumuman hasil disimpan.');
    }

    public function edit(int $id)
    {
        $hasil = $this->cari($id);

        return view('kelola.hasil.form', [
            'hasil' => $hasil,
            'items' => Sql::all('SELECT * FROM announcement_result_items WHERE announcement_id = ? AND is_active ORDER BY display_order, id', [$id]),
            'sumber' => $hasil->source_announcement_id ? Sql::one('SELECT id, title FROM announcements WHERE id = ?', [$hasil->source_announcement_id]) : null,
            'lampiran' => Lampiran::daftar($id),
        ] + $this->opsi());
    }

    public function update(Request $request, int $id)
    {
        $hasil = $this->cari($id);
        $data = $this->validasi($request);

        if ($data['pengumuman']['status'] === 'published' && !$hasil->published_at) {
            $data['pengumuman']['published_at'] = now();
        }

        Sql::transaction(function () use ($request, $data, $id) {
            Sql::update('announcements', $id, $data['pengumuman']);
            DB::delete('DELETE FROM announcement_result_items WHERE announcement_id = ?', [$id]);
            $this->simpanItem($id, $data['items']);
            Lampiran::simpan($request, $id);
        });

        return back()->with('success', 'Pengumuman hasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $this->cari($id);
        Sql::update('announcements', $id, ['status' => 'archived', 'is_active' => false]);

        return redirect()->route('kelola.hasil.index')->with('success', 'Pengumuman hasil dihapus dari daftar.');
    }

    private function cari(int $id): object
    {
        return Sql::oneOrFail("SELECT * FROM announcements WHERE id = ? AND type = 'result'", [$id]);
    }

    private function lowonganAsal(int $announcementId): object
    {
        return Sql::oneOrFail(
            "SELECT a.id, a.title, v.id AS vacancy_id, v.registration_type FROM announcements a
             JOIN vacancy_details v ON v.announcement_id = a.id WHERE a.id = ?", [$announcementId]
        );
    }

    /** Untuk lowongan internal: isi otomatis dari pendaftar berstatus Lulus. */
    private function lulusDariPendaftaran(int $announcementId): \Illuminate\Support\Collection
    {
        return Sql::all(
            "SELECT ap.name AS student_name, ap.nim, ap.study_program_id
             FROM applications ap JOIN vacancy_details v ON v.id = ap.vacancy_id
             WHERE v.announcement_id = ? AND ap.status = 'passed' AND ap.is_active
             ORDER BY ap.name", [$announcementId]
        );
    }

    private function opsi(): array
    {
        return [
            'lowonganList' => Sql::all(
                "SELECT a.id, a.title || CASE v.registration_type WHEN 'external' THEN ' (eksternal)' ELSE '' END AS label
                 FROM announcements a JOIN vacancy_details v ON v.announcement_id = a.id
                 WHERE a.type = 'vacancy' ORDER BY a.created_at DESC"
            ),
            'prodiList' => Query::opsiProdi(),
        ];
    }

    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'summary' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:10000'],
            'source_announcement_id' => ['nullable', 'exists:announcements,id'],
            'items' => ['nullable', 'array', 'max:500'],
            'items.*.nim' => ['nullable', 'string', 'max:30'],
            'items.*.student_name' => ['nullable', 'string', 'max:150'],
            'items.*.study_program_id' => ['nullable', 'exists:study_programs,id'],
        ] + Lampiran::RULES);

        return [
            'pengumuman' => [
                'title' => $data['title'],
                'summary' => $data['summary'] ?? null,
                'content' => $data['content'] ?? '',
                'source_announcement_id' => $data['source_announcement_id'] ?? null,
                'status' => $request->boolean('terbit') ? 'published' : 'draft',
            ],
            'items' => collect($data['items'] ?? [])->filter(fn ($i) => trim((string) ($i['student_name'] ?? '')) !== '')->values()->all(),
        ];
    }

    private function simpanItem(int $announcementId, array $items): void
    {
        foreach ($items as $n => $i) {
            Sql::insert('announcement_result_items', [
                'announcement_id' => $announcementId,
                'student_name' => trim($i['student_name']),
                'nim' => trim((string) ($i['nim'] ?? '')),
                'study_program_id' => $i['study_program_id'] ?? null,
                'display_order' => $n + 1,
            ]);
        }
    }
}
