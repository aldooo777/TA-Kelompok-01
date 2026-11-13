<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use App\Models\Pendaftaran;
use App\Models\ProgramStudi;
use App\Support\NimMahasiswa;
use App\Support\Peran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Pendaftaran lowongan oleh mahasiswa (PB-04) dan progres pendaftaran (PB-05).
 */
class PendaftaranController extends Controller
{
    public function index()
    {
        $user = Peran::user();

        $pendaftaran = Pendaftaran::with(['lowongan.mitra', 'lowongan.jenisKegiatan'])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user?->id);
                if ($user?->nim) {
                    $q->orWhere('nim', $user->nim);
                }
            })
            ->latest()
            ->get();

        return view('mahasiswa.pendaftaran.index', compact('pendaftaran'));
    }

    public function create(string $slug, NimMahasiswa $nimMahasiswa)
    {
        $lowongan = Lowongan::terbit()->with(['mitra', 'jenisKegiatan', 'fieldTambahan'])
            ->where('slug', $slug)->firstOrFail();

        if (!$lowongan->dibuka) {
            return redirect()->route('lowongan.show', $slug)->with('warning', 'Pendaftaran untuk lowongan ini sudah ditutup.');
        }

        $user = Peran::user();
        $nim = $nimMahasiswa->sesiIni();

        if ($this->sudahMendaftar($lowongan, $user?->id, $nim)) {
            return redirect()->route('mahasiswa.pendaftaran.index')
                ->with('warning', 'Anda sudah mendaftar pada lowongan ini. Pantau statusnya di bawah.');
        }

        $cis = session('cis_user', []);

        return view('mahasiswa.pendaftaran.create', [
            'lowongan' => $lowongan,
            'prodiList' => ProgramStudi::orderBy('jenjang')->orderBy('nama')->get(),
            'awal' => [
                'nim' => $nim,
                'nama' => $cis['nama'] ?? $user?->name,
                'email' => $cis['email'] ?? $user?->email,
                'program_studi_id' => $user?->program_studi_id,
            ],
        ]);
    }

    public function store(Request $request, string $slug, NimMahasiswa $nimMahasiswa)
    {
        $lowongan = Lowongan::terbit()->with('fieldTambahan')->where('slug', $slug)->firstOrFail();

        abort_unless($lowongan->dibuka, 403, 'Pendaftaran untuk lowongan ini sudah ditutup.');

        $data = $request->validate([
            'nim' => ['required', 'string', 'max:30'],
            'program_studi_id' => ['required', 'exists:program_studi,id'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'ipk' => ['required', 'numeric', 'min:0', 'max:4'],
            'no_hp' => ['required', 'string', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'email' => ['required', 'email', 'max:150'],
            'dokumen' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'tambahan' => ['nullable', 'array'],
            'tambahan.*' => ['nullable', 'string', 'max:2000'],
            'persetujuan' => ['accepted'],
        ], [
            'no_hp.regex' => 'Nomor HP tidak valid. Contoh: 081234567890.',
            'dokumen.mimes' => 'Dokumen harus berupa satu file PDF.',
            'dokumen.max' => 'Ukuran dokumen maksimal 10 MB.',
            'persetujuan.accepted' => 'Centang pernyataan bahwa data yang diisi sudah benar.',
            'ipk.max' => 'IPK maksimal 4.00.',
        ]);

        // NIM harus milik akun yang sedang login (dicek ke CIS).
        $cek = $nimMahasiswa->verifikasi(trim($data['nim']));

        if (!$cek['ok']) {
            return back()->withErrors(['nim' => $cek['pesan']])->withInput();
        }

        $mhs = $cek['mahasiswa'];
        $user = Peran::user();

        if ($this->sudahMendaftar($lowongan, $user?->id, (string) $mhs['nim'])) {
            return redirect()->route('mahasiswa.pendaftaran.index')
                ->with('warning', 'Anda sudah mendaftar pada lowongan ini.');
        }

        // Simpan isian tambahan sebagai label => jawaban agar tetap terbaca
        // walaupun Staf UPT mengubah isian tambahan setelahnya.
        $jawaban = [];
        foreach ($lowongan->fieldTambahan as $field) {
            $nilai = trim((string) ($data['tambahan'][$field->id] ?? ''));
            if ($nilai !== '') {
                $jawaban[$field->label] = $nilai;
            }
        }

        $file = $request->file('dokumen');
        $path = $file->store("pendaftaran/{$lowongan->id}", 'local');

        Pendaftaran::create([
            'lowongan_id' => $lowongan->id,
            'user_id' => $user?->id,
            'nim' => (string) $mhs['nim'],
            'nama' => $mhs['nama'] ?? (session('cis_user')['nama'] ?? $user?->name),
            'program_studi_id' => $data['program_studi_id'],
            'semester' => $data['semester'],
            'ipk' => $data['ipk'],
            'no_hp' => $data['no_hp'],
            'email' => $data['email'],
            'dokumen_path' => $path,
            'dokumen_nama_asli' => mb_substr($file->getClientOriginalName(), 0, 255),
            'jawaban_tambahan' => $jawaban ?: null,
            'status' => Pendaftaran::TERDAFTAR,
        ]);

        if ($user && !$user->program_studi_id) {
            $user->forceFill(['program_studi_id' => $data['program_studi_id']])->save();
        }

        return redirect()->route('mahasiswa.pendaftaran.index')
            ->with('success', 'Pendaftaran berhasil dikirim. Status awal: Terdaftar.');
    }

    public function dokumen(Pendaftaran $pendaftaran)
    {
        $user = Peran::user();

        abort_unless(
            $pendaftaran->user_id === $user?->id || ($user?->nim && $pendaftaran->nim === $user->nim),
            403
        );

        return Storage::disk('local')->download($pendaftaran->dokumen_path, $pendaftaran->dokumen_nama_asli);
    }

    private function sudahMendaftar(Lowongan $lowongan, ?int $userId, ?string $nim): bool
    {
        return Pendaftaran::where('lowongan_id', $lowongan->id)
            ->where(function ($q) use ($userId, $nim) {
                $q->where('user_id', $userId ?? 0);
                if ($nim) {
                    $q->orWhere('nim', $nim);
                }
            })
            ->exists();
    }
}
