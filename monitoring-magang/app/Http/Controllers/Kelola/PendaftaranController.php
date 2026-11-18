<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Services\ParticipantService;
use App\Support\Label;
use App\Support\Query;
use App\Support\Sql;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Pendaftar lowongan internal (applications) oleh Staf UPT, query SQL langsung.
 * UPT mengirim data ke mitra (unduh spreadsheet), lalu memperbarui status.
 */
class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        [$where, $bind] = $this->filter($request);

        return view('kelola.pendaftaran.index', [
            'pendaftaran' => Sql::paginate(Query::PENDAFTARAN . " WHERE $where ORDER BY ap.created_at DESC", $bind, 20),
            'statistik' => Sql::all(
                "SELECT ap.status, COUNT(*) AS jumlah FROM applications ap
                 JOIN vacancy_details v ON v.id = ap.vacancy_id
                 JOIN announcements a ON a.id = v.announcement_id
                 JOIN partners p ON p.id = v.partner_id
                 WHERE $where GROUP BY ap.status", $bind
            )->pluck('jumlah', 'status'),
            'lowonganList' => Sql::all(
                "SELECT v.id, a.title FROM vacancy_details v JOIN announcements a ON a.id = v.announcement_id
                 WHERE v.registration_type = 'internal' ORDER BY v.created_at DESC"
            ),
        ]);
    }

    public function updateStatus(Request $request, int $id, ParticipantService $service)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Label::APPLICATION_STATUS))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $hasil = $service->changeApplicationStatus($id, $data['status'], $data['notes'] ?? null);

        return back()->with($hasil['ok'] ? 'success' : 'warning', $hasil['pesan']);
    }

    /** Tandai beberapa pendaftar sekaligus, mis. "Proses" setelah dikirim ke mitra. */
    public function updateStatusMassal(Request $request, ParticipantService $service)
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:applications,id'],
            'status' => ['required', Rule::in(array_keys(Label::APPLICATION_STATUS))],
        ], ['ids.required' => 'Pilih minimal satu pendaftar.']);

        $berhasil = 0;
        $gagal = [];

        foreach ($data['ids'] as $id) {
            $hasil = $service->changeApplicationStatus((int) $id, $data['status']);
            $hasil['ok'] ? $berhasil++ : $gagal[] = Sql::value('SELECT name FROM applications WHERE id = ?', [$id]);
        }

        $pesan = "{$berhasil} pendaftar diperbarui menjadi " . Label::APPLICATION_STATUS[$data['status']] . '.';

        return $gagal
            ? back()->with('warning', $pesan . ' Tidak diubah (sudah lulus): ' . implode(', ', $gagal) . '.')
            : back()->with('success', $pesan);
    }

    public function dokumen(int $id)
    {
        $ap = Sql::oneOrFail('SELECT nim, document_path, document_original_name FROM applications WHERE id = ?', [$id]);

        return Storage::disk('local')->download($ap->document_path, $ap->nim . '-' . $ap->document_original_name);
    }

    /** Unduh data pendaftar (CSV, bisa dibuka di Excel / Google Sheets) untuk dikirim ke mitra. */
    public function ekspor(Request $request)
    {
        [$where, $bind] = $this->filter($request);
        $rows = Sql::all(Query::PENDAFTARAN . " WHERE $where ORDER BY ap.created_at", $bind);
        $kolomTambahan = $rows->flatMap(fn ($p) => array_keys((array) json_decode((string) $p->additional_answers, true)))->unique()->values();

        return response()->streamDownload(function () use ($rows, $kolomTambahan) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8

            fputcsv($out, array_merge(
                ['Lowongan', 'Mitra', 'NIM', 'Nama', 'Program Studi', 'Semester', 'IPK', 'No. HP', 'Email', 'Status', 'Tanggal Daftar', 'Link Dokumen'],
                $kolomTambahan->all()
            ));

            foreach ($rows as $p) {
                $jawaban = (array) json_decode((string) $p->additional_answers, true);
                fputcsv($out, array_merge([
                    $p->vacancy_title, $p->partner_name, $p->nim, $p->name,
                    trim($p->prodi_level . ' ' . $p->prodi_name), $p->semester,
                    number_format((float) $p->gpa, 2, '.', ''), "'" . $p->phone, $p->email,
                    Label::of(Label::APPLICATION_STATUS, $p->status), tgl($p->created_at, 'd/m/Y H:i'),
                    route('kelola.pendaftaran.dokumen', $p->id),
                ], $kolomTambahan->map(fn ($k) => $jawaban[$k] ?? '')->all()));
            }

            fclose($out);
        }, 'pendaftar-pdlk-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** @return array{0: string, 1: array} */
    private function filter(Request $request): array
    {
        $where = ['ap.is_active = true'];
        $bind = [];

        if ($v = $request->query('lowongan')) {
            $where[] = 'ap.vacancy_id = ?';
            $bind[] = (int) $v;
        }
        if ($s = $request->query('status')) {
            $where[] = 'ap.status = ?';
            $bind[] = $s;
        }
        if (($q = trim((string) $request->query('q'))) !== '') {
            $where[] = '(LOWER(ap.name) LIKE ? OR ap.nim LIKE ?)';
            array_push($bind, '%' . mb_strtolower($q) . '%', "%$q%");
        }

        return [implode(' AND ', $where), $bind];
    }
}
