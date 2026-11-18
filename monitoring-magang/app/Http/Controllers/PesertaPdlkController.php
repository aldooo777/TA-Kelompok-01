<?php

namespace App\Http\Controllers;

use App\Services\ParticipantService;
use App\Support\Label;
use App\Support\Peran;
use App\Support\Query;
use App\Support\Sql;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Mahasiswa aktif PDLK (pdlk_participants), query SQL langsung.
 *  - Staf UPT: semua prodi, input manual, ubah, status Tidak Aktif (force majeure).
 *  - Koordinator PDLK prodi: prodinya sendiri, input manual (PDLK mandiri), ubah.
 *  - Koordinator program studi: hanya melihat prodinya.
 * Tidak ada fitur batal; mahasiswa tidak dapat memasukkan dirinya sendiri.
 */
class PesertaPdlkController extends Controller
{
    public function index(Request $request)
    {
        [$where, $bind] = $this->cakupan();

        foreach (['prodi' => 'pp.study_program_id', 'jenis' => 'pp.activity_type_id', 'periode' => 'pp.period_id'] as $param => $kolom) {
            if ($v = $request->query($param)) {
                $where[] = "$kolom = ?";
                $bind[] = (int) $v;
            }
        }
        if ($s = $request->query('status')) {
            $where[] = 'pp.status = ?';
            $bind[] = $s;
        }
        if (($q = trim((string) $request->query('q'))) !== '') {
            $where[] = '(LOWER(pp.name) LIKE ? OR pp.nim LIKE ?)';
            array_push($bind, '%' . mb_strtolower($q) . '%', "%$q%");
        }

        return view('peserta-pdlk.index', [
            'peserta' => Sql::paginate(Query::PESERTA . ' WHERE ' . implode(' AND ', $where) . ' ORDER BY pp.name', $bind, 25),
            'prodiList' => Query::opsiProdi(Peran::cakupanProdi()),
            'jenisList' => Query::opsi('activity_types', 'name', 'display_order'),
            'periodeList' => Query::opsi('periods', 'name', 'start_date DESC'),
            'bolehUbahStatus' => Peran::aktif() === 'upt_staff',
            'bolehInput' => in_array(Peran::aktif(), ['upt_staff', 'pdlk_coordinator'], true),
        ]);
    }

    public function show(int $id)
    {
        $peserta = $this->cari($id);

        return view('peserta-pdlk.show', [
            'peserta' => $peserta,
            'riwayat' => Sql::all(
                'SELECT h.*, u.name AS oleh FROM participant_status_histories h
                 LEFT JOIN users u ON u.id = h.created_by
                 WHERE h.participant_id = ? ORDER BY h.created_at DESC', [$id]
            ),
            'bolehUbahStatus' => Peran::aktif() === 'upt_staff',
            'bolehUbah' => in_array(Peran::aktif(), ['upt_staff', 'pdlk_coordinator'], true),
        ]);
    }

    public function create()
    {
        return view('peserta-pdlk.form', $this->dataForm(null));
    }

    public function store(Request $request, ParticipantService $service)
    {
        $data = $this->validasi($request);

        $ada = Sql::value(
            "SELECT id FROM pdlk_participants WHERE nim = ? AND status = 'active' AND is_active AND period_id IS NOT DISTINCT FROM ?::bigint",
            [$data['nim'], $data['period_id'] ?? null]
        );
        if ($ada) {
            return back()->withInput()->withErrors(['nim' => 'Mahasiswa ini sudah tercatat aktif pada periode yang sama.']);
        }

        $id = Sql::transaction(function () use ($data, $service) {
            $id = Sql::insert('pdlk_participants', $data + [
                'source' => 'manual',
                'status' => 'active',
                'user_id' => Sql::value('SELECT id FROM users WHERE nim = ? LIMIT 1', [$data['nim']]),
            ]);
            $service->catatRiwayat($id, null, 'active', 'Diinput manual oleh ' . Peran::label());

            return $id;
        });

        return redirect()->route('peserta.show', $id)->with('success', "{$data['name']} dicatat sebagai mahasiswa aktif PDLK.");
    }

    public function edit(int $id)
    {
        return view('peserta-pdlk.form', $this->dataForm($this->cari($id)));
    }

    public function update(Request $request, int $id)
    {
        $this->cari($id);
        Sql::update('pdlk_participants', $id, $this->validasi($request, $id));

        return redirect()->route('peserta.show', $id)->with('success', 'Data mahasiswa PDLK diperbarui.');
    }

    public function nonaktifkan(Request $request, int $id, ParticipantService $service)
    {
        $data = $request->validate([
            'alasan' => ['required', 'string', 'min:5', 'max:1000'],
        ], ['alasan.required' => 'Alasan force majeure wajib diisi.']);

        $p = $this->cari($id);

        if ($p->status !== 'active') {
            return back()->with('warning', 'Hanya mahasiswa berstatus Aktif yang dapat diubah menjadi Tidak Aktif.');
        }

        $service->changeParticipantStatus($id, 'inactive', $data['alasan']);

        return back()->with('success', "{$p->name} sekarang berstatus Tidak Aktif.");
    }

    /** @return array{0: array, 1: array} batas data sesuai cakupan prodi peran aktif */
    private function cakupan(): array
    {
        $where = ['pp.is_active = true'];
        $bind = [];
        $prodi = Peran::cakupanProdi();

        if ($prodi !== null) {
            $where[] = 'pp.study_program_id IN (' . (implode(',', array_map('intval', $prodi)) ?: '0') . ')';
        }

        return [$where, $bind];
    }

    private function cari(int $id): object
    {
        [$where, $bind] = $this->cakupan();

        return Sql::oneOrFail(Query::PESERTA . ' WHERE ' . implode(' AND ', $where) . ' AND pp.id = ?', [...$bind, $id]);
    }

    private function dataForm(?object $peserta): array
    {
        return [
            'peserta' => $peserta,
            'prodiList' => Query::opsiProdi(Peran::cakupanProdi()),
            'jenisList' => Query::opsi('activity_types', 'name', 'display_order'),
            'periodeList' => Query::opsi('periods', 'name', 'start_date DESC'),
            'mitraList' => Sql::all('SELECT name FROM partners WHERE is_active = true ORDER BY name')->pluck('name'),
        ];
    }

    private function validasi(Request $request, ?int $id = null): array
    {
        $prodi = Peran::cakupanProdi();

        $data = $request->validate([
            'nim' => ['required', 'string', 'max:30'],
            'name' => ['required', 'string', 'max:150'],
            'study_program_id' => ['required', 'exists:study_programs,id', $prodi !== null ? Rule::in($prodi) : 'nullable'],
            'period_id' => ['nullable', 'exists:periods,id'],
            'activity_type_id' => ['required', 'exists:activity_types,id'],
            'nama_mitra' => ['required', 'string', 'max:200'],
            'program_category' => ['nullable', Rule::in(array_keys(Label::PROGRAM_CATEGORY))],
            'position' => ['nullable', 'string', 'max:150'],
            'mentor_name' => ['nullable', 'string', 'max:150'],
            'advisor_name' => ['nullable', 'string', 'max:150'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'location' => ['nullable', 'string', 'max:200'],
        ], [
            'study_program_id.in' => 'Anda hanya dapat menginput mahasiswa dari program studi Anda.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ], ['nama_mitra' => 'mitra', 'study_program_id' => 'program studi', 'activity_type_id' => 'jenis kegiatan']);

        $data['partner_id'] = Query::mitraDariNama($data['nama_mitra']);
        unset($data['nama_mitra']);

        return $data;
    }
}
