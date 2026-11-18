<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PublikController;
use App\Support\NimMahasiswa;
use App\Support\Peran;
use App\Support\Query;
use App\Support\Sql;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Pendaftaran lowongan oleh mahasiswa (applications), query SQL langsung. */
class PendaftaranController extends Controller
{
    public function index()
    {
        $user = Peran::user();

        return view('mahasiswa.pendaftaran.index', [
            'pendaftaran' => Sql::all(
                Query::PENDAFTARAN . ' WHERE ap.user_id = ? OR (?::text IS NOT NULL AND ap.nim = ?) ORDER BY ap.created_at DESC',
                [$user?->id ?? 0, $user?->nim, $user?->nim]
            ),
        ]);
    }

    public function create(string $slug, NimMahasiswa $nimMahasiswa)
    {
        $lowongan = PublikController::cariLowongan($slug);

        if ($redirect = $this->tolakJikaTidakBisa($lowongan)) {
            return $redirect;
        }

        $user = Peran::user();
        $nim = $nimMahasiswa->sesiIni();

        if ($this->sudahMendaftar($lowongan->id, $user?->id, $nim)) {
            return redirect($this->halamanSetelahDaftar())
                ->with('warning', 'Anda sudah mendaftar pada lowongan ini.');
        }

        $user = Peran::user();
        $cis = session('cis_user', []);

        return view('mahasiswa.pendaftaran.create', [
            'lowongan' => $lowongan,
            'fields' => $this->fields($lowongan->id),
            'prodiList' => Query::opsiProdi(),
            'awal' => [
                'nim' => $nim,
                'nama' => $cis['nama'] ?? $user?->name,
                'email' => $cis['email'] ?? $user?->email,
                'study_program_id' => $user?->study_program_id,
            ],
        ]);
    }

    public function store(Request $request, string $slug, NimMahasiswa $nimMahasiswa)
    {
        $lowongan = PublikController::cariLowongan($slug);

        if ($redirect = $this->tolakJikaTidakBisa($lowongan)) {
            return $redirect;
        }

        $data = $request->validate([
            'nim' => ['required', 'string', 'max:30'],
            'study_program_id' => ['required', 'exists:study_programs,id'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'gpa' => ['required', 'numeric', 'min:0', 'max:4'],
            'phone' => ['required', 'string', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'email' => ['required', 'email', 'max:150'],
            'dokumen' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'tambahan' => ['nullable', 'array'],
            'tambahan.*' => ['nullable', 'string', 'max:2000'],
            'persetujuan' => ['accepted'],
        ], [
            'phone.regex' => 'Nomor HP tidak valid. Contoh: 081234567890.',
            'dokumen.mimes' => 'Dokumen harus berupa satu file PDF.',
            'dokumen.max' => 'Ukuran dokumen maksimal 10 MB.',
            'persetujuan.accepted' => 'Centang pernyataan bahwa data yang diisi sudah benar.',
            'gpa.max' => 'IPK maksimal 4.00.',
        ]);

        // NIM harus milik akun yang sedang login (dicek ke CIS).
        $cek = $nimMahasiswa->verifikasi(trim($data['nim']));

        if (!$cek['ok']) {
            return back()->withErrors(['nim' => $cek['pesan']])->withInput();
        }

        $mhs = $cek['mahasiswa'];
        $user = Peran::user();

        if ($this->sudahMendaftar($lowongan->id, $user?->id, (string) $mhs['nim'])) {
            return redirect($this->halamanSetelahDaftar())->with('warning', 'Anda sudah mendaftar pada lowongan ini.');
        }

        // Simpan sebagai label => jawaban agar tetap terbaca walau isian tambahan diubah.
        $jawaban = [];
        foreach ($this->fields($lowongan->id) as $field) {
            $nilai = trim((string) ($data['tambahan'][$field->id] ?? ''));
            if ($nilai !== '') {
                $jawaban[$field->label] = $nilai;
            }
        }

        $file = $request->file('dokumen');

        Sql::insert('applications', [
            'vacancy_id' => $lowongan->id,
            'user_id' => $user?->id,
            'nim' => (string) $mhs['nim'],
            'name' => $mhs['nama'] ?? (session('cis_user')['nama'] ?? $user?->name),
            'study_program_id' => $data['study_program_id'],
            'semester' => $data['semester'],
            'gpa' => $data['gpa'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'document_path' => $file->store("pendaftaran/{$lowongan->id}", 'local'),
            'document_original_name' => mb_substr($file->getClientOriginalName(), 0, 255),
            'additional_answers' => $jawaban ?: null,
            'status' => 'registered',
        ]);

        if ($user && !$user->study_program_id) {
            Sql::update('users', $user->id, ['study_program_id' => $data['study_program_id']]);
        }

        return redirect($this->halamanSetelahDaftar())
            ->with('success', 'Pendaftaran berhasil dikirim. Status awal: Terdaftar.');
    }

    /** Halaman progres pendaftaran (PB-05) baru ada mulai Sprint 2; sebelumnya kembali ke dashboard. */
    private function halamanSetelahDaftar(): string
    {
        return sprint(2) ? route('mahasiswa.pendaftaran.index') : route('dashboard');
    }

    public function dokumen(int $id)
    {
        $user = Peran::user();
        $ap = Sql::oneOrFail('SELECT * FROM applications WHERE id = ?', [$id]);

        abort_unless((int) $ap->user_id === (int) $user?->id || ($user?->nim && $ap->nim === $user->nim), 403);

        return Storage::disk('local')->download($ap->document_path, $ap->document_original_name);
    }

    private function fields(int $vacancyId): \Illuminate\Support\Collection
    {
        return Sql::all('SELECT * FROM vacancy_additional_fields WHERE vacancy_id = ? AND is_active = true ORDER BY display_order', [$vacancyId]);
    }

    private function tolakJikaTidakBisa(object $lowongan)
    {
        if ($lowongan->registration_type === 'external') {
            return redirect()->away($lowongan->external_url);
        }

        if (!lowongan_dibuka($lowongan)) {
            return redirect()->route('portal.lowongan.show', $lowongan->slug)->with('warning', 'Pendaftaran untuk lowongan ini sudah ditutup.');
        }

        return null;
    }

    private function sudahMendaftar(int $vacancyId, ?int $userId, ?string $nim): bool
    {
        return (bool) Sql::value(
            'SELECT EXISTS (SELECT 1 FROM applications WHERE vacancy_id = ? AND (user_id = ? OR nim = ?))',
            [$vacancyId, $userId ?? 0, $nim ?? '']
        );
    }
}
