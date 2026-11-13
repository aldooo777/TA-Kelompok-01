<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\JenisKegiatan;
use App\Models\Lowongan;
use App\Models\LowonganFieldTambahan;
use App\Models\Mitra;
use App\Models\Pendaftaran;
use App\Support\Peran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Pengelolaan lowongan PDLK oleh Staf UPT.
 * Formulir pendaftaran sama untuk semua lowongan; Staf UPT hanya dapat
 * menambahkan isian tambahan yang TIDAK wajib sesuai permintaan mitra.
 */
class LowonganController extends Controller
{
    public function index(Request $request)
    {
        $lowongan = Lowongan::with(['mitra', 'jenisKegiatan'])
            ->withCount('pendaftaran')
            ->withCount(['pendaftaran as pendaftar_baru_count' => fn ($q) => $q->where('status', Pendaftaran::TERDAFTAR)])
            ->latest()
            ->paginate(15);

        return view('kelola.lowongan.index', compact('lowongan'));
    }

    public function create()
    {
        return view('kelola.lowongan.form', [
            'lowongan' => new Lowongan(['is_published' => true]),
            'jenisKegiatan' => JenisKegiatan::orderBy('urutan')->get(),
            'mitraList' => Mitra::orderBy('nama')->pluck('nama'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        DB::transaction(function () use ($request, $data) {
            $lowongan = Lowongan::create($data);
            $this->simpanFieldTambahan($lowongan, $request->input('field_tambahan', []));
        });

        return redirect()->route('kelola.lowongan.index')->with('success', 'Lowongan berhasil dipublikasikan.');
    }

    public function edit(Lowongan $lowongan)
    {
        return view('kelola.lowongan.form', [
            'lowongan' => $lowongan->load('fieldTambahan', 'mitra'),
            'jenisKegiatan' => JenisKegiatan::orderBy('urutan')->get(),
            'mitraList' => Mitra::orderBy('nama')->pluck('nama'),
        ]);
    }

    public function update(Request $request, Lowongan $lowongan)
    {
        $data = $this->validasi($request, $lowongan);

        DB::transaction(function () use ($request, $data, $lowongan) {
            $lowongan->update($data);
            $this->simpanFieldTambahan($lowongan, $request->input('field_tambahan', []));
        });

        return redirect()->route('kelola.lowongan.index')->with('success', 'Lowongan berhasil diperbarui.');
    }

    public function destroy(Lowongan $lowongan)
    {
        if ($lowongan->pendaftaran()->exists()) {
            $lowongan->update(['is_published' => false]);

            return back()->with('warning', 'Lowongan sudah memiliki pendaftar sehingga tidak dihapus, tetapi disembunyikan dari halaman publik.');
        }

        if ($lowongan->poster_path) {
            Storage::disk('public')->delete($lowongan->poster_path);
        }

        $lowongan->delete();

        return back()->with('success', 'Lowongan dihapus.');
    }

    private function validasi(Request $request, ?Lowongan $lowongan = null): array
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'nama_mitra' => ['required', 'string', 'max:200'],
            'jenis_kegiatan_id' => ['required', 'exists:jenis_kegiatan_pdlk,id'],
            'posisi' => ['nullable', 'string', 'max:150'],
            'lokasi' => ['nullable', 'string', 'max:150'],
            'deskripsi' => ['required', 'string', 'max:10000'],
            'kriteria' => ['required', 'string', 'max:10000'],
            'kuota' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'tanggal_buka' => ['nullable', 'date'],
            'tanggal_tutup' => ['nullable', 'date', 'after_or_equal:tanggal_buka'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'field_tambahan' => ['nullable', 'array', 'max:10'],
            'field_tambahan.*.label' => ['nullable', 'string', 'max:150'],
            'field_tambahan.*.tipe' => ['nullable', Rule::in(array_keys(LowonganFieldTambahan::TIPE))],
        ], [
            'tanggal_tutup.after_or_equal' => 'Tanggal tutup tidak boleh sebelum tanggal buka.',
            'nama_mitra.required' => 'Nama mitra wajib diisi.',
        ]);

        $data['mitra_id'] = Mitra::dariNama($data['nama_mitra'])->id;
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('poster')) {
            if ($lowongan?->poster_path) {
                Storage::disk('public')->delete($lowongan->poster_path);
            }
            $data['poster_path'] = $request->file('poster')->store('lowongan', 'public');
        } elseif ($request->boolean('hapus_poster') && $lowongan?->poster_path) {
            Storage::disk('public')->delete($lowongan->poster_path);
            $data['poster_path'] = null;
        }

        unset($data['nama_mitra'], $data['poster'], $data['field_tambahan']);

        return $data;
    }

    /**
     * Ganti daftar isian tambahan. Jawaban lama tetap aman karena disimpan
     * per pendaftaran dalam bentuk label => jawaban.
     */
    private function simpanFieldTambahan(Lowongan $lowongan, array $rows): void
    {
        $lowongan->fieldTambahan()->delete();

        $urutan = 0;
        foreach ($rows as $row) {
            $label = trim((string) ($row['label'] ?? ''));
            if ($label === '') {
                continue;
            }
            $lowongan->fieldTambahan()->create([
                'label' => $label,
                'tipe' => $row['tipe'] ?? 'text',
                'urutan' => ++$urutan,
            ]);
        }
    }
}
