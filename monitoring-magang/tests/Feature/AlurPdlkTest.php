<?php

namespace Tests\Feature;

use App\Services\CisNewService;
use App\Services\CisService;
use App\Support\Peran;
use App\Support\Sql;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

/**
 * Skenario black box alur PDLK tanpa memanggil CIS asli.
 * Skema database dimuat dari database/sql/skema_pdlk.sql (lihat TestCase).
 */
class AlurPdlkTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Storage::fake('local');
    }

    private function id(string $sql, array $b = []): int
    {
        return (int) Sql::value($sql, $b);
    }

    private function prodi(string $kode): int
    {
        return $this->id('SELECT id FROM study_programs WHERE code = ?', [$kode]);
    }

    /** Login tiruan: identitas CIS + peran PDLK di session. */
    private function sebagai(string $cisRole, array $peran = [], array $extra = []): int
    {
        session()->flush();
        $userId = Sql::insert('users', [
            'username' => $extra['username'] ?? 'u' . uniqid(),
            'name' => $extra['nama'] ?? 'Pengguna Uji',
            'email' => $extra['email'] ?? uniqid() . '@del.ac.id',
            'cis_role' => $cisRole,
            'nim' => $extra['nim'] ?? null,
        ]);

        foreach ($peran as $kode => $prodiId) {
            Sql::insert('user_roles', [
                'user_id' => $userId,
                'role_id' => $this->id('SELECT id FROM roles WHERE code = ?', [$kode]),
                'study_program_id' => $prodiId,
            ]);
        }

        $u = Sql::one('SELECT * FROM users WHERE id = ?', [$userId]);
        session(['cis_user' => ['username' => $u->username, 'email' => $u->email, 'role' => $cisRole]
            + (isset($extra['nama']) ? ['nama' => $extra['nama']] : [])
            + ($u->nim ? ['nim' => $u->nim] : [])]);
        Peran::masuk($userId);
        $this->withSession(session()->all());

        return $userId;
    }

    /** Membuat lowongan (announcements + vacancy_details). Mengembalikan [vacancy_id, slug]. */
    private function lowongan(array $detail = [], array $pengumuman = []): array
    {
        $judul = $pengumuman['title'] ?? 'Magang Backend';
        $slug = \Illuminate\Support\Str::slug($judul) . '-' . uniqid();
        $aId = Sql::insert('announcements', $pengumuman + [
            'type' => 'vacancy', 'title' => $judul, 'slug' => $slug,
            'content' => 'Deskripsi kegiatan', 'status' => 'published', 'published_at' => now()->subMinute(),
        ]);
        $vId = Sql::insert('vacancy_details', $detail + [
            'announcement_id' => $aId,
            'partner_id' => \App\Support\Query::mitraDariNama('PT Mitra Uji'),
            'activity_type_id' => $this->id("SELECT id FROM activity_types WHERE slug = 'magang'"),
            'criteria' => 'IPK minimal 3.00',
            'registration_type' => 'internal',
        ]);

        return [$vId, $slug];
    }

    private function cisPalsu(string $nim, string $email, string $nama = 'Aldo Uji', ?string $prodi = null): void
    {
        $cis = Mockery::mock(CisService::class);
        $cis->shouldReceive('findStudentByNim')->andReturnUsing(
            fn ($n) => $n === $nim ? ['nim' => $nim, 'nama' => $nama, 'email' => $email, 'prodi' => $prodi] : null
        );
        $this->app->instance(CisService::class, $cis);
    }

    public function test_skema_dan_data_referensi_sesuai_query_pgadmin(): void
    {
        $this->assertSame(27, $this->id(
            "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = current_schema() AND table_name IN (
              'roles','study_programs','users','user_roles','periods','activity_types','partners','organization_positions',
              'contacts','galleries','gallery_items','announcements','vacancy_details','vacancy_additional_fields',
              'announcement_attachments','announcement_result_items','applications','pdlk_participants',
              'participant_status_histories','monev_questionnaires','monev_questions','monev_schedules','monev_responses','monev_answers',
              'activity_submissions','submission_histories','notifications')"
        ));
        $this->assertSame(27, $this->id(
            "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = current_schema() AND table_type = 'BASE TABLE'"
        ));
        $this->assertSame(1, $this->id(
            "SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = current_schema()
             AND table_name = 'pdlk_participants' AND column_name = 'mentor_contact'"
        ));
        $this->assertEqualsCanonicalizing(
            ['student', 'upt_staff', 'pdlk_coordinator', 'program_head'],
            Sql::all('SELECT code FROM roles')->pluck('code')->all()
        );
        $this->assertSame(7, $this->id('SELECT COUNT(*) FROM activity_types'));
    }

    public function test_guest_dapat_melihat_halaman_publik_tanpa_login(): void
    {
        [, $slug] = $this->lowongan();
        usleep(1200000); // jeda > 1 detik: berita tetap tampil walau dibuat setelah transaksi dimulai
        Sql::insert('announcements', ['type' => 'news', 'title' => 'Pembekalan Magang', 'slug' => 'pembekalan', 'content' => 'Isi berita', 'status' => 'published', 'published_at' => now()]);
        Sql::insert('announcements', ['type' => 'news', 'title' => 'Masih Draf', 'slug' => 'draf', 'content' => 'x', 'status' => 'draft']);
        Sql::insert('organization_positions', ['position_name' => 'Kepala UPT', 'officer_name' => 'Ibu Kepala']);
        Sql::insert('contacts', ['name' => 'Email UPT', 'contact_type' => 'email', 'contact_value' => 'upt@del.ac.id']);
        $g = Sql::insert('galleries', ['title' => 'Pembekalan 2026', 'status' => 'published']);

        $this->get('/')->assertOk()->assertSee('Magang Backend')->assertSee('Pembekalan Magang')->assertDontSee('Masih Draf')->assertSee('Pembekalan 2026');
        $this->get('/lowongan')->assertOk()->assertSee('PT Mitra Uji');
        $this->get("/lowongan/$slug")->assertOk()->assertSee('IPK minimal 3.00')->assertSee('Login untuk Mendaftar');
        $this->get('/berita/pembekalan')->assertOk()->assertSee('Isi berita');
        $this->get('/berita/draf')->assertNotFound();
        $this->get('/tentang-pdlk')->assertOk()->assertSee('Ibu Kepala')->assertSee('upt@del.ac.id');
        $this->get("/galeri/$g")->assertOk()->assertSee('Pembekalan 2026');
    }

    public function test_guest_yang_mendaftar_diarahkan_ke_login(): void
    {
        [, $slug] = $this->lowongan();

        $this->get("/lowongan/$slug/daftar")->assertRedirect('/login');
        $this->get('/kelola/postingan')->assertRedirect('/login');
    }

    public function test_login_cis_menyimpan_user_dan_peran(): void
    {
        $baru = Mockery::mock(CisNewService::class);
        $baru->shouldReceive('login')->andThrow(new \RuntimeException('gagal'));
        $this->app->instance(CisNewService::class, $baru);

        $lama = Mockery::mock(CisService::class);
        $lama->shouldReceive('login')->andReturn([
            'result' => true, 'token' => 't',
            'user' => ['user_id' => 77, 'username' => 'aldo', 'email' => 'aldo@students.del.ac.id', 'role' => 'Mahasiswa', 'jabatan' => 'Mahasiswa'],
        ]);
        $this->app->instance(CisService::class, $lama);

        $this->post('/login', ['username' => 'aldo', 'password' => 'rahasia'])->assertRedirect('/dashboard');

        $u = Sql::one("SELECT * FROM users WHERE username = 'aldo'");
        $this->assertSame(77, (int) $u->cis_user_id);
        $this->assertSame(['Mahasiswa'], json_decode($u->cis_position, true));
        $this->assertSame('student', session('peran_aktif'));
    }

    public function test_login_memberi_pesan_jelas_bila_server_cis_tidak_terjangkau(): void
    {
        config(['sdi.cis_api_url' => 'https://cis.del.ac.id/api']);
        $baru = Mockery::mock(CisNewService::class);
        $baru->shouldReceive('login')->andReturn(['result' => false]);
        $this->app->instance(CisNewService::class, $baru);

        $lama = Mockery::mock(CisService::class);
        $lama->shouldReceive('login')->andThrow(new \Illuminate\Http\Client\ConnectionException('cURL error 28: timeout'));
        $this->app->instance(CisService::class, $lama);

        $this->from('/login')->post('/login', ['username' => 'mhs', 'password' => 'rahasia'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['username' => 'Server CIS (cis.del.ac.id) tidak dapat dihubungi dari jaringan ini. Gunakan jaringan kampus IT Del atau VPN, lalu coba lagi.']);
        $this->assertFalse(session()->has('cis_user'));
    }

    public function test_staf_upt_membuat_berita_dan_lowongan_dengan_kolom_audit(): void
    {
        $staf = $this->sebagai('Pegawai', ['upt_staff' => null]);

        $this->post('/kelola/postingan', [
            'title' => 'Pembukaan Magang',
            'content' => 'Silakan cek lowongan.',
            'foto' => UploadedFile::fake()->image('foto.jpg'),
            'lampiran' => [UploadedFile::fake()->create('panduan.pdf', 100, 'application/pdf')],
            'terbit' => 1,
        ])->assertRedirect('/kelola/postingan');

        $b = Sql::one("SELECT * FROM announcements WHERE type = 'news'");
        Storage::disk('public')->assertExists($b->cover_image);
        $this->assertSame('published', $b->status);
        $this->assertNotNull($b->published_at);
        $this->assertSame($staf, (int) $b->created_by);
        $this->assertSame($staf, (int) $b->updated_by);
        $this->assertSame(1, $this->id('SELECT COUNT(*) FROM announcement_attachments WHERE announcement_id = ?', [$b->id]));

        $this->post('/kelola/lowongan', [
            'title' => 'Magang Data',
            'content' => 'Deskripsi',
            'nama_mitra' => 'PT Baru Sekali',
            'activity_type_id' => $this->id("SELECT id FROM activity_types WHERE slug = 'magang'"),
            'criteria' => 'Kriteria',
            'registration_type' => 'internal',
            'terbit' => 1,
            'field_tambahan' => [['label' => 'Link GitHub', 'field_type' => 'url'], ['label' => '', 'field_type' => 'text']],
        ])->assertRedirect('/kelola/lowongan');

        $v = Sql::one(\App\Support\Query::LOWONGAN . " WHERE a.title = 'Magang Data'");
        $this->assertSame('PT Baru Sekali', $v->partner_name);
        $this->assertSame($staf, $this->id('SELECT created_by FROM partners WHERE id = ?', [$v->partner_id]));
        $f = Sql::all('SELECT * FROM vacancy_additional_fields WHERE vacancy_id = ?', [$v->id]);
        $this->assertCount(1, $f);
        $this->assertFalse((bool) $f->first()->is_required);

        $this->get("/kelola/lowongan/{$v->id}/edit")->assertOk()->assertSee('Link GitHub');
    }

    public function test_crud_data_master_dan_galeri(): void
    {
        $this->sebagai('Pegawai', ['upt_staff' => null]);

        $this->get('/kelola/master/mitra')->assertOk();
        $this->post('/kelola/master/mitra', ['name' => 'PT Master', 'cooperation_status' => 'cooperating'])->assertRedirect('/kelola/master/mitra');
        $id = $this->id("SELECT id FROM partners WHERE name = 'PT Master'");
        $this->post('/kelola/master/mitra', ['name' => 'PT Master'])->assertSessionHasErrors('name');
        $this->put("/kelola/master/mitra/$id", ['name' => 'PT Master Baru', 'cooperation_status' => 'in_process'])->assertRedirect();
        $this->assertSame('in_process', Sql::value('SELECT cooperation_status FROM partners WHERE id = ?', [$id]));
        $this->patch("/kelola/master/mitra/$id/status")->assertRedirect();
        $this->assertFalse((bool) Sql::value('SELECT is_active FROM partners WHERE id = ?', [$id]));

        $this->post('/kelola/master/periode', [
            'name' => 'Genap 2026', 'academic_year' => '2025/2026', 'semester' => 'genap',
            'start_date' => '2026-06-01', 'end_date' => '2026-02-01',
        ])->assertSessionHasErrors('end_date');

        $this->get('/kelola/master/peran/tambah')->assertNotFound();

        $this->post('/kelola/galeri', [
            'title' => 'Galeri Uji', 'status' => 'published',
            'items' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg')],
        ])->assertRedirect();
        $g = $this->id("SELECT id FROM galleries WHERE title = 'Galeri Uji'");
        $this->assertSame(2, $this->id('SELECT COUNT(*) FROM gallery_items WHERE gallery_id = ?', [$g]));
    }

    public function test_staf_upt_memberi_dan_mencabut_peran_lewat_halaman(): void
    {
        $this->sebagai('Pegawai', ['upt_staff' => null]);
        $target = Sql::insert('users', ['username' => 'hernawati', 'name' => 'Hernawati', 'cis_role' => 'Dosen']);
        $head = $this->id("SELECT id FROM roles WHERE code = 'program_head'");

        $this->get('/kelola/pengguna')->assertOk()->assertSee('Hernawati');
        $this->post("/kelola/pengguna/$target/peran", ['role_id' => $head])->assertSessionHasErrors('study_program_id');
        $this->post("/kelola/pengguna/$target/peran", ['role_id' => $head, 'study_program_id' => $this->prodi('D3TI')])->assertSessionHas('success');
        $this->post("/kelola/pengguna/$target/peran", ['role_id' => $head, 'study_program_id' => $this->prodi('D3TI')]);
        $this->assertSame(1, $this->id('SELECT COUNT(*) FROM user_roles WHERE user_id = ?', [$target]));

        $ur = $this->id('SELECT id FROM user_roles WHERE user_id = ?', [$target]);
        $this->delete("/kelola/pengguna/$target/peran/$ur")->assertSessionHas('success');
        $this->assertSame(0, $this->id('SELECT COUNT(*) FROM user_roles WHERE user_id = ?', [$target]));
    }

    public function test_lowongan_eksternal_mengarah_ke_situs_mitra(): void
    {
        [, $slug] = $this->lowongan(['registration_type' => 'external', 'external_url' => 'https://mitra.example/daftar']);

        $this->get("/lowongan/$slug")->assertOk()->assertSee('Daftar di Situs Mitra')->assertSee('https://mitra.example/daftar');

        $this->sebagai('Mahasiswa');
        $this->get("/lowongan/$slug/daftar")->assertRedirect('https://mitra.example/daftar');
    }

    public function test_mahasiswa_tidak_dapat_membuka_menu_staf(): void
    {
        $this->sebagai('Mahasiswa');

        $this->get('/kelola/lowongan')->assertForbidden();
        $this->get('/kelola/master/mitra')->assertForbidden();
        $this->get('/peserta-pdlk')->assertForbidden();
        $this->get('/peserta-pdlk/tambah')->assertForbidden();
    }

    public function test_mahasiswa_mendaftar_lalu_lulus_menjadi_mahasiswa_aktif(): void
    {
        [$vId, $slug] = $this->lowongan();
        $fieldId = Sql::insert('vacancy_additional_fields', ['vacancy_id' => $vId, 'label' => 'Link GitHub', 'field_type' => 'url', 'display_order' => 1]);
        $prodi = $this->prodi('D3TI');

        $this->cisPalsu('11324001', 'aldo@students.del.ac.id');
        $mhs = $this->sebagai('Mahasiswa', [], ['email' => 'aldo@students.del.ac.id']);

        $this->get('/monitoring')->assertRedirect('/dashboard');
        $this->get("/lowongan/$slug/daftar")->assertOk()->assertSee('Link GitHub');

        $data = [
            'nim' => '11324001', 'study_program_id' => $prodi, 'semester' => 5, 'gpa' => '3.45',
            'phone' => '081234567890', 'email' => 'aldo@students.del.ac.id',
            'dokumen' => UploadedFile::fake()->create('berkas.pdf', 500, 'application/pdf'),
            'tambahan' => [$fieldId => 'https://github.com/aldo'],
            'persetujuan' => 1,
        ];

        $this->post("/lowongan/$slug/daftar", $data)->assertRedirect('/pendaftaran-saya');

        $app = Sql::one('SELECT * FROM applications');
        $this->assertSame('registered', $app->status);
        $this->assertSame('Aldo Uji', $app->name);
        $this->assertSame(['Link GitHub' => 'https://github.com/aldo'], json_decode($app->additional_answers, true));
        $this->assertSame($mhs, (int) $app->created_by);
        $this->assertSame('11324001', Sql::value('SELECT nim FROM users WHERE id = ?', [$mhs]));

        $this->post("/lowongan/$slug/daftar", $data);
        $this->assertSame(1, $this->id('SELECT COUNT(*) FROM applications'));

        $this->get('/pendaftaran-saya')->assertOk()->assertSee('Terdaftar');

        $staf = $this->sebagai('Pegawai', ['upt_staff' => null]);
        $this->patch('/kelola/pendaftaran/status-massal', ['ids' => [$app->id], 'status' => 'processing'])->assertSessionHas('success');
        $this->patch("/kelola/pendaftaran/{$app->id}/status", ['status' => 'passed'])->assertSessionHas('success');

        $app = Sql::one('SELECT * FROM applications WHERE id = ?', [$app->id]);
        $this->assertSame($staf, (int) $app->processed_by);
        $this->assertSame($staf, (int) $app->updated_by);

        $p = Sql::one("SELECT * FROM pdlk_participants WHERE nim = '11324001'");
        $this->assertSame('active', $p->status);
        $this->assertSame('application', $p->source);
        $this->assertSame((int) $app->id, (int) $p->application_id);
        $this->assertSame($prodi, (int) $p->study_program_id);
        $this->assertSame(1, $this->id('SELECT COUNT(*) FROM participant_status_histories WHERE participant_id = ?', [$p->id]));

        $this->patch("/kelola/pendaftaran/{$app->id}/status", ['status' => 'failed'])->assertSessionHas('warning');
        $this->assertSame('passed', Sql::value('SELECT status FROM applications WHERE id = ?', [$app->id]));

        $csv = $this->get('/kelola/pendaftaran/ekspor')->assertOk()->streamedContent();
        $this->assertStringContainsString('11324001', $csv);
        $this->assertStringContainsString('Link GitHub', $csv);

        // Mahasiswa aktif: monitoring terbuka dan dapat memperbarui progres
        $this->cisPalsu('11324001', 'aldo@students.del.ac.id');
        $this->sebagai('Mahasiswa', [], ['email' => 'aldo2@students.del.ac.id', 'nim' => '11324001']);
        $this->get('/monitoring')->assertOk();
        $this->get('/progres')->assertOk();
        $this->put('/progres', ['stage' => 'ongoing', 'work_mode' => 'remote', 'location' => 'Rumah, Balige'])->assertRedirect('/dashboard');
        $p = Sql::one('SELECT * FROM pdlk_participants WHERE id = ?', [$p->id]);
        $this->assertSame(['ongoing', 'remote', 'Rumah, Balige'], [$p->stage, $p->work_mode, $p->location]);
    }

    public function test_nim_milik_orang_lain_ditolak(): void
    {
        [, $slug] = $this->lowongan();
        $this->cisPalsu('11324999', 'orang.lain@students.del.ac.id');
        $this->sebagai('Mahasiswa', [], ['email' => 'aldo@students.del.ac.id']);

        $this->post("/lowongan/$slug/daftar", [
            'nim' => '11324999', 'study_program_id' => $this->prodi('D3TI'),
            'semester' => 5, 'gpa' => '3.1', 'phone' => '081234567890', 'email' => 'aldo@students.del.ac.id',
            'dokumen' => UploadedFile::fake()->create('b.pdf', 100, 'application/pdf'),
            'persetujuan' => 1,
        ])->assertSessionHasErrors('nim');

        $this->assertSame(0, $this->id('SELECT COUNT(*) FROM applications'));
    }

    public function test_lowongan_ditutup_tidak_menerima_pendaftaran(): void
    {
        [, $lewat] = $this->lowongan(['registration_deadline' => now()->subDay()->toDateString()]);
        [, $manual] = $this->lowongan(['is_closed' => true], ['title' => 'Ditutup Manual']);
        $this->sebagai('Mahasiswa');

        $this->get("/lowongan/$lewat/daftar")->assertRedirect("/portal/lowongan/$lewat");
        $this->get("/lowongan/$manual/daftar")->assertRedirect("/portal/lowongan/$manual");
    }

    public function test_koordinator_hanya_melihat_dan_menginput_mahasiswa_prodinya(): void
    {
        $ti = $this->prodi('D3TI');
        $if = $this->prodi('S1IF');
        Sql::insert('pdlk_participants', ['nim' => '1', 'name' => 'Mahasiswa TI', 'study_program_id' => $ti]);
        $lain = Sql::insert('pdlk_participants', ['nim' => '2', 'name' => 'Mahasiswa IF', 'study_program_id' => $if]);

        $this->sebagai('Dosen', ['program_head' => $ti]);
        $this->get('/peserta-pdlk')->assertOk()->assertSee('Mahasiswa TI')->assertDontSee('Mahasiswa IF');
        $this->get("/peserta-pdlk/$lain")->assertNotFound();
        $this->get('/peserta-pdlk/tambah')->assertForbidden();
        $this->get('/dashboard')->assertOk()->assertSee('Koordinator Program Studi');

        $this->sebagai('Dosen', ['pdlk_coordinator' => $ti]);
        $form = [
            'nim' => '11324777', 'name' => 'Mandiri Uji', 'activity_type_id' => $this->id("SELECT id FROM activity_types WHERE slug = 'magang'"),
            'nama_mitra' => 'PT Mandiri', 'mentor_name' => 'Pak Mentor',
        ];
        $this->post('/peserta-pdlk', $form + ['study_program_id' => $if])->assertSessionHasErrors('study_program_id');
        $this->post('/peserta-pdlk', $form + ['study_program_id' => $ti])->assertRedirect();

        $p = Sql::one("SELECT * FROM pdlk_participants WHERE nim = '11324777'");
        $this->assertSame(['manual', 'active', 'Pak Mentor'], [$p->source, $p->status, $p->mentor_name]);
        $this->get("/peserta-pdlk/{$p->id}")->assertOk()->assertSee('Mandiri Uji')->assertSee('Diinput manual');
    }

    public function test_staf_upt_mengubah_status_tidak_aktif_karena_force_majeure(): void
    {
        $id = Sql::insert('pdlk_participants', ['nim' => '3', 'name' => 'Mahasiswa Sakit']);
        $this->assertSame('active', Sql::value('SELECT status FROM pdlk_participants WHERE id = ?', [$id]));

        $staf = $this->sebagai('Pegawai', ['upt_staff' => null]);

        $this->patch("/peserta-pdlk/$id/tidak-aktif", ['alasan' => ''])->assertSessionHasErrors('alasan');
        $this->patch("/peserta-pdlk/$id/tidak-aktif", ['alasan' => 'Sakit, rawat inap'])->assertSessionHas('success');

        $p = Sql::one('SELECT * FROM pdlk_participants WHERE id = ?', [$id]);
        $this->assertSame('inactive', $p->status);
        $this->assertSame('Sakit, rawat inap', $p->inactive_reason);
        $r = Sql::one('SELECT * FROM participant_status_histories WHERE participant_id = ?', [$id]);
        $this->assertSame('active', $r->old_status);
        $this->assertSame($staf, (int) $r->created_by);
    }

    public function test_dashboard_setiap_peran_dapat_dibuka(): void
    {
        $this->sebagai('Pegawai', ['upt_staff' => null]);
        $this->get('/dashboard')->assertOk()->assertSee('Kelola Pendaftar');

        $this->sebagai('Dosen');
        $this->get('/dashboard')->assertOk()->assertSee('belum memiliki peran');

        $this->cisPalsu('x', 'x');
        $this->sebagai('Mahasiswa');
        $this->get('/dashboard')->assertOk()->assertSee('Belum mendaftar');
    }

    public function test_mahasiswa_input_manual_tidak_tampil_belum_mendaftar(): void
    {
        Sql::insert('pdlk_participants', ['nim' => '42324032', 'name' => 'Aldo Steven Dion Sitorus', 'source' => 'manual']);
        $this->cisPalsu('42324032', 'x@x');
        $this->sebagai('Mahasiswa', [], ['nim' => '42324032', 'username' => 'if324032']);

        $this->get('/dashboard')->assertOk()
            ->assertSee('Tanpa pendaftaran')
            ->assertDontSee('Belum mendaftar')
            ->assertSee('Aldo Steven Dion Sitorus');
    }

    public function test_prodi_kosong_dilengkapi_dari_cis(): void
    {
        $ti = $this->prodi('D3TI');
        Sql::insert('pdlk_participants', ['nim' => '42324032', 'name' => 'Aldo Lama']);
        $this->cisPalsu('42324032', 'a@b', 'Aldo Lama', 'DIII Teknologi Informasi');

        $this->artisan('pdlk:lengkapi-prodi')->assertSuccessful();
        $this->assertSame($ti, $this->id('SELECT study_program_id FROM pdlk_participants'));

        $this->sebagai('Dosen', ['program_head' => $ti]);
        $this->get('/peserta-pdlk')->assertSee('Aldo Lama');
    }

    public function test_satu_akun_dua_peran_dan_ganti_peran(): void
    {
        $this->sebagai('Mahasiswa', ['upt_staff' => null]);
        $this->assertSame('upt_staff', session('peran_aktif'));

        $this->post('/peran/student')->assertRedirect('/dashboard');
        $this->assertSame('student', session('peran_aktif'));

        $this->post('/peran/program_head')->assertForbidden();
    }

    public function test_perintah_beri_peran(): void
    {
        $this->artisan('pdlk:beri-peran', ['username' => 'monalisa', 'peran' => 'upt_staff'])->assertSuccessful();
        $this->artisan('pdlk:beri-peran', ['username' => 'monalisa', 'peran' => 'upt_staff'])->assertSuccessful();
        $this->artisan('pdlk:beri-peran', ['username' => 'kaprodi', 'peran' => 'program_head'])->assertFailed();
        $this->artisan('pdlk:beri-peran', ['username' => 'kaprodi', 'peran' => 'program_head', '--prodi' => 'd3ti'])->assertSuccessful();

        $this->assertSame(2, $this->id('SELECT COUNT(*) FROM user_roles'), 'Peran Staf UPT tidak boleh tersimpan dobel (NULLS NOT DISTINCT).');
    }

    public function test_alur_monev_kunci_dashboard_sampai_selesai(): void
    {
        $ti = $this->prodi('D3TI');
        $periode = Sql::insert('periods', ['name' => 'Genap 2026', 'academic_year' => '2025/2026', 'semester' => 'genap', 'start_date' => '2026-02-01', 'end_date' => '2026-06-30']);

        // Staf UPT menyiapkan kuesioner, pertanyaan, dan jadwal
        $this->sebagai('Pegawai', ['upt_staff' => null]);
        $this->post('/kelola/kuesioner', ['type' => 'mid', 'title' => 'Monev Tengah Uji'])->assertRedirect();
        $mid = $this->id("SELECT id FROM monev_questionnaires WHERE type = 'mid'");
        $this->post("/kelola/kuesioner/$mid/pertanyaan", ['section_title' => 'Umum', 'question_text' => 'Kepuasan terhadap mitra', 'question_type' => 'scale', 'skala_min' => 1, 'skala_max' => 5, 'is_required' => 1])->assertRedirect();
        $this->post("/kelola/kuesioner/$mid/pertanyaan", ['question_text' => 'Kendala yang dihadapi', 'question_type' => 'textarea', 'is_required' => 1]);
        $this->post("/kelola/kuesioner/$mid/pertanyaan", ['question_text' => 'Bukti meeting', 'question_type' => 'file', 'is_required' => 1]);
        $this->post("/kelola/kuesioner/$mid/pertanyaan", ['question_text' => 'Mode', 'question_type' => 'choice', 'is_required' => 0])->assertSessionHasErrors('pilihan');
        $this->post('/kelola/kuesioner', ['type' => 'final', 'title' => 'Monev Akhir Uji']);
        $final = $this->id("SELECT id FROM monev_questionnaires WHERE type = 'final'");
        $this->post("/kelola/kuesioner/$final/pertanyaan", ['question_text' => 'Kesan', 'question_type' => 'text', 'is_required' => 1]);

        $buka = now()->subDay()->format('Y-m-d\\TH:i');
        $this->post('/kelola/jadwal-monev', ['questionnaire_id' => $mid, 'period_id' => $periode, 'open_at' => $buka, 'deadline_at' => now()->addWeek()->format('Y-m-d\\TH:i')])->assertRedirect('/kelola/jadwal-monev');
        $this->post('/kelola/jadwal-monev', ['questionnaire_id' => $mid, 'period_id' => $periode, 'open_at' => $buka, 'deadline_at' => now()->addWeek()->format('Y-m-d\\TH:i')])->assertSessionHasErrors('questionnaire_id');
        $this->post('/kelola/jadwal-monev', ['questionnaire_id' => $final, 'period_id' => $periode, 'open_at' => now()->addMonth()->format('Y-m-d\\TH:i'), 'deadline_at' => now()->addMonths(2)->format('Y-m-d\\TH:i')]);
        $jMid = $this->id('SELECT id FROM monev_schedules WHERE questionnaire_id = ?', [$mid]);
        $jFinal = $this->id('SELECT id FROM monev_schedules WHERE questionnaire_id = ?', [$final]);

        $pid = Sql::insert('pdlk_participants', ['nim' => '11324001', 'name' => 'Aldo Monev', 'study_program_id' => $ti, 'period_id' => $periode, 'source' => 'manual']);
        Sql::insert('pdlk_participants', ['nim' => '11324002', 'name' => 'Belum Isi', 'study_program_id' => $ti, 'period_id' => $periode, 'source' => 'manual']);

        // Mahasiswa: dashboard terkunci sampai monev tengah dikirim
        $this->cisPalsu('11324001', 'aldo@x');
        $this->sebagai('Mahasiswa', [], ['nim' => '11324001']);
        $this->get('/dashboard')->assertRedirect("/monitoring/$jMid");
        $this->get('/pendaftaran-saya')->assertRedirect("/monitoring/$jMid");
        $this->get("/monitoring/$jMid")->assertOk()->assertSee('Kepuasan terhadap mitra')->assertSee('Umum');

        $q = Sql::all('SELECT id, question_type FROM monev_questions WHERE questionnaire_id = ? ORDER BY display_order', [$mid])->pluck('id', 'question_type');
        $this->post("/monitoring/$jMid", ['aksi' => 'draf', 'jawaban' => [$q['scale'] => 4]])->assertRedirect("/monitoring/$jMid");
        $this->assertSame('draft', Sql::value('SELECT status FROM monev_responses WHERE participant_id = ?', [$pid]));
        $this->get('/dashboard')->assertRedirect("/monitoring/$jMid");

        $this->post("/monitoring/$jMid", ['aksi' => 'kirim', 'jawaban' => [$q['scale'] => 4]])->assertSessionHasErrors(["jawaban.{$q['textarea']}", "jawaban.{$q['file']}"]);
        $this->post("/monitoring/$jMid", ['aksi' => 'kirim', 'jawaban' => [
            $q['scale'] => 4, $q['textarea'] => 'Tidak ada kendala', $q['file'] => UploadedFile::fake()->image('meeting.jpg'),
        ]])->assertRedirect('/monitoring');

        $r = Sql::one('SELECT * FROM monev_responses WHERE participant_id = ?', [$pid]);
        $this->assertSame('submitted', $r->status);
        $this->assertNotNull($r->submitted_at);
        $this->assertSame(4, $this->id('SELECT answer_number FROM monev_answers WHERE response_id = ? AND question_id = ?', [$r->id, $q['scale']]));
        $this->post("/monitoring/$jMid", ['aksi' => 'kirim'])->assertForbidden();
        $this->get('/dashboard')->assertOk()->assertSee('Terkirim');
        $this->assertSame('active', Sql::value('SELECT status FROM pdlk_participants WHERE id = ?', [$pid]));

        // Rekap: koordinator melihat siapa yang belum mengirim
        $this->sebagai('Dosen', ['program_head' => $ti]);
        $this->get("/rekap-monev?jadwal=$jMid")->assertOk()->assertSee('Belum Isi')->assertSee('50%');
        $this->get("/rekap-monev?jadwal=$jMid&isi=belum")->assertOk()->assertDontSee('Aldo Monev');
        $this->get("/rekap-monev/jawaban/{$r->id}")->assertOk()->assertSee('Tidak ada kendala');
        $this->assertStringContainsString('Tidak ada kendala', $this->get("/rekap-monev/$jMid/ekspor")->streamedContent());

        // Monev akhir dibuka, lalu dikirim -> status Selesai otomatis
        \DB::update('UPDATE monev_schedules SET open_at = ? WHERE id = ?', [now()->subHour(), $jFinal]);
        $this->cisPalsu('11324001', 'aldo@x');
        $this->sebagai('Mahasiswa', [], ['nim' => '11324001']);
        $this->get('/dashboard')->assertRedirect("/monitoring/$jFinal");
        $qf = $this->id('SELECT id FROM monev_questions WHERE questionnaire_id = ?', [$final]);
        $this->post("/monitoring/$jFinal", ['aksi' => 'kirim', 'jawaban' => [$qf => 'Sangat bermanfaat']])->assertRedirect('/monitoring');

        $this->assertSame('completed', Sql::value('SELECT status FROM pdlk_participants WHERE id = ?', [$pid]));
        $this->assertSame('completed', Sql::value('SELECT new_status FROM participant_status_histories WHERE participant_id = ? ORDER BY id DESC LIMIT 1', [$pid]));
        $this->get('/dashboard')->assertOk()->assertSee('Selesai');
        $this->get('/monitoring')->assertOk();
    }

    public function test_pengumuman_hasil_dari_pendaftar_lulus(): void
    {
        [$vId, $slug] = $this->lowongan();
        $aId = $this->id('SELECT announcement_id FROM vacancy_details WHERE id = ?', [$vId]);
        Sql::insert('applications', [
            'vacancy_id' => $vId, 'nim' => '11324555', 'name' => 'Lulus Uji', 'study_program_id' => $this->prodi('D3TI'),
            'semester' => 5, 'gpa' => 3.5, 'phone' => '0812', 'email' => 'a@b', 'document_path' => 'x.pdf',
            'document_original_name' => 'x.pdf', 'status' => 'passed',
        ]);

        $this->sebagai('Pegawai', ['upt_staff' => null]);
        $this->get("/kelola/hasil/create?lowongan=$aId")->assertOk()->assertSee('Lulus Uji');
        $this->post('/kelola/hasil', [
            'title' => 'Hasil Seleksi Magang Backend', 'source_announcement_id' => $aId, 'terbit' => 1,
            'items' => [['nim' => '11324555', 'student_name' => 'Lulus Uji', 'study_program_id' => $this->prodi('D3TI')], ['nim' => '', 'student_name' => '']],
        ])->assertRedirect();

        $h = Sql::one("SELECT * FROM announcements WHERE type = 'result'");
        $this->assertSame(1, $this->id('SELECT COUNT(*) FROM announcement_result_items WHERE announcement_id = ?', [$h->id]));

        $this->get('/pengumuman-hasil')->assertOk()->assertSee('Hasil Seleksi Magang Backend');
        $this->get("/pengumuman-hasil/{$h->slug}")->assertOk()->assertSee('Lulus Uji')->assertSee('11324555');
        $this->get("/lowongan/$slug")->assertOk()->assertSee('Hasil seleksi sudah diumumkan');
    }

    public function test_mode_sprint_1_hanya_menampilkan_pb01_sampai_pb04(): void
    {
        config(['pdlk.sprint' => 1]);
        [$vId, $slug] = $this->lowongan();
        $prodi = $this->prodi('D3TI');

        // Guest: halaman Sprint 1 terbuka, hasil seleksi (Sprint 2) tidak ada.
        $this->get('/')->assertOk()->assertDontSee('Hasil Seleksi');
        $this->get('/tentang-pdlk')->assertOk();
        $this->get('/galeri')->assertOk();
        $this->get('/pengumuman-hasil')->assertNotFound();

        // Staf UPT: menu Sprint 1 ada, menu Sprint 2–3 tersembunyi dan halamannya 404.
        $this->sebagai('Staf', ['upt_staff' => null]);
        $this->get('/dashboard')->assertOk()->assertSee('Lowongan')->assertSee('Data Master')->assertSee('Pengguna &amp; Peran', false)
            ->assertDontSee('Pendaftar Lowongan')->assertDontSee('Kuesioner')->assertDontSee('Rekap Monev')->assertDontSee('Mahasiswa Aktif');
        $this->get('/kelola/lowongan')->assertOk();
        $this->get('/kelola/master/mitra')->assertOk();
        $this->get('/kelola/pengguna')->assertOk();
        foreach (['/kelola/pendaftaran', '/kelola/hasil', '/kelola/kuesioner', '/kelola/jadwal-monev', '/peserta-pdlk', '/peserta-pdlk/tambah', '/rekap-monev'] as $url) {
            $this->get($url)->assertNotFound();
        }

        // Koordinator prodi: belum ada menu pemantauan.
        $this->sebagai('Dosen', ['pdlk_coordinator' => $prodi]);
        $this->get('/dashboard')->assertOk()->assertDontSee('Mahasiswa PDLK Prodi');

        // Mahasiswa: mendaftar lalu kembali ke dashboard dengan pesan berhasil.
        $this->cisPalsu('11324009', 'mhs9@students.del.ac.id', 'Mahasiswa Sprint');
        $this->sebagai('Mahasiswa', [], ['email' => 'mhs9@students.del.ac.id']);
        $this->get('/dashboard')->assertOk()->assertDontSee('Progres Pelaksanaan')->assertDontSee('Monev Tengah');
        $this->get('/pendaftaran-saya')->assertNotFound();
        $this->get('/progres')->assertNotFound();
        $this->get('/monitoring')->assertNotFound();

        $this->post("/lowongan/$slug/daftar", [
            'nim' => '11324009', 'study_program_id' => $prodi, 'semester' => 5, 'gpa' => '3.50',
            'phone' => '081234567899', 'email' => 'mhs9@students.del.ac.id',
            'dokumen' => UploadedFile::fake()->create('berkas.pdf', 300, 'application/pdf'),
            'persetujuan' => 1,
        ])->assertRedirect('/dashboard');

        $this->assertSame('registered', Sql::value('SELECT status FROM applications WHERE nim = ?', ['11324009']));
        $this->get('/dashboard')->assertOk()->assertSee('Terkirim')->assertSee('Magang Backend');
    }

    public function test_halaman_pengguna_dengan_jabatan_cis_bertingkat(): void
    {
        $target = Sql::insert('users', [
            'username' => 'dosenjabatan', 'name' => 'Dosen Jabatan', 'cis_role' => 'Dosen',
            'cis_position' => [['struktur_jabatan_id' => 7, 'jabatan' => 'Koordinator PDLK D3TI'], ['jabatan' => 'Dosen']],
        ]);

        $this->sebagai('Staf', ['upt_staff' => null]);
        $this->get("/kelola/pengguna/$target")->assertOk()->assertSee('Koordinator PDLK D3TI, Dosen');
    }

    public function test_mahasiswa_melihat_lowongan_tetap_di_tampilan_dashboard(): void
    {
        config(['pdlk.sprint' => 1]);
        [, $slug] = $this->lowongan();
        $this->sebagai('Mahasiswa', [], ['nim' => '11324010']);

        // Menu "Lowongan PDLK" mengarah ke halaman portal yang memakai layout dashboard.
        $this->get('/dashboard')->assertOk()->assertSee(route('portal.lowongan.index'), false);
        $this->get('/portal/lowongan')->assertOk()->assertSee('MONITORING PDLK')->assertSee('Magang Backend')
            ->assertSee(route('portal.lowongan.show', $slug), false)->assertDontSee('pub-header');
        $this->get("/portal/lowongan/$slug")->assertOk()->assertSee('MONITORING PDLK')->assertSee('IPK minimal 3.00')
            ->assertSee('Daftar Sekarang')->assertSee('Semua lowongan');

        // Halaman publik untuk guest tetap memakai tampilan publik.
        session()->flush();
        $this->flushSession();
        $this->get('/lowongan')->assertOk()->assertSee('pub-header');
        $this->get('/portal/lowongan')->assertRedirect('/login');
    }

    public function test_validasi_formulir_pendaftaran_pb04(): void
    {
        config(['pdlk.sprint' => 1]);
        [, $slug] = $this->lowongan();
        $prodi = $this->prodi('D3TI');
        $this->cisPalsu('11324011', 'mhs11@students.del.ac.id');
        $this->sebagai('Mahasiswa', [], ['email' => 'mhs11@students.del.ac.id']);

        $this->get("/lowongan/$slug/daftar")->assertOk()
            ->assertSee('KTM, KTP, CV, portofolio, transkrip nilai,', false)->assertSee('surat pengantar');

        $sah = [
            'nim' => '11324011', 'study_program_id' => $prodi, 'semester' => 5, 'gpa' => '3.50',
            'phone' => '081234567811', 'email' => 'mhs11@students.del.ac.id', 'persetujuan' => 1,
        ];
        $pdf = fn () => UploadedFile::fake()->create('berkas.pdf', 300, 'application/pdf');

        // Berkas wajib, harus PDF, maksimal 10 MB
        $this->post("/lowongan/$slug/daftar", $sah)->assertSessionHasErrors('dokumen');
        $this->post("/lowongan/$slug/daftar", $sah + ['dokumen' => UploadedFile::fake()->create('berkas.docx', 100)])->assertSessionHasErrors('dokumen');
        $this->post("/lowongan/$slug/daftar", $sah + ['dokumen' => UploadedFile::fake()->create('besar.pdf', 10241, 'application/pdf')])->assertSessionHasErrors('dokumen');
        // Batas nilai IPK dan semester (Boundary Value Analysis)
        $this->post("/lowongan/$slug/daftar", ['gpa' => '4.01', 'dokumen' => $pdf()] + $sah)->assertSessionHasErrors('gpa');
        $this->post("/lowongan/$slug/daftar", ['semester' => 0, 'dokumen' => $pdf()] + $sah)->assertSessionHasErrors('semester');
        // Pernyataan wajib dicentang
        $this->post("/lowongan/$slug/daftar", array_diff_key($sah, ['persetujuan' => 1]) + ['dokumen' => $pdf()])->assertSessionHasErrors('persetujuan');
        $this->assertSame(0, $this->id('SELECT COUNT(*) FROM applications'));

        // Nilai batas yang sah diterima
        $this->post("/lowongan/$slug/daftar", ['gpa' => '4.00', 'semester' => 14, 'dokumen' => $pdf()] + $sah)->assertRedirect('/dashboard');
        $this->assertSame(1, $this->id('SELECT COUNT(*) FROM applications'));
    }
}
