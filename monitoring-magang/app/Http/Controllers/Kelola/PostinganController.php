<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Postingan;
use App\Support\Peran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Posting berita / pengumuman / kegiatan oleh Staf UPT.
 * Dibuat sesederhana unggah foto + narasi (seperti posting media sosial).
 */
class PostinganController extends Controller
{
    public function index(Request $request)
    {
        $postingan = Postingan::query()
            ->when($request->query('jenis'), fn ($q, $j) => $q->where('jenis', $j))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('kelola.postingan.index', compact('postingan'));
    }

    public function create()
    {
        return view('kelola.postingan.form', ['postingan' => new Postingan(['jenis' => 'berita', 'is_published' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            $data['foto_path'] = $request->file('foto')->store('postingan', 'public');
        }


        Postingan::create($data);

        return redirect()->route('kelola.postingan.index')->with('success', 'Postingan berhasil diterbitkan.');
    }

    public function edit(Postingan $postingan)
    {
        return view('kelola.postingan.form', compact('postingan'));
    }

    public function update(Request $request, Postingan $postingan)
    {
        $data = $this->validasi($request, $postingan);

        if ($request->hasFile('foto')) {
            $this->hapusFoto($postingan);
            $data['foto_path'] = $request->file('foto')->store('postingan', 'public');
        } elseif ($request->boolean('hapus_foto')) {
            $this->hapusFoto($postingan);
            $data['foto_path'] = null;
        }

        $postingan->update($data);

        return redirect()->route('kelola.postingan.index')->with('success', 'Postingan berhasil diperbarui.');
    }

    public function destroy(Postingan $postingan)
    {
        $this->hapusFoto($postingan);
        $postingan->delete();

        return redirect()->route('kelola.postingan.index')->with('success', 'Postingan dihapus.');
    }

    private function validasi(Request $request, ?Postingan $postingan = null): array
    {
        $data = $request->validate([
            'jenis' => ['required', Rule::in(array_keys(Postingan::JENIS))],
            'judul' => ['required', 'string', 'max:200'],
            'narasi' => ['required', 'string', 'max:10000'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'foto.image' => 'File harus berupa gambar (JPG, PNG, atau WEBP).',
            'foto.max' => 'Ukuran foto maksimal 4 MB.',
        ]);

        unset($data['foto']);
        $data['is_published'] = $request->boolean('is_published');

        if ($data['is_published'] && !$postingan?->published_at) {
            $data['published_at'] = now();
        }

        return $data;
    }

    private function hapusFoto(Postingan $postingan): void
    {
        if ($postingan->foto_path) {
            Storage::disk('public')->delete($postingan->foto_path);
        }
    }
}
