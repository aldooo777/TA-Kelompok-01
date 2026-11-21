<?php

namespace App\Http\Controllers;

use App\Support\NimMahasiswa;
use App\Support\Peran;
use App\Support\Query;
use App\Support\Sql;

class DashboardController extends Controller
{
    public function __invoke(NimMahasiswa $nimMahasiswa)
    {
        return match (Peran::aktif()) {
            'student' => $this->mahasiswa($nimMahasiswa),
            'upt_staff', 'pdlk_coordinator', 'program_head' => $this->pengelola(),
            default => view('dashboard.tanpa-peran'),
        };
    }

    private function mahasiswa(NimMahasiswa $nimMahasiswa)
    {
        $user = Peran::user();
        $nim = $nimMahasiswa->sesiIni();

        $pendaftaranList = Sql::all(
            Query::PENDAFTARAN . ' WHERE (ap.user_id = ? OR ap.nim = ?) AND ap.is_active = true ORDER BY ap.created_at DESC LIMIT 3',
            [$user?->id ?? 0, $nim ?? '']
        );
        $pendaftaranTerbaru = $pendaftaranList->first();

        $statPendaftaran = Sql::one(
            "SELECT COUNT(*) AS total,
                    COUNT(*) FILTER (WHERE status IN ('registered', 'processing')) AS diproses,
                    COUNT(*) FILTER (WHERE status = 'passed') AS lulus
             FROM applications
             WHERE (user_id = ? OR nim = ?) AND is_active = true",
            [$user?->id ?? 0, $nim ?? '']
        );

        $pesertaAktif = $nim ? Sql::one(
            Query::PESERTA . " WHERE pp.nim = ? AND pp.is_active
             ORDER BY CASE pp.status WHEN 'active' THEN 0 WHEN 'completed' THEN 1 ELSE 2 END, pp.created_at DESC LIMIT 1",
            [$nim]
        ) : null;

        // Login lama tidak mengirim nama; gunakan nama dari data peserta.
        $cis = session('cis_user', []);
        $nama = $pesertaAktif?->name ?? $pendaftaranTerbaru?->name;

        if (empty($cis['nama']) && $nama) {
            $cis['nama'] = $nama;
            session(['cis_user' => $cis]);
            if ($user && $user->name !== $nama) {
                Sql::update('users', $user->id, ['name' => $nama]);
            }
        }

        $monev = $pesertaAktif && $pesertaAktif->status !== 'inactive'
            ? \App\Support\PesertaSaya::jadwalMonev($pesertaAktif)
            : collect();

        $periodeAktif = Sql::one('SELECT * FROM periods WHERE is_active = true ORDER BY start_date DESC NULLS LAST, id DESC LIMIT 1');

        $prodiMahasiswa = $user?->study_program_id
            ? Sql::value("SELECT level || ' ' || name FROM study_programs WHERE id = ?", [$user->study_program_id])
            : ($pesertaAktif ? trim(($pesertaAktif->prodi_level ?? '') . ' ' . ($pesertaAktif->prodi_name ?? '')) : null);

        $lowonganTerbaru = Sql::all(
            Query::LOWONGAN . ' WHERE ' . Query::LOWONGAN_TERBIT . '
             AND v.is_closed = false
             AND (v.registration_start_date IS NULL OR v.registration_start_date <= CURRENT_DATE)
             AND (v.registration_deadline IS NULL OR v.registration_deadline >= CURRENT_DATE)
             ORDER BY v.registration_deadline ASC NULLS LAST, a.published_at DESC NULLS LAST
             LIMIT 3'
        );

        return view('dashboard.mahasiswa', compact(
            'user',
            'nim',
            'prodiMahasiswa',
            'periodeAktif',
            'pendaftaranList',
            'pendaftaranTerbaru',
            'statPendaftaran',
            'pesertaAktif',
            'monev',
            'lowonganTerbaru'
        ));
    }

    private function pengelola()
    {
        $peran = Peran::aktif();
        $staf = $peran === 'upt_staff';
        $prodi = Peran::cakupanProdi();
        $batas = $prodi !== null ? ' AND study_program_id IN (' . (implode(',', array_map('intval', $prodi)) ?: '0') . ')' : '';
        $batasAlias = $prodi !== null ? ' AND pp.study_program_id IN (' . (implode(',', array_map('intval', $prodi)) ?: '0') . ')' : '';

        $namaProdiCakupan = $prodi !== null && $prodi !== []
            ? Sql::all("SELECT level || ' ' || name AS label FROM study_programs WHERE id IN (" . implode(',', array_map('intval', $prodi)) . ') ORDER BY level, name')
                ->pluck('label')
                ->implode(', ')
            : 'Semua Program Studi';

        $periodeAktif = Sql::one('SELECT * FROM periods WHERE is_active = true ORDER BY start_date DESC NULLS LAST, id DESC LIMIT 1');

        $statistikPeserta = Sql::all("SELECT status, COUNT(*) AS jumlah FROM pdlk_participants WHERE is_active = true$batas GROUP BY status")
            ->pluck('jumlah', 'status');

        $skemaPeserta = Sql::one(
            "SELECT COUNT(*) AS total,
                    COUNT(*) FILTER (WHERE program_category = 'flagship') AS flagship,
                    COUNT(*) FILTER (WHERE program_category = 'independent') AS mandiri
             FROM pdlk_participants
             WHERE is_active = true AND status = 'active'$batas"
        );

        $pesertaPerJenis = Sql::all(
            "SELECT t.name, COALESCE(COUNT(pp.id), 0) AS total
             FROM activity_types t
             LEFT JOIN pdlk_participants pp
                    ON pp.activity_type_id = t.id AND pp.is_active = true AND pp.status = 'active'$batasAlias
             WHERE t.is_active = true
             GROUP BY t.id, t.name, t.display_order
             ORDER BY total DESC, t.display_order ASC"
        );

        $statistikPendaftaran = $staf
            ? Sql::all('SELECT status, COUNT(*) AS jumlah FROM applications WHERE is_active = true GROUP BY status')->pluck('jumlah', 'status')
            : null;

        $jumlahLowonganDibuka = (int) Sql::value(
            'SELECT COUNT(*) FROM vacancy_details v JOIN announcements a ON a.id = v.announcement_id WHERE ' . Query::LOWONGAN_TERBIT . '
             AND v.is_closed = false
             AND (v.registration_start_date IS NULL OR v.registration_start_date <= CURRENT_DATE)
             AND (v.registration_deadline IS NULL OR v.registration_deadline >= CURRENT_DATE)'
        );

        $jumlahLowonganMendesak = (int) Sql::value(
            'SELECT COUNT(*) FROM vacancy_details v JOIN announcements a ON a.id = v.announcement_id WHERE ' . Query::LOWONGAN_TERBIT . "
             AND v.is_closed = false
             AND v.registration_deadline BETWEEN CURRENT_DATE AND CURRENT_DATE + INTERVAL '7 days'"
        );

        $jumlahBerita = (int) Sql::value('SELECT COUNT(*) FROM announcements a WHERE ' . Query::BERITA_TERBIT);

        $pelamarTerbaru = $staf
            ? Sql::all(
                "SELECT ap.id, ap.name, ap.nim, ap.status, ap.created_at,
                        a.title AS vacancy_title, p.name AS partner_name,
                        COALESCE(sp.level || ' ' || sp.name, '-') AS prodi
                 FROM applications ap
                 JOIN vacancy_details v ON v.id = ap.vacancy_id
                 JOIN announcements a ON a.id = v.announcement_id
                 JOIN partners p ON p.id = v.partner_id
                 LEFT JOIN study_programs sp ON sp.id = ap.study_program_id
                 WHERE ap.is_active = true AND ap.status IN ('registered', 'processing')
                 ORDER BY ap.created_at DESC
                 LIMIT 5"
            )
            : collect();

        $riwayatStatus = Sql::all(
            "SELECT h.*, pp.name AS participant_name, pp.nim,
                    COALESCE(sp.level || ' ' || sp.name, '') AS prodi,
                    u.name AS actor_name
             FROM participant_status_histories h
             JOIN pdlk_participants pp ON pp.id = h.participant_id
             LEFT JOIN study_programs sp ON sp.id = pp.study_program_id
             LEFT JOIN users u ON u.id = h.created_by
             WHERE h.is_active = true AND pp.is_active = true$batasAlias
             ORDER BY h.created_at DESC
             LIMIT 4"
        );

        $jadwalMonev = Sql::all(
            "SELECT ms.*, mq.type, mq.title,
                    (SELECT COUNT(*) FROM monev_responses mr WHERE mr.schedule_id = ms.id AND mr.status = 'submitted' AND mr.is_active = true) AS jumlah_kirim
             FROM monev_schedules ms
             JOIN monev_questionnaires mq ON mq.id = ms.questionnaire_id
             WHERE ms.is_active = true AND mq.is_active = true
             ORDER BY ms.open_at DESC NULLS LAST, ms.id DESC
             LIMIT 2"
        );

        $lowonganSegeraTutup = $staf
            ? Sql::all(
                'SELECT v.id, a.title, a.slug, p.name AS partner_name, v.registration_deadline,
                        (SELECT COUNT(*) FROM applications ap WHERE ap.vacancy_id = v.id AND ap.is_active = true) AS jumlah_pelamar
                 FROM vacancy_details v
                 JOIN announcements a ON a.id = v.announcement_id
                 JOIN partners p ON p.id = v.partner_id
                 WHERE ' . Query::LOWONGAN_TERBIT . '
                   AND v.is_closed = false
                   AND v.registration_deadline IS NOT NULL
                   AND v.registration_deadline >= CURRENT_DATE
                 ORDER BY v.registration_deadline ASC
                 LIMIT 4'
            )
            : collect();

        $mitraStat = Sql::one(
            "SELECT COUNT(*) AS total,
                    COUNT(*) FILTER (WHERE cooperation_status = 'cooperating') AS cooperating,
                    COUNT(*) FILTER (WHERE cooperation_status = 'in_process') AS in_process
             FROM partners WHERE is_active = true"
        );

        // Data khusus Koordinator PDLK Prodi & Kaprodi
        $daftarPesertaProdi = ! $staf
            ? Sql::all(
                Query::PESERTA . " WHERE pp.is_active = true$batasAlias
                 ORDER BY CASE pp.status WHEN 'active' THEN 0 WHEN 'completed' THEN 1 ELSE 2 END, pp.created_at DESC
                 LIMIT 12"
            )
            : collect();

        $sebaranMitraProdi = ! $staf
            ? Sql::all(
                "SELECT p.name, COUNT(pp.id) AS total
                 FROM pdlk_participants pp
                 JOIN partners p ON p.id = pp.partner_id
                 WHERE pp.is_active = true AND pp.status IN ('active', 'completed')$batasAlias
                 GROUP BY p.id, p.name
                 ORDER BY total DESC, p.name ASC
                 LIMIT 5"
            )
            : collect();

        $skorMonevBagian = ! $staf
            ? Sql::all(
                "SELECT COALESCE(NULLIF(TRIM(mq.section_title), ''), 'Evaluasi Umum') AS bagian,
                        ROUND(AVG(ma.answer_number)::numeric, 2) AS rata_rata
                 FROM monev_answers ma
                 JOIN monev_questions mq ON mq.id = ma.question_id
                 JOIN monev_responses mr ON mr.id = ma.response_id
                 JOIN pdlk_participants pp ON pp.id = mr.participant_id
                 WHERE ma.is_active = true AND mr.is_active = true AND mr.status = 'submitted'
                   AND ma.answer_number IS NOT NULL AND pp.is_active = true$batasAlias
                 GROUP BY COALESCE(NULLIF(TRIM(mq.section_title), ''), 'Evaluasi Umum')
                 ORDER BY rata_rata DESC
                 LIMIT 6"
            )
            : collect();

        $belumMonevProdi = (! $staf && sprint(3))
            ? Sql::all(
                "SELECT pp.id, pp.nim, pp.name, p.name AS partner_name, ms.deadline_at, mq.type
                 FROM pdlk_participants pp
                 JOIN monev_schedules ms ON ms.is_active = true AND ms.is_closed = false
                      AND ms.open_at <= clock_timestamp()::timestamp
                      AND (ms.deadline_at IS NULL OR ms.deadline_at >= clock_timestamp()::timestamp)
                 JOIN monev_questionnaires mq ON mq.id = ms.questionnaire_id AND mq.is_active = true
                 LEFT JOIN partners p ON p.id = pp.partner_id
                 LEFT JOIN monev_responses mr ON mr.participant_id = pp.id AND mr.schedule_id = ms.id AND mr.is_active = true
                 WHERE pp.is_active = true AND pp.status = 'active'$batasAlias
                   AND (mr.id IS NULL OR mr.status <> 'submitted')
                 ORDER BY ms.deadline_at ASC NULLS LAST, pp.name ASC
                 LIMIT 5"
            )
            : collect();

        $jenisKegiatanList = ! $staf
            ? Sql::all('SELECT name FROM activity_types WHERE is_active = true ORDER BY display_order, name')
            : collect();

        return view('dashboard.pengelola', [
            'user' => Peran::user(),
            'namaProdiCakupan' => $namaProdiCakupan,
            'periodeAktif' => $periodeAktif,
            'statistikPeserta' => $statistikPeserta,
            'skemaPeserta' => $skemaPeserta,
            'pesertaPerJenis' => $pesertaPerJenis,
            'statistikPendaftaran' => $statistikPendaftaran,
            'jumlahLowonganDibuka' => $jumlahLowonganDibuka,
            'jumlahLowonganMendesak' => $jumlahLowonganMendesak,
            'jumlahBerita' => $jumlahBerita,
            'pelamarTerbaru' => $pelamarTerbaru,
            'riwayatStatus' => $riwayatStatus,
            'jadwalMonev' => $jadwalMonev,
            'lowonganSegeraTutup' => $lowonganSegeraTutup,
            'mitraStat' => $mitraStat,
            'daftarPesertaProdi' => $daftarPesertaProdi,
            'sebaranMitraProdi' => $sebaranMitraProdi,
            'skorMonevBagian' => $skorMonevBagian,
            'belumMonevProdi' => $belumMonevProdi,
            'jenisKegiatanList' => $jenisKegiatanList,
        ]);
    }
}
