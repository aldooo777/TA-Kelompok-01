<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Support\Label;
use App\Support\Sql;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/** Kuesioner monev (monev_questionnaires) dan pertanyaannya (monev_questions). */
class KuesionerController extends Controller
{
    public function index()
    {
        return view('kelola.monev.kuesioner-index', [
            'kuesioner' => Sql::all(
                "SELECT q.*,
                        (SELECT COUNT(*) FROM monev_questions x WHERE x.questionnaire_id = q.id AND x.is_active) AS jumlah_pertanyaan,
                        (SELECT COUNT(*) FROM monev_schedules s WHERE s.questionnaire_id = q.id AND s.is_active) AS jumlah_jadwal
                 FROM monev_questionnaires q
                 ORDER BY q.is_active DESC, q.type, q.created_at DESC"
            ),
        ]);
    }

    public function create()
    {
        return view('kelola.monev.kuesioner-form', ['kuesioner' => null, 'pertanyaan' => collect()]);
    }

    public function store(Request $request)
    {
        $id = Sql::insert('monev_questionnaires', $this->validasi($request));

        return redirect()->route('kelola.kuesioner.edit', $id)->with('success', 'Kuesioner dibuat. Sekarang tambahkan pertanyaannya.');
    }

    public function edit(int $id)
    {
        return view('kelola.monev.kuesioner-form', [
            'kuesioner' => Sql::oneOrFail('SELECT * FROM monev_questionnaires WHERE id = ?', [$id]),
            'pertanyaan' => Sql::all(
                'SELECT x.*, (SELECT COUNT(*) FROM monev_answers a WHERE a.question_id = x.id) AS jumlah_jawaban
                 FROM monev_questions x WHERE x.questionnaire_id = ? AND x.is_active
                 ORDER BY x.display_order, x.id', [$id]
            ),
        ]);
    }

    public function update(Request $request, int $id)
    {
        Sql::oneOrFail('SELECT id FROM monev_questionnaires WHERE id = ?', [$id]);
        Sql::update('monev_questionnaires', $id, $this->validasi($request));

        return back()->with('success', 'Kuesioner diperbarui.');
    }

    public function toggle(int $id)
    {
        $q = Sql::oneOrFail('SELECT id, is_active FROM monev_questionnaires WHERE id = ?', [$id]);
        Sql::update('monev_questionnaires', $id, ['is_active' => !$q->is_active]);

        return back()->with('success', $q->is_active ? 'Kuesioner dinonaktifkan.' : 'Kuesioner diaktifkan.');
    }

    public function simpanPertanyaan(Request $request, int $id, ?int $pertanyaanId = null)
    {
        Sql::oneOrFail('SELECT id FROM monev_questionnaires WHERE id = ?', [$id]);

        $data = $request->validate([
            'section_title' => ['nullable', 'string', 'max:150'],
            'question_text' => ['required', 'string', 'max:2000'],
            'question_type' => ['required', Rule::in(array_keys(Label::QUESTION_TYPE))],
            'pilihan' => ['nullable', 'required_if:question_type,choice', 'string', 'max:2000'],
            'skala_min' => ['nullable', 'integer', 'min:0', 'max:10'],
            'skala_max' => ['nullable', 'integer', 'min:1', 'max:10', 'gt:skala_min'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ], ['pilihan.required_if' => 'Isi pilihan jawaban (satu per baris) untuk pertanyaan pilihan ganda.']);

        $opsi = match ($data['question_type']) {
            'choice' => array_values(array_filter(array_map('trim', preg_split('/\r?\n/', (string) $data['pilihan'])))),
            'scale' => ['min' => (int) ($data['skala_min'] ?? 1), 'max' => (int) ($data['skala_max'] ?? 5)],
            default => null,
        };

        $baris = [
            'questionnaire_id' => $id,
            'section_title' => $data['section_title'] ?? null,
            'question_text' => $data['question_text'],
            'question_type' => $data['question_type'],
            'options' => $opsi,
            'is_required' => $request->boolean('is_required'),
            'display_order' => $data['display_order']
                ?? ((int) Sql::value('SELECT COALESCE(MAX(display_order), 0) FROM monev_questions WHERE questionnaire_id = ?', [$id]) + 1),
        ];

        if ($pertanyaanId) {
            Sql::oneOrFail('SELECT id FROM monev_questions WHERE id = ? AND questionnaire_id = ?', [$pertanyaanId, $id]);
            Sql::update('monev_questions', $pertanyaanId, $baris);
        } else {
            Sql::insert('monev_questions', $baris);
        }

        return redirect()->route('kelola.kuesioner.edit', $id)->with('success', 'Pertanyaan disimpan.');
    }

    /** Pertanyaan yang sudah dijawab tidak dihapus, hanya dinonaktifkan agar jawaban lama tetap utuh. */
    public function hapusPertanyaan(int $id, int $pertanyaanId)
    {
        $x = Sql::oneOrFail('SELECT id FROM monev_questions WHERE id = ? AND questionnaire_id = ?', [$pertanyaanId, $id]);

        if (Sql::value('SELECT EXISTS (SELECT 1 FROM monev_answers WHERE question_id = ?)', [$x->id])) {
            Sql::update('monev_questions', $x->id, ['is_active' => false]);
        } else {
            DB::delete('DELETE FROM monev_questions WHERE id = ?', [$x->id]);
        }

        return back()->with('success', 'Pertanyaan dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'type' => ['required', Rule::in(array_keys(Label::MONEV_TYPE))],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
        ], [], ['type' => 'jenis monev', 'title' => 'judul']);
    }
}
