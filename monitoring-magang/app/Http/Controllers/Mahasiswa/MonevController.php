<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Services\ParticipantService;
use App\Support\NimMahasiswa;
use App\Support\PesertaSaya;
use App\Support\Sql;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Pengisian monev tengah & akhir oleh mahasiswa aktif
 * (monev_responses + monev_answers), query SQL langsung.
 */
class MonevController extends Controller
{
    public function index(NimMahasiswa $nimMahasiswa)
    {
        $peserta = $this->peserta($nimMahasiswa);

        return view('mahasiswa.monev.index', [
            'peserta' => $peserta,
            'jadwal' => PesertaSaya::jadwalMonev($peserta),
        ]);
    }

    public function isi(int $jadwalId, NimMahasiswa $nimMahasiswa)
    {
        $peserta = $this->peserta($nimMahasiswa);
        $jadwal = $this->jadwal($jadwalId, $peserta);
        $respon = Sql::one('SELECT * FROM monev_responses WHERE participant_id = ? AND schedule_id = ?', [$peserta->id, $jadwalId]);

        return view('mahasiswa.monev.isi', [
            'peserta' => $peserta,
            'jadwal' => $jadwal,
            'respon' => $respon,
            'pertanyaan' => $this->pertanyaan($jadwal->questionnaire_id),
            'jawaban' => $respon
                ? Sql::all('SELECT * FROM monev_answers WHERE response_id = ?', [$respon->id])->keyBy('question_id')
                : collect(),
            'bisaIsi' => !$jadwal->is_closed && $jadwal->open_at <= now()->toDateTimeString() && $respon?->status !== 'submitted',
        ]);
    }

    public function simpan(Request $request, int $jadwalId, NimMahasiswa $nimMahasiswa, ParticipantService $service)
    {
        $peserta = $this->peserta($nimMahasiswa);
        $jadwal = $this->jadwal($jadwalId, $peserta);
        $kirim = $request->input('aksi') === 'kirim';

        abort_if($jadwal->is_closed || $jadwal->open_at > now()->toDateTimeString(), 403, 'Monev ini sedang tidak dibuka.');

        $respon = Sql::one('SELECT * FROM monev_responses WHERE participant_id = ? AND schedule_id = ?', [$peserta->id, $jadwalId]);
        abort_if($respon?->status === 'submitted', 403, 'Monev ini sudah dikirim.');

        $pertanyaan = $this->pertanyaan($jadwal->questionnaire_id);
        $lama = $respon ? Sql::all('SELECT * FROM monev_answers WHERE response_id = ?', [$respon->id])->keyBy('question_id') : collect();

        // Validasi: saat "Kirim", pertanyaan wajib harus terisi.
        $rules = [];
        foreach ($pertanyaan as $x) {
            $wajib = $kirim && $x->is_required && !($x->question_type === 'file' && $lama->get($x->id)?->file_path);
            $kunci = "jawaban.{$x->id}";
            $rules[$kunci] = match ($x->question_type) {
                'file' => [$wajib ? 'required' : 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx', 'max:10240'],
                'scale' => [$wajib ? 'required' : 'nullable', 'integer',
                    'min:' . (json_decode((string) $x->options, true)['min'] ?? 1),
                    'max:' . (json_decode((string) $x->options, true)['max'] ?? 5)],
                'choice' => [$wajib ? 'required' : 'nullable', 'in:' . implode(',', array_map(fn ($o) => str_replace(',', '\,', $o), (array) json_decode((string) $x->options, true)))],
                default => [$wajib ? 'required' : 'nullable', 'string', 'max:5000'],
            };
        }
        $nama = $pertanyaan->mapWithKeys(fn ($x) => ["jawaban.{$x->id}" => '"' . \Illuminate\Support\Str::limit($x->question_text, 60) . '"'])->all();
        $request->validate($rules, ['required' => 'Pertanyaan :attribute wajib diisi.'], $nama);

        Sql::transaction(function () use ($request, $peserta, $jadwalId, $respon, $pertanyaan, $lama, $kirim, $service) {
            $responId = $respon?->id ?? Sql::insert('monev_responses', [
                'participant_id' => $peserta->id,
                'schedule_id' => $jadwalId,
                'status' => 'draft',
            ]);

            foreach ($pertanyaan as $x) {
                $nilai = [
                    'answer_text' => null,
                    'answer_number' => null,
                    'file_path' => $lama->get($x->id)?->file_path,
                ];

                if ($x->question_type === 'file') {
                    if ($request->hasFile("jawaban.{$x->id}")) {
                        if ($nilai['file_path']) {
                            Storage::disk('public')->delete($nilai['file_path']);
                        }
                        $nilai['file_path'] = $request->file("jawaban.{$x->id}")->store("monev/$responId", 'public');
                    }
                } elseif ($x->question_type === 'scale') {
                    $v = $request->input("jawaban.{$x->id}");
                    $nilai['answer_number'] = $v === null || $v === '' ? null : (int) $v;
                    $nilai['answer_text'] = $nilai['answer_number'] === null ? null : (string) $nilai['answer_number'];
                } else {
                    $nilai['answer_text'] = trim((string) $request->input("jawaban.{$x->id}")) ?: null;
                }

                if ($lama->has($x->id)) {
                    Sql::update('monev_answers', $lama->get($x->id)->id, $nilai);
                } else {
                    Sql::insert('monev_answers', $nilai + ['response_id' => $responId, 'question_id' => $x->id]);
                }
            }

            Sql::update('monev_responses', $responId, $kirim
                ? ['status' => 'submitted', 'submitted_at' => now()]
                : ['status' => 'draft']);

            if ($kirim) {
                $this->cekSelesai($peserta->id, $service);
            }
        });

        return $kirim
            ? redirect()->route('monitoring.index')->with('success', 'Monev berhasil dikirim. Terima kasih!')
            : redirect()->route('monitoring.isi', $jadwalId)->with('success', 'Jawaban disimpan sebagai draf. Jangan lupa klik Kirim.');
    }

    /** Status menjadi Selesai otomatis setelah monev tengah DAN akhir terkirim. */
    private function cekSelesai(int $participantId, ParticipantService $service): void
    {
        $jenis = Sql::all(
            "SELECT DISTINCT q.type FROM monev_responses r
             JOIN monev_schedules s ON s.id = r.schedule_id
             JOIN monev_questionnaires q ON q.id = s.questionnaire_id
             WHERE r.participant_id = ? AND r.status = 'submitted'",
            [$participantId]
        )->pluck('type');

        if ($jenis->contains('mid') && $jenis->contains('final')) {
            $service->changeParticipantStatus($participantId, 'completed', 'Monev tengah dan akhir telah lengkap.');
        }
    }

    private function peserta(NimMahasiswa $nimMahasiswa): object
    {
        return PesertaSaya::untukNim($nimMahasiswa->sesiIni(), true)
            ?? abort(403, 'Monev hanya untuk mahasiswa aktif PDLK.');
    }

    private function jadwal(int $id, object $peserta): object
    {
        return Sql::oneOrFail(
            'SELECT s.*, q.type, q.title, q.description FROM monev_schedules s
             JOIN monev_questionnaires q ON q.id = s.questionnaire_id
             WHERE s.id = ? AND s.period_id = ? AND s.is_active AND q.is_active',
            [$id, $peserta->period_id ?? 0]
        );
    }

    private function pertanyaan(int $kuesionerId): \Illuminate\Support\Collection
    {
        return Sql::all(
            'SELECT * FROM monev_questions WHERE questionnaire_id = ? AND is_active ORDER BY display_order, id',
            [$kuesionerId]
        );
    }
}
