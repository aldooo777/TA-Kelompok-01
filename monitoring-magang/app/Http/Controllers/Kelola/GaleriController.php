<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Support\Sql;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/** Galeri kegiatan (galleries + gallery_items) dengan query SQL langsung. */
class GaleriController extends Controller
{
    public function index()
    {
        $galeri = Sql::paginate(
            'SELECT g.*, (SELECT COUNT(*) FROM gallery_items i WHERE i.gallery_id = g.id AND i.is_active) AS jumlah_item
             FROM galleries g
             WHERE g.is_active = true
             ORDER BY g.event_date DESC NULLS LAST, g.id DESC',
            [], 15
        );

        return view('kelola.galeri.index', compact('galeri'));
    }

    public function create()
    {
        return view('kelola.galeri.form', ['galeri' => null, 'items' => collect()]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        $id = Sql::insert('galleries', $data);
        $this->simpanItem($request, $id);

        return redirect()->route('kelola.galeri.edit', $id)->with('success', 'Galeri berhasil dibuat. Tambahkan foto atau video bila perlu.');
    }

    public function edit(int $id)
    {
        return view('kelola.galeri.form', [
            'galeri' => Sql::oneOrFail('SELECT * FROM galleries WHERE id = ?', [$id]),
            'items' => Sql::all('SELECT * FROM gallery_items WHERE gallery_id = ? AND is_active = true ORDER BY display_order, id', [$id]),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $galeri = Sql::oneOrFail('SELECT * FROM galleries WHERE id = ?', [$id]);
        Sql::update('galleries', $id, $this->validasi($request, $galeri));
        $this->simpanItem($request, $id);

        foreach ($request->input('caption', []) as $itemId => $caption) {
            \DB::update(
                'UPDATE gallery_items SET caption = ?, updated_at = CURRENT_TIMESTAMP, updated_by = ? WHERE id = ? AND gallery_id = ?',
                [mb_substr((string) $caption, 0, 255) ?: null, \App\Support\Peran::userId(), $itemId, $id]
            );
        }

        return redirect()->route('kelola.galeri.edit', $id)->with('success', 'Galeri berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        Sql::update('galleries', $id, ['is_active' => false]);

        return redirect()->route('kelola.galeri.index')->with('success', 'Galeri dihapus dari daftar.');
    }

    public function hapusItem(int $id, int $itemId)
    {
        $item = Sql::oneOrFail('SELECT * FROM gallery_items WHERE id = ? AND gallery_id = ?', [$itemId, $id]);
        Storage::disk('public')->delete($item->file_path);
        Sql::delete('gallery_items', $itemId);

        return back()->with('success', 'Item galeri dihapus.');
    }

    private function validasi(Request $request, ?object $galeri = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'event_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'cover' => ['nullable', 'image', 'max:4096'],
            'items' => ['nullable', 'array', 'max:20'],
            'items.*' => ['file', 'mimetypes:image/jpeg,image/png,image/webp,video/mp4', 'max:20480'],
        ], ['items.*.mimetypes' => 'Item galeri harus berupa gambar (JPG/PNG/WEBP) atau video MP4.']);

        unset($data['cover'], $data['items']);

        if ($request->hasFile('cover')) {
            if ($galeri?->cover_image) {
                Storage::disk('public')->delete($galeri->cover_image);
            }
            $data['cover_image'] = $request->file('cover')->store('galeri', 'public');
        }

        return $data;
    }

    private function simpanItem(Request $request, int $galeriId): void
    {
        $urutan = (int) Sql::value('SELECT COALESCE(MAX(display_order), 0) FROM gallery_items WHERE gallery_id = ?', [$galeriId]);

        foreach ($request->file('items', []) as $file) {
            Sql::insert('gallery_items', [
                'gallery_id' => $galeriId,
                'file_path' => $file->store("galeri/$galeriId", 'public'),
                'media_type' => str_starts_with((string) $file->getMimeType(), 'video/') ? 'video' : 'image',
                'display_order' => ++$urutan,
            ]);
        }
    }
}
