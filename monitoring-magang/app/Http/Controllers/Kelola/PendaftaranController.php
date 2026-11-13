<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use App\Models\Pendaftaran;
use App\Services\PesertaPdlkService;
use App\Support\Peran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Pengelolaan pendaftar oleh Staf UPT (PB-06, PB-07).
 * UPT mengirim data pendaftar ke mitra (unduh spreadsheet), lalu
 * memperbarui status sesuai hasil seleksi mitra.
 */
class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->query($request);

        $statistik = (clone $query)->reorder()
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        return view('kelola.pendaftaran.index', [
            'pendaftaran' => $query->with(['lowongan.mitra', 'programStudi'])->paginate(20)->withQueryString(),
            'lowonganList' => Lowongan::orderByDesc('created_at')->get(['id', 'judul']),
            'statistik' => $statistik,
        ]);
    }

    public function updateStatus(Request $request, Pendaftaran $pendaftaran, PesertaPdlkService $service)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Pendaftaran::STATUS))],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $hasil = $service->ubahStatusPendaftaran($pendaftaran, $data['status'], $data['catatan'] ?? null, Peran::userId());

        return back()->with($hasil['ok'] ? 'success' : 'warning', $hasil['pesan']);
    }

    /** Tandai beberapa pendaftar sekaligus, mis. "Proses" setelah dikirim ke mitra. */
    public function updateStatusMassal(Request $request, PesertaPdlkService $service)
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:pendaftaran,id'],
            'status' => ['required', Rule::in(array_keys(Pendaftaran::STATUS))],
        ], ['ids.required' => 'Pilih minimal satu pendaftar.']);

        $berhasil = 0;
        $gagal = [];

        foreach (Pendaftaran::whereIn('id', $data['ids'])->get() as $p) {
            $hasil = $service->ubahStatusPendaftaran($p, $data['status'], null, Peran::userId());
            $hasil['ok'] ? $berhasil++ : $gagal[] = $p->nama;
        }

        $pesan = "{$berhasil} pendaftar diperbarui menjadi " . Pendaftaran::STATUS[$data['status']] . '.';

        if ($gagal) {
            return back()->with('warning', $pesan . ' Tidak diubah (sudah lulus): ' . implode(', ', $gagal) . '.');
        }

        return back()->with('success', $pesan);
    }

    public function dokumen(Pendaftaran $pendaftaran)
    {
        return Storage::disk('local')->download($pendaftaran->dokumen_path, $pendaftaran->nim . '-' . $pendaftaran->dokumen_nama_asli);
    }

    /**
     * Unduh data pendaftar (CSV, dapat dibuka di Excel / Google Sheets)
     * untuk dikirim ke mitra.
     */
    public function ekspor(Request $request)
    {
        $rows = $this->query($request)->with(['lowongan.mitra', 'programStudi'])->get();
        $nama = 'pendaftar-pdlk-' . now()->format('Ymd-His') . '.csv';

        $kolomTambahan = $rows->flatMap(fn ($p) => array_keys($p->jawaban_tambahan ?? []))->unique()->values();

        return response()->streamDownload(function () use ($rows, $kolomTambahan) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8

            fputcsv($out, array_merge(
                ['Lowongan', 'Mitra', 'NIM', 'Nama', 'Program Studi', 'Semester', 'IPK', 'No. HP', 'Email', 'Status', 'Tanggal Daftar', 'Link Dokumen'],
                $kolomTambahan->all()
            ));

            foreach ($rows as $p) {
                fputcsv($out, array_merge([
                    $p->lowongan->judul,
                    $p->lowongan->mitra->nama ?? '',
                    $p->nim,
                    $p->nama,
                    $p->programStudi->label ?? '',
                    $p->semester,
                    number_format((float) $p->ipk, 2, '.', ''),
                    "'" . $p->no_hp,
                    $p->email,
                    $p->status_label,
                    $p->created_at->format('d/m/Y H:i'),
                    route('kelola.pendaftaran.dokumen', $p),
                ], $kolomTambahan->map(fn ($k) => $p->jawaban_tambahan[$k] ?? '')->all()));
            }

            fclose($out);
        }, $nama, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function query(Request $request)
    {
        $q = trim((string) $request->query('q'));

        return Pendaftaran::query()
            ->when($request->query('lowongan'), fn ($w, $id) => $w->where('lowongan_id', $id))
            ->when($request->query('status'), fn ($w, $s) => $w->where('status', $s))
            ->when($q !== '', fn ($w) => $w->where(function ($x) use ($q) {
                $like = '%' . mb_strtolower($q) . '%';
                $x->whereRaw('LOWER(nama) LIKE ?', [$like])->orWhere('nim', 'like', "%{$q}%");
            }))
            ->latest();
    }
}
