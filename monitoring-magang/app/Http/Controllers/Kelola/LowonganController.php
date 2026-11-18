<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Support\Label;
use App\Support\Lampiran;
use App\Support\Query;
use App\Support\Sql;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Lowongan PDLK oleh Staf UPT, query SQL langsung:
 * announcements (type = 'vacancy') + vacancy_details + vacancy_additional_fields.
 */
class LowonganController extends Controller
{
    public function index()
    {
        $lowongan = Sql::paginate(
            "SELECT x.*,
                    (SELECT COUNT(*) FROM applications ap WHERE ap.vacancy_id = x.id AND ap.is_active) AS jumlah_pendaftar,
                    (SELECT COUNT(*) FROM applications ap WHERE ap.vacancy_id = x.id AND ap.is_active AND ap.status = 'registered') AS jumlah_baru
             FROM (" . Query::LOWONGAN . " WHERE v.is_active = true) AS x
             ORDER BY x.created_at DESC",
            [], 15
        );

        return view('kelola.lowongan.index', compact('lowongan'));
    }

    public function create()
    {
        return view('kelola.lowongan.form', $this->dataForm(null));
    }

    public function store(Request $request)
    {
        [$pengumuman, $detail] = $this->validasi($request);

        Sql::transaction(function () use ($request, $pengumuman, $detail) {
            $aId = Sql::insert('announcements', $pengumuman + [
                'type' => 'vacancy',
                'slug' => Query::slugUnik($pengumuman['title']),
                'published_at' => $pengumuman['status'] === 'published' ? now() : null,
            ]);
            $vId = Sql::insert('vacancy_details', $detail + ['announcement_id' => $aId]);
            $this->simpanFieldTambahan($vId, $request->input('field_tambahan', []));
            Lampiran::simpan($request, $aId);
        });

        return redirect()->route('kelola.lowongan.index')->with('success', 'Lowongan berhasil disimpan.');
    }

    public function edit(int $id)
    {
        return view('kelola.lowongan.form', $this->dataForm($this->cari($id)));
    }

    public function update(Request $request, int $id)
    {
        $lowongan = $this->cari($id);
        [$pengumuman, $detail] = $this->validasi($request, $lowongan);

        if ($pengumuman['status'] === 'published' && !$lowongan->published_at) {
            $pengumuman['published_at'] = now();
        }

        Sql::transaction(function () use ($request, $pengumuman, $detail, $lowongan) {
            Sql::update('announcements', $lowongan->announcement_id, $pengumuman);
            Sql::update('vacancy_details', $lowongan->id, $detail);
            $this->simpanFieldTambahan($lowongan->id, $request->input('field_tambahan', []));
            Lampiran::simpan($request, $lowongan->announcement_id);
        });

        return redirect()->route('kelola.lowongan.index')->with('success', 'Lowongan berhasil diperbarui.');
    }

    /** Diarsipkan agar data pendaftar tetap utuh. */
    public function destroy(int $id)
    {
        $lowongan = $this->cari($id);

        Sql::transaction(function () use ($lowongan) {
            Sql::update('vacancy_details', $lowongan->id, ['is_active' => false, 'is_closed' => true]);
            Sql::update('announcements', $lowongan->announcement_id, ['status' => 'archived', 'is_active' => false]);
        });

        return back()->with('success', 'Lowongan diarsipkan dan tidak tampil lagi di halaman publik.');
    }

    private function cari(int $id): object
    {
        return Sql::oneOrFail(Query::LOWONGAN . ' WHERE v.id = ?', [$id]);
    }

    private function dataForm(?object $lowongan): array
    {
        return [
            'lowongan' => $lowongan,
            'fields' => $lowongan
                ? Sql::all('SELECT label, field_type FROM vacancy_additional_fields WHERE vacancy_id = ? AND is_active = true ORDER BY display_order', [$lowongan->id])
                : collect(),
            'lampiran' => $lowongan ? Lampiran::daftar($lowongan->announcement_id) : collect(),
            'jenisKegiatan' => Query::opsi('activity_types', 'name', 'display_order, name'),
            'periodeList' => Query::opsi('periods', 'name', 'start_date DESC'),
            'mitraList' => Sql::all('SELECT name FROM partners WHERE is_active = true ORDER BY name')->pluck('name'),
        ];
    }

    /** @return array{0: array, 1: array} kolom announcements dan vacancy_details */
    private function validasi(Request $request, ?object $lowongan = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'summary' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:10000'],
            'nama_mitra' => ['required', 'string', 'max:200'],
            'activity_type_id' => ['required', 'exists:activity_types,id'],
            'period_id' => ['nullable', 'exists:periods,id'],
            'position' => ['nullable', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:150'],
            'criteria' => ['required', 'string', 'max:10000'],
            'quota' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'registration_type' => ['required', Rule::in(array_keys(Label::REGISTRATION_TYPE))],
            'external_url' => ['nullable', 'required_if:registration_type,external', 'url', 'max:255'],
            'registration_start_date' => ['nullable', 'date'],
            'registration_deadline' => ['nullable', 'date', 'after_or_equal:registration_start_date'],
            'expired_at' => ['nullable', 'date'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'field_tambahan' => ['nullable', 'array', 'max:10'],
            'field_tambahan.*.label' => ['nullable', 'string', 'max:150'],
            'field_tambahan.*.field_type' => ['nullable', Rule::in(array_keys(Label::FIELD_TYPE))],
        ] + Lampiran::RULES, [
            'registration_deadline.after_or_equal' => 'Batas pendaftaran tidak boleh sebelum tanggal buka.',
            'nama_mitra.required' => 'Nama mitra wajib diisi.',
            'external_url.required_if' => 'Tautan pendaftaran mitra wajib diisi untuk pendaftaran eksternal.',
        ]);

        $pengumuman = [
            'title' => $data['title'],
            'summary' => $data['summary'] ?? null,
            'content' => $data['content'],
            'expired_at' => $data['expired_at'] ?? null,
            'status' => $request->boolean('terbit') ? 'published' : 'draft',
        ];

        $cover = $lowongan?->cover_image;
        if ($request->hasFile('cover')) {
            if ($cover) {
                Storage::disk('public')->delete($cover);
            }
            $pengumuman['cover_image'] = $request->file('cover')->store('lowongan', 'public');
        } elseif ($request->boolean('hapus_cover') && $cover) {
            Storage::disk('public')->delete($cover);
            $pengumuman['cover_image'] = null;
        }

        $eksternal = $data['registration_type'] === 'external';

        $detail = [
            'partner_id' => Query::mitraDariNama($data['nama_mitra']),
            'activity_type_id' => $data['activity_type_id'],
            'period_id' => $data['period_id'] ?? null,
            'position' => $data['position'] ?? null,
            'location' => $data['location'] ?? null,
            'criteria' => $data['criteria'],
            'quota' => $data['quota'] ?? null,
            'registration_type' => $data['registration_type'],
            'external_url' => $eksternal ? $data['external_url'] : null,
            'registration_start_date' => $data['registration_start_date'] ?? null,
            'registration_deadline' => $data['registration_deadline'] ?? null,
            'is_closed' => $request->boolean('is_closed'),
        ];

        return [$pengumuman, $detail];
    }

    /** Ganti daftar isian tambahan (selalu tidak wajib). Jawaban lama tetap aman (disimpan label => jawaban). */
    private function simpanFieldTambahan(int $vacancyId, array $rows): void
    {
        DB::delete('DELETE FROM vacancy_additional_fields WHERE vacancy_id = ?', [$vacancyId]);

        $urutan = 0;
        foreach ($rows as $row) {
            $label = trim((string) ($row['label'] ?? ''));
            if ($label === '') {
                continue;
            }
            Sql::insert('vacancy_additional_fields', [
                'vacancy_id' => $vacancyId,
                'label' => $label,
                'field_type' => $row['field_type'] ?? 'text',
                'is_required' => false,
                'display_order' => ++$urutan,
            ]);
        }
    }
}
