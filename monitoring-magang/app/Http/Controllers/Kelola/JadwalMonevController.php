<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Support\Query;
use App\Support\Sql;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Jadwal monev per periode (monev_schedules). */
class JadwalMonevController extends Controller
{
    public function index()
    {
        return view('kelola.monev.jadwal-index', [
            'jadwal' => Sql::all(
                "SELECT s.*, q.title, q.type, p.name AS period_name,
                        (SELECT COUNT(*) FROM pdlk_participants pp WHERE pp.period_id = s.period_id AND pp.is_active AND pp.status IN ('active','completed')) AS jumlah_peserta,
                        (SELECT COUNT(*) FROM monev_responses r WHERE r.schedule_id = s.id AND r.status = 'submitted') AS jumlah_terkirim
                 FROM monev_schedules s
                 JOIN monev_questionnaires q ON q.id = s.questionnaire_id
                 JOIN periods p ON p.id = s.period_id
                 WHERE s.is_active
                 ORDER BY s.open_at DESC"
            ),
        ]);
    }

    public function create()
    {
        return view('kelola.monev.jadwal-form', $this->dataForm(null));
    }

    public function store(Request $request)
    {
        Sql::insert('monev_schedules', $this->validasi($request));

        return redirect()->route('kelola.jadwal-monev.index')->with('success', 'Jadwal monev disimpan.');
    }

    public function edit(int $id)
    {
        return view('kelola.monev.jadwal-form', $this->dataForm(Sql::oneOrFail('SELECT * FROM monev_schedules WHERE id = ?', [$id])));
    }

    public function update(Request $request, int $id)
    {
        Sql::oneOrFail('SELECT id FROM monev_schedules WHERE id = ?', [$id]);
        Sql::update('monev_schedules', $id, $this->validasi($request, $id));

        return redirect()->route('kelola.jadwal-monev.index')->with('success', 'Jadwal monev diperbarui.');
    }

    public function destroy(int $id)
    {
        Sql::update('monev_schedules', $id, ['is_active' => false]);

        return back()->with('success', 'Jadwal monev dihapus dari daftar.');
    }

    private function dataForm(?object $jadwal): array
    {
        return [
            'jadwal' => $jadwal,
            'kuesionerList' => Sql::all(
                "SELECT id, CASE type WHEN 'mid' THEN 'Monev Tengah' ELSE 'Monev Akhir' END || ' — ' || title AS label
                 FROM monev_questionnaires WHERE is_active ORDER BY type, title"
            ),
            'periodeList' => Query::opsi('periods', 'name', 'start_date DESC'),
        ];
    }

    private function validasi(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'questionnaire_id' => [
                'required', 'exists:monev_questionnaires,id',
                Rule::unique('monev_schedules')->where('period_id', $request->input('period_id'))->ignore($id),
            ],
            'period_id' => ['required', 'exists:periods,id'],
            'open_at' => ['required', 'date'],
            'deadline_at' => ['required', 'date', 'after:open_at'],
        ], [
            'questionnaire_id.unique' => 'Kuesioner ini sudah dijadwalkan untuk periode tersebut.',
            'deadline_at.after' => 'Tenggat harus setelah waktu buka.',
        ], ['questionnaire_id' => 'kuesioner', 'period_id' => 'periode']);

        $data['is_closed'] = $request->boolean('is_closed');

        return $data;
    }
}
