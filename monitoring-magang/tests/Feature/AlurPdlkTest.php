<?php

namespace Tests\Feature;

use App\Models\JenisKegiatan;
use App\Models\Lowongan;
use App\Models\Mitra;
use App\Models\Pendaftaran;
use App\Models\PesertaPdlk;
use App\Models\Postingan;
use App\Models\ProgramStudi;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Services\CisNewService;
use App\Services\CisService;
use App\Support\Peran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

/**
 * Skenario black box Sprint 1 (PB-01 s.d. PB-07) tanpa memanggil CIS asli.
 */
class AlurPdlkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Storage::fake('local');
    }

    /** Login tiruan: identitas CIS + peran PDLK di session. */
    private function sebagai(string $cisRole, array $peran = [], array $extra = []): User
    {
        $user = User::create([
            'username' => $extra['username'] ?? 'u' . uniqid(),
            'name' => $extra['nama'] ?? 'Pengguna Uji',
            'email' => $extra['email'] ?? uniqid() . '@del.ac.id',
            'cis_role' => $cisRole,
            'nim' => $extra['nim'] ?? null,
        ]);

        foreach ($peran as $kode => $prodiId) {
            UserRole::create([
                'user_id' => $user->id,
                'role_id' => Role::where('kode', $kode)->value('id'),
                'program_studi_id' => $prodiId,
            ]);
        }

        $this->withSession([
            'cis_user' => ['username' => $user->username, 'email' => $user->email, 'role' => $cisRole, 'nama' => $user->name]
                + ($user->nim ? ['nim' => $user->nim] : []),
        ]);

        // Hitung peran ke session seperti setelah login.
        session(['cis_user' => ['username' => $user->username, 'email' => $user->email, 'role' => $cisRole, 'nama' => $user->name]
            + ($user->nim ? ['nim' => $user->nim] : [])]);
        Peran::masuk($user);
        $this->withSession(session()->all());

        return $user;
    }

    private function lowongan(array $atribut = []): Lowongan
    {
        return Lowongan::create($atribut + [
            'judul' => 'Magang Backend',
            'mitra_id' => Mitra::dariNama('PT Mitra Uji')->id,
            'jenis_kegiatan_id' => JenisKegiatan::where('slug', 'magang')->value('id'),
            'deskripsi' => 'Deskripsi',
            'kriteria' => 'IPK minimal 3.00',
            'is_published' => true,
        ]);
    }

    private function cisPalsu(string $nim, string $email, string $nama = 'Aldo Uji'): void
    {
        $cis = Mockery::mock(CisService::class);
        $cis->shouldReceive('findStudentByNim')->andReturnUsing(
            fn ($n) => $n === $nim ? ['nim' => $nim, 'nama' => $nama, 'email' => $email] : null
        );
        $this->app->instance(CisService::class, $cis);
    }

    public function test_guest_dapat_melihat_halaman_publik_tanpa_login(): void
    {
        $l = $this->lowongan();
        $p = Postingan::create(['jenis' => 'berita', 'judul' => 'Pembekalan Magang', 'narasi' => 'Isi berita']);

        $this->get('/')->assertOk()->assertSee('Magang Backend')->assertSee('Pembekalan Magang');
        $this->get('/lowongan')->assertOk()->assertSee('PT Mitra Uji');
        $this->get('/lowongan/' . $l->slug)->assertOk()->assertSee('IPK minimal 3.00')->assertSee('Login CIS untuk Mendaftar');
        $this->get('/berita/' . $p->slug)->assertOk()->assertSee('Isi berita');
        $this->get('/tentang-pdlk')->assertOk()->assertSee('Visi UPT PP-ESTEM');
    }

    public function test_guest_yang_mendaftar_diarahkan_ke_login(): void
    {
        $l = $this->lowongan();

        $this->get("/lowongan/{$l->slug}/daftar")->assertRedirect('/login');
        $this->get('/kelola/postingan')->assertRedirect('/login');
    }

    public function test_login_cis_menyimpan_user_dan_peran(): void
    {
        $baru = Mockery::mock(CisNewService::class);
        $baru->shouldReceive('login')->andThrow(new \RuntimeException('gagal'));
        $this->app->instance(CisNewService::class, $baru);

        $lama = Mockery::mock(CisService::class);
        $lama->shouldReceive('login')->andReturn([
            'result' => true,
            'token' => 't',
            'user' => ['user_id' => 77, 'username' => 'aldo', 'email' => 'aldo@students.del.ac.id', 'role' => 'Mahasiswa', 'jabatan' => 'Mahasiswa'],
        ]);
        $this->app->instance(CisService::class, $lama);

        $this->post('/login', ['username' => 'aldo', 'password' => 'rahasia'])->assertRedirect('/dashboard');

        $user = User::where('username', 'aldo')->firstOrFail();
        $this->assertSame(77, (int) $user->cis_user_id);
        $this->assertNull($user->password, 'Password CIS tidak boleh disimpan.');
        $this->assertSame('mahasiswa', session('peran_aktif'));
    }

    public function test_staf_upt_memposting_berita_dengan_foto_dan_lowongan_dengan_field_tambahan(): void
    {
        $this->sebagai('Pegawai', [Role::STAF_UPT => null]);

        $this->post('/kelola/postingan', [
            'jenis' => 'pengumuman',
            'judul' => 'Pembukaan Magang',
            'narasi' => 'Silakan cek lowongan.',
            'foto' => UploadedFile::fake()->image('foto.jpg'),
            'is_published' => 1,
        ])->assertRedirect('/kelola/postingan');

        $post = Postingan::firstOrFail();
        Storage::disk('public')->assertExists($post->foto_path);

        $this->post('/kelola/lowongan', [
            'judul' => 'Magang Data',
            'nama_mitra' => 'PT Baru Sekali',
            'jenis_kegiatan_id' => JenisKegiatan::where('slug', 'magang')->value('id'),
            'deskripsi' => 'Deskripsi',
            'kriteria' => 'Kriteria',
            'is_published' => 1,
            'field_tambahan' => [['label' => 'Link GitHub', 'tipe' => 'url'], ['label' => '', 'tipe' => 'text']],
        ])->assertRedirect('/kelola/lowongan');

        $l = Lowongan::where('judul', 'Magang Data')->firstOrFail();
        $this->assertSame('PT Baru Sekali', $l->mitra->nama);
        $this->assertCount(1, $l->fieldTambahan);
    }

    public function test_mahasiswa_tidak_dapat_membuka_menu_staf(): void
    {
        $this->sebagai('Mahasiswa');

        $this->get('/kelola/lowongan')->assertForbidden();
        $this->get('/peserta-pdlk')->assertForbidden();
    }

    public function test_mahasiswa_mendaftar_lalu_lulus_menjadi_mahasiswa_aktif(): void
    {
        $l = $this->lowongan();
        $l->fieldTambahan()->create(['label' => 'Link GitHub', 'tipe' => 'url', 'urutan' => 1]);
        $prodi = ProgramStudi::where('kode', 'D3TI')->first();

        $this->cisPalsu('11324001', 'aldo@students.del.ac.id');
        $mhs = $this->sebagai('Mahasiswa', [], ['email' => 'aldo@students.del.ac.id']);

        // Belum aktif -> monitoring terkunci
        $this->get('/monitoring')->assertRedirect('/dashboard');

        $this->get("/lowongan/{$l->slug}/daftar")->assertOk()->assertSee('Link GitHub');

        $data = [
            'nim' => '11324001',
            'program_studi_id' => $prodi->id,
            'semester' => 5,
            'ipk' => '3.45',
            'no_hp' => '081234567890',
            'email' => 'aldo@students.del.ac.id',
            'dokumen' => UploadedFile::fake()->create('berkas.pdf', 500, 'application/pdf'),
            'tambahan' => [$l->fieldTambahan->first()->id => 'https://github.com/aldo'],
            'persetujuan' => 1,
        ];

        $this->post("/lowongan/{$l->slug}/daftar", $data)->assertRedirect('/pendaftaran-saya');

        $p = Pendaftaran::firstOrFail();
        $this->assertSame('terdaftar', $p->status);
        $this->assertSame('Aldo Uji', $p->nama, 'Nama diambil dari CIS.');
        $this->assertSame(['Link GitHub' => 'https://github.com/aldo'], $p->jawaban_tambahan);
        $this->assertSame('11324001', $mhs->fresh()->nim);

        // Pendaftaran ganda ditolak
        $this->post("/lowongan/{$l->slug}/daftar", $data);
        $this->assertSame(1, Pendaftaran::count());

        $this->get('/pendaftaran-saya')->assertOk()->assertSee('Terdaftar');

        // Staf UPT memproses -> lulus
        $this->sebagai('Pegawai', [Role::STAF_UPT => null]);
        $this->patch('/kelola/pendaftaran/status-massal', ['ids' => [$p->id], 'status' => 'proses'])->assertSessionHas('success');
        $this->patch("/kelola/pendaftaran/{$p->id}/status", ['status' => 'lulus'])->assertSessionHas('success');

        $peserta = PesertaPdlk::where('nim', '11324001')->firstOrFail();
        $this->assertSame('aktif', $peserta->status);
        $this->assertSame('lowongan', $peserta->sumber);
        $this->assertSame($prodi->id, $peserta->program_studi_id);
        $this->assertCount(1, $peserta->riwayatStatus);

        // Tidak ada pembatalan setelah lulus
        $this->patch("/kelola/pendaftaran/{$p->id}/status", ['status' => 'tidak_lulus'])->assertSessionHas('warning');
        $this->assertSame('lulus', $p->fresh()->status);

        // Ekspor spreadsheet untuk mitra
        $csv = $this->get('/kelola/pendaftaran/ekspor')->assertOk()->streamedContent();
        $this->assertStringContainsString('11324001', $csv);
        $this->assertStringContainsString('Link GitHub', $csv);

        // Mahasiswa sekarang dapat membuka monitoring
        $this->cisPalsu('11324001', 'aldo@students.del.ac.id');
        $this->sebagai('Mahasiswa', [], ['email' => 'aldo2@students.del.ac.id', 'nim' => '11324001']);
        $this->get('/monitoring')->assertOk();
    }

    public function test_nim_milik_orang_lain_ditolak(): void
    {
        $l = $this->lowongan();
        $this->cisPalsu('11324999', 'orang.lain@students.del.ac.id');
        $this->sebagai('Mahasiswa', [], ['email' => 'aldo@students.del.ac.id']);

        $this->post("/lowongan/{$l->slug}/daftar", [
            'nim' => '11324999',
            'program_studi_id' => ProgramStudi::first()->id,
            'semester' => 5, 'ipk' => '3.1', 'no_hp' => '081234567890',
            'email' => 'aldo@students.del.ac.id',
            'dokumen' => UploadedFile::fake()->create('b.pdf', 100, 'application/pdf'),
            'persetujuan' => 1,
        ])->assertSessionHasErrors('nim');

        $this->assertSame(0, Pendaftaran::count());
    }

    public function test_lowongan_yang_sudah_ditutup_tidak_menerima_pendaftaran(): void
    {
        $l = $this->lowongan(['tanggal_tutup' => now()->subDay()]);
        $this->sebagai('Mahasiswa');

        $this->get("/lowongan/{$l->slug}/daftar")->assertRedirect("/lowongan/{$l->slug}");
    }

    public function test_koordinator_hanya_melihat_mahasiswa_prodinya(): void
    {
        $ti = ProgramStudi::where('kode', 'D3TI')->first();
        $if = ProgramStudi::where('kode', 'S1IF')->first();

        PesertaPdlk::create(['nim' => '1', 'nama' => 'Mahasiswa TI', 'program_studi_id' => $ti->id, 'status' => 'aktif']);
        PesertaPdlk::create(['nim' => '2', 'nama' => 'Mahasiswa IF', 'program_studi_id' => $if->id, 'status' => 'aktif']);

        $this->sebagai('Dosen', [Role::KOORDINATOR_PRODI => $ti->id]);

        $this->get('/peserta-pdlk')->assertOk()->assertSee('Mahasiswa TI')->assertDontSee('Mahasiswa IF');
        $this->get('/dashboard')->assertOk()->assertSee('Koordinator Program Studi');
    }

    public function test_staf_upt_mengubah_status_tidak_aktif_karena_force_majeure(): void
    {
        $ps = PesertaPdlk::create(['nim' => '3', 'nama' => 'Mahasiswa Sakit', 'status' => 'aktif']);
        $this->sebagai('Pegawai', [Role::STAF_UPT => null]);

        $this->patch("/peserta-pdlk/{$ps->id}/tidak-aktif", ['alasan' => ''])->assertSessionHasErrors('alasan');
        $this->patch("/peserta-pdlk/{$ps->id}/tidak-aktif", ['alasan' => 'Sakit, rawat inap'])->assertSessionHas('success');

        $this->assertSame('tidak_aktif', $ps->fresh()->status);
        $this->assertSame('Sakit, rawat inap', $ps->fresh()->alasan_tidak_aktif);
        $this->assertSame(1, $ps->riwayatStatus()->count());
    }

    public function test_dashboard_setiap_peran_dapat_dibuka(): void
    {
        $this->sebagai('Pegawai', [Role::STAF_UPT => null]);
        $this->get('/dashboard')->assertOk()->assertSee('Kelola Pendaftar');

        $this->sebagai('Dosen');
        $this->get('/dashboard')->assertOk()->assertSee('belum memiliki peran');

        $this->cisPalsu('x', 'x');
        $this->sebagai('Mahasiswa');
        $this->get('/dashboard')->assertOk()->assertSee('Belum mendaftar');
    }

    public function test_mahasiswa_mandiri_tidak_tampil_belum_mendaftar(): void
    {
        PesertaPdlk::create(['nim' => '42324032', 'nama' => 'Aldo Steven Dion Sitorus', 'status' => 'aktif', 'sumber' => 'mandiri']);
        $this->sebagai('Mahasiswa', [], ['nim' => '42324032', 'username' => 'if324032']);

        $this->get('/dashboard')->assertOk()
            ->assertSee('Tanpa pendaftaran')
            ->assertDontSee('Belum mendaftar');
    }

    public function test_prodi_kosong_dilengkapi_dari_cis_agar_tampil_di_koordinator(): void
    {
        $ti = ProgramStudi::where('kode', 'D3TI')->first();
        PesertaPdlk::create(['nim' => '42324032', 'nama' => 'Aldo Lama', 'status' => 'aktif']);

        $cis = Mockery::mock(CisService::class);
        $cis->shouldReceive('findStudentByNim')->andReturn(['nim' => '42324032', 'nama' => 'Aldo Lama', 'email' => 'a@b', 'prodi' => 'DIII Teknologi Informasi']);
        $this->app->instance(CisService::class, $cis);

        $this->artisan('pdlk:lengkapi-prodi')->assertSuccessful();
        $this->assertSame($ti->id, PesertaPdlk::first()->program_studi_id);

        $this->sebagai('Dosen', [Role::KOORDINATOR_PRODI => $ti->id]);
        $this->get('/peserta-pdlk')->assertSee('Aldo Lama');
    }

    public function test_kolom_audit_terisi_otomatis(): void
    {
        $staf = $this->sebagai('Pegawai', [Role::STAF_UPT => null]);

        $this->post('/kelola/postingan', ['jenis' => 'berita', 'judul' => 'Audit', 'narasi' => 'Isi', 'is_published' => 1]);

        $p = Postingan::firstOrFail();
        $this->assertSame($staf->id, (int) $p->created_by);
        $this->assertNotNull($p->submitted_at);
        $this->assertNotNull($p->created_at);
        $this->assertSame($staf->id, (int) Mitra::dariNama('PT Audit')->created_by);
    }

    public function test_perintah_beri_peran(): void
    {
        $this->artisan('pdlk:beri-peran', ['username' => 'monalisa', 'peran' => 'staf_upt'])->assertSuccessful();
        $this->artisan('pdlk:beri-peran', ['username' => 'kaprodi', 'peran' => 'koordinator_prodi'])->assertFailed();
        $this->artisan('pdlk:beri-peran', ['username' => 'kaprodi', 'peran' => 'koordinator_prodi', '--prodi' => 'd3ti'])->assertSuccessful();

        $this->assertSame(2, UserRole::count());
    }
}
