<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Support\Lampiran;
use App\Support\Query;
use App\Support\Sql;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Berita oleh Staf UPT (announcements.type = 'news'), query SQL langsung.
 * Dibuat sesederhana unggah foto + narasi (seperti posting media sosial).
 */
class PostinganController extends Controller
{
    public function index()
    {
        $berita = Sql::paginate(
            "SELECT a.*, (SELECT COUNT(*) FROM announcement_attachments l WHERE l.announcement_id = a.id) AS jumlah_lampiran
             FROM announcements a
             WHERE a.type = 'news' AND a.is_active = true
             ORDER BY a.created_at DESC",
            [], 15
        );

        return view('kelola.postingan.index', compact('berita'));
    }

    public function create()
    {
        return view('kelola.postingan.form', ['berita' => null, 'lampiran' => collect()]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        Sql::transaction(function () use ($request, $data) {
            $id = Sql::insert('announcements', $data + [
                'type' => 'news',
                'slug' => Query::slugUnik($data['title']),
                'published_at' => $data['status'] === 'published' ? now() : null,
            ]);
            Lampiran::simpan($request, $id);
        });

        return redirect()->route('kelola.postingan.index')->with('success', 'Berita berhasil disimpan.');
    }

    public function edit(int $id)
    {
        return view('kelola.postingan.form', [
            'berita' => $this->cari($id),
            'lampiran' => Lampiran::daftar($id),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $berita = $this->cari($id);
        $data = $this->validasi($request, $berita);

        if ($data['status'] === 'published' && !$berita->published_at) {
            $data['published_at'] = now();
        }

        Sql::transaction(function () use ($request, $data, $id) {
            Sql::update('announcements', $id, $data);
            Lampiran::simpan($request, $id);
        });

        return redirect()->route('kelola.postingan.index')->with('success', 'Berita berhasil diperbarui.');
    }

    /** Tidak dihapus permanen: diarsipkan dan is_active = false. */
    public function destroy(int $id)
    {
        $this->cari($id);
        Sql::update('announcements', $id, ['status' => 'archived', 'is_active' => false]);

        return redirect()->route('kelola.postingan.index')->with('success', 'Berita dihapus dari daftar.');
    }

    private function cari(int $id): object
    {
        return Sql::oneOrFail("SELECT * FROM announcements WHERE id = ? AND type = 'news'", [$id]);
    }

    private function validasi(Request $request, ?object $berita = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'summary' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:10000'],
            'expired_at' => ['nullable', 'date'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ] + Lampiran::RULES, [
            'title.required' => 'Judul wajib diisi.',
            'content.required' => 'Narasi wajib diisi.',
            'foto.image' => 'File harus berupa gambar (JPG, PNG, atau WEBP).',
            'foto.max' => 'Ukuran foto maksimal 4 MB.',
        ]);

        $hasil = [
            'title' => $data['title'],
            'summary' => $data['summary'] ?? null,
            'content' => $data['content'],
            'expired_at' => $data['expired_at'] ?? null,
            'status' => $request->boolean('terbit') ? 'published' : 'draft',
        ];

        if ($request->hasFile('foto')) {
            if ($berita?->cover_image) {
                Storage::disk('public')->delete($berita->cover_image);
            }
            $hasil['cover_image'] = $request->file('foto')->store('berita', 'public');
        } elseif ($request->boolean('hapus_foto') && $berita?->cover_image) {
            Storage::disk('public')->delete($berita->cover_image);
            $hasil['cover_image'] = null;
        }

        return $hasil;
    }
}
