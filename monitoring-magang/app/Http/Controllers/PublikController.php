<?php

namespace App\Http\Controllers;

use App\Models\JenisKegiatan;
use App\Models\Lowongan;
use App\Models\Postingan;
use Illuminate\Http\Request;

/**
 * Halaman yang dapat dibuka tanpa login (Guest).
 * Aksi lanjutan seperti mendaftar diarahkan ke login CIS.
 */
class PublikController extends Controller
{
    public function beranda()
    {
        return view('publik.beranda', [
            'lowongan' => Lowongan::terbit()->with(['mitra', 'jenisKegiatan'])
                ->latest()->take(6)->get(),
            'postingan' => Postingan::terbit()->take(6)->get(),
            'jenisKegiatan' => JenisKegiatan::orderBy('urutan')->get(),
        ]);
    }

    public function lowongan(Request $request)
    {
        $jenis = JenisKegiatan::orderBy('urutan')->get();
        $q = trim((string) $request->query('q'));

        $lowongan = Lowongan::terbit()
            ->with(['mitra', 'jenisKegiatan'])
            ->when($request->query('jenis'), fn ($query, $slug) =>
                $query->whereHas('jenisKegiatan', fn ($j) => $j->where('slug', $slug)))
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $like = '%' . mb_strtolower($q) . '%';
                $w->whereRaw('LOWER(judul) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(posisi) LIKE ?', [$like])
                    ->orWhereHas('mitra', fn ($m) => $m->whereRaw('LOWER(nama) LIKE ?', [$like]));
            }))
            ->orderByRaw('CASE WHEN tanggal_tutup IS NULL OR tanggal_tutup >= ? THEN 0 ELSE 1 END', [now()->toDateString()])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('publik.lowongan-index', compact('lowongan', 'jenis', 'q'));
    }

    public function lowonganDetail(string $slug)
    {
        $lowongan = Lowongan::terbit()
            ->with(['mitra', 'jenisKegiatan', 'fieldTambahan'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('publik.lowongan-show', compact('lowongan'));
    }

    public function berita(Request $request)
    {
        $postingan = Postingan::terbit()
            ->when($request->query('jenis'), fn ($q, $jenis) => $q->where('jenis', $jenis))
            ->paginate(12)
            ->withQueryString();

        return view('publik.berita-index', compact('postingan'));
    }

    public function beritaDetail(string $slug)
    {
        $postingan = Postingan::terbit()->where('slug', $slug)->firstOrFail();

        $lainnya = Postingan::terbit()->whereKeyNot($postingan->id)->take(3)->get();

        return view('publik.berita-show', compact('postingan', 'lainnya'));
    }

    public function tentang()
    {
        return view('publik.tentang', [
            'jenisKegiatan' => JenisKegiatan::orderBy('urutan')->get(),
        ]);
    }
}
