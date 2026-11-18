<?php

namespace App\Http\Controllers;

use App\Support\Label;
use App\Support\Peran;
use App\Support\Query;
use App\Support\Sql;
use Illuminate\Http\Request;

/**
 * Rekap monev untuk Staf UPT (semua prodi) dan koordinator (prodinya saja):
 * siapa yang belum mengisi, persentase, tepat waktu / terlambat,
 * rata-rata jawaban skala, dan detail jawaban.
 */
class RekapMonevController extends Controller
{
    public function index(Request $request)
    {
        $jadwalList = Sql::all(
            "SELECT s.id, s.deadline_at, q.type, q.title, p.name AS period_name
             FROM monev_schedules s
             JOIN monev_questionnaires q ON q.id = s.questionnaire_id
             JOIN periods p ON p.id = s.period_id
             WHERE s.is_active ORDER BY (s.open_at <= CURRENT_TIMESTAMP) DESC, s.open_at DESC"
        );

        $jadwal = $jadwalList->firstWhere('id', (int) $request->query('jadwal')) ?? $jadwalList->first();

        if (!$jadwal) {
            return view('rekap-monev.index', ['jadwal' => null, 'jadwalList' => $jadwalList]);
        }

        [$where, $bind] = $this->filterPeserta($request, $jadwal->id);

        $baris = Sql::all(
            "SELECT pp.id, pp.nim, pp.name, pp.status AS participant_status, sp.code AS prodi_code,
                    p.name AS partner_name, r.id AS response_id, r.status AS response_status, r.submitted_at
             FROM pdlk_participants pp
             JOIN monev_schedules s ON s.id = ? AND s.period_id = pp.period_id
             LEFT JOIN study_programs sp ON sp.id = pp.study_program_id
             LEFT JOIN partners p ON p.id = pp.partner_id
             LEFT JOIN monev_responses r ON r.schedule_id = s.id AND r.participant_id = pp.id
             WHERE $where
             ORDER BY (r.status = 'submitted') NULLS FIRST, sp.code, pp.name",
            [$jadwal->id, ...$bind]
        );

        $total = $baris->count();
        $terkirim = $baris->where('response_status', 'submitted');

        return view('rekap-monev.index', [
            'jadwal' => $jadwal,
            'jadwalList' => $jadwalList,
            'baris' => $request->query('isi') === 'belum'
                ? $baris->where('response_status', '!==', 'submitted')
                : $baris,
            'statistik' => [
                'total' => $total,
                'terkirim' => $terkirim->count(),
                'terlambat' => $terkirim->filter(fn ($b) => $b->submitted_at > $jadwal->deadline_at)->count(),
                'draf' => $baris->where('response_status', 'draft')->count(),
                'belum' => $baris->whereNull('response_status')->count(),
                'persen' => $total ? round($terkirim->count() / $total * 100) : 0,
            ],
            'perProdi' => $baris->groupBy(fn ($b) => $b->prodi_code ?? '(kosong)')->map(fn ($g) => [
                'total' => $g->count(),
                'terkirim' => $g->where('response_status', 'submitted')->count(),
            ])->sortKeys(),
            'rataSkala' => Sql::all(
                "SELECT x.question_text, ROUND(AVG(a.answer_number), 2) AS rata, COUNT(a.answer_number) AS n
                 FROM monev_questions x
                 JOIN monev_schedules s ON s.questionnaire_id = x.questionnaire_id AND s.id = ?
                 JOIN monev_answers a ON a.question_id = x.id
                 JOIN monev_responses r ON r.id = a.response_id AND r.schedule_id = s.id AND r.status = 'submitted'
                 JOIN pdlk_participants pp ON pp.id = r.participant_id
                 WHERE x.question_type = 'scale' AND $where
                 GROUP BY x.id, x.question_text, x.display_order ORDER BY x.display_order",
                [$jadwal->id, ...$bind]
            ),
            'prodiList' => Query::opsiProdi(Peran::cakupanProdi()),
        ]);
    }

    public function jawaban(int $responseId)
    {
        $prodi = Peran::cakupanProdi();
        $respon = Sql::oneOrFail(
            'SELECT r.*, pp.name, pp.nim, pp.study_program_id, q.type, q.title, s.deadline_at
             FROM monev_responses r
             JOIN pdlk_participants pp ON pp.id = r.participant_id
             JOIN monev_schedules s ON s.id = r.schedule_id
             JOIN monev_questionnaires q ON q.id = s.questionnaire_id
             WHERE r.id = ?', [$responseId]
        );

        abort_if($prodi !== null && !in_array((int) $respon->study_program_id, $prodi, true), 404);

        return view('rekap-monev.jawaban', [
            'respon' => $respon,
            'jawaban' => Sql::all(
                'SELECT x.section_title, x.question_text, x.question_type, a.answer_text, a.answer_number, a.file_path
                 FROM monev_questions x
                 JOIN monev_schedules s ON s.questionnaire_id = x.questionnaire_id
                 JOIN monev_responses r ON r.schedule_id = s.id AND r.id = ?
                 LEFT JOIN monev_answers a ON a.question_id = x.id AND a.response_id = r.id
                 WHERE x.is_active OR a.id IS NOT NULL
                 ORDER BY x.display_order, x.id', [$responseId]
            ),
        ]);
    }

    /** Ekspor rekap (CSV) untuk diolah lebih lanjut. */
    public function ekspor(Request $request, int $jadwalId)
    {
        [$where, $bind] = $this->filterPeserta($request, $jadwalId);
        $jadwal = Sql::oneOrFail('SELECT s.*, q.title FROM monev_schedules s JOIN monev_questionnaires q ON q.id = s.questionnaire_id WHERE s.id = ?', [$jadwalId]);
        $pertanyaan = Sql::all('SELECT id, question_text FROM monev_questions WHERE questionnaire_id = ? AND is_active ORDER BY display_order, id', [$jadwal->questionnaire_id]);

        $baris = Sql::all(
            "SELECT pp.id, pp.nim, pp.name, sp.code AS prodi_code, r.id AS response_id, r.status, r.submitted_at
             FROM pdlk_participants pp
             LEFT JOIN study_programs sp ON sp.id = pp.study_program_id
             LEFT JOIN monev_responses r ON r.schedule_id = ? AND r.participant_id = pp.id
             WHERE pp.period_id = ? AND $where ORDER BY sp.code, pp.name",
            [$jadwalId, $jadwal->period_id, ...$bind]
        );
        $jawaban = Sql::all(
            'SELECT a.response_id, a.question_id, COALESCE(a.answer_text, a.file_path) AS isi
             FROM monev_answers a JOIN monev_responses r ON r.id = a.response_id WHERE r.schedule_id = ?', [$jadwalId]
        )->groupBy('response_id');

        return response()->streamDownload(function () use ($baris, $pertanyaan, $jawaban, $jadwal) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, array_merge(['NIM', 'Nama', 'Prodi', 'Status', 'Waktu kirim', 'Terlambat'], $pertanyaan->pluck('question_text')->all()));
            foreach ($baris as $b) {
                $j = collect($jawaban->get($b->response_id, []))->pluck('isi', 'question_id');
                fputcsv($out, array_merge([
                    $b->nim, $b->name, $b->prodi_code,
                    $b->status ? Label::RESPONSE_STATUS[$b->status] : 'Belum diisi',
                    tgl($b->submitted_at, 'd/m/Y H:i'),
                    $b->submitted_at && $b->submitted_at > $jadwal->deadline_at ? 'Ya' : '',
                ], $pertanyaan->map(fn ($x) => $j[$x->id] ?? '')->all()));
            }
            fclose($out);
        }, 'rekap-monev-' . $jadwalId . '-' . now()->format('Ymd') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** @return array{0: string, 1: array} syarat peserta: aktif/selesai + cakupan prodi + filter */
    private function filterPeserta(Request $request, int $jadwalId): array
    {
        $where = ["pp.is_active", "pp.status IN ('active', 'completed')"];
        $bind = [];
        $prodi = Peran::cakupanProdi();

        if ($prodi !== null) {
            $where[] = 'pp.study_program_id IN (' . (implode(',', array_map('intval', $prodi)) ?: '0') . ')';
        }
        if ($p = $request->query('prodi')) {
            $where[] = 'pp.study_program_id = ?';
            $bind[] = (int) $p;
        }

        return [implode(' AND ', $where), $bind];
    }
}
