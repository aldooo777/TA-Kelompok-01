-- =====================================================================
-- CEK DATA SISTEM INFORMASI PDLK  (database: monitoring_pdlk)
-- Cara pakai di pgAdmin 4:
--   klik kanan monitoring_pdlk > Query Tool > Open File > pilih file ini
--   > blok satu query > tekan F5.
-- Semua query hanya MEMBACA data (SELECT), aman dijalankan.
-- =====================================================================


-- 0. Jumlah baris setiap tabel
SELECT 'roles' AS tabel, COUNT(*) AS jumlah FROM roles
UNION ALL SELECT 'study_programs', COUNT(*) FROM study_programs
UNION ALL SELECT 'users', COUNT(*) FROM users
UNION ALL SELECT 'user_roles', COUNT(*) FROM user_roles
UNION ALL SELECT 'periods', COUNT(*) FROM periods
UNION ALL SELECT 'activity_types', COUNT(*) FROM activity_types
UNION ALL SELECT 'partners', COUNT(*) FROM partners
UNION ALL SELECT 'announcements', COUNT(*) FROM announcements
UNION ALL SELECT 'vacancy_details', COUNT(*) FROM vacancy_details
UNION ALL SELECT 'vacancy_additional_fields', COUNT(*) FROM vacancy_additional_fields
UNION ALL SELECT 'applications', COUNT(*) FROM applications
UNION ALL SELECT 'pdlk_participants', COUNT(*) FROM pdlk_participants
UNION ALL SELECT 'participant_status_histories', COUNT(*) FROM participant_status_histories
UNION ALL SELECT 'monev_questionnaires', COUNT(*) FROM monev_questionnaires
UNION ALL SELECT 'monev_schedules', COUNT(*) FROM monev_schedules
UNION ALL SELECT 'monev_responses', COUNT(*) FROM monev_responses;


-- 1a. Akun yang pernah login lewat CIS
SELECT id, username, name, nim, email, cis_role, last_login_at, is_active
FROM users
ORDER BY last_login_at DESC NULLS LAST;

-- 1b. Peran setiap akun (Mahasiswa dari CIS tidak tercatat di sini)
SELECT u.username, u.name, r.name AS peran,
       COALESCE(sp.code, 'Semua prodi') AS cakupan_prodi, ur.is_active
FROM user_roles ur
JOIN users u ON u.id = ur.user_id
JOIN roles r ON r.id = ur.role_id
LEFT JOIN study_programs sp ON sp.id = ur.study_program_id
ORDER BY u.username;


-- 2. Data master
SELECT id, code, level, name, is_active FROM study_programs ORDER BY level, name;
SELECT id, slug, name, display_order FROM activity_types ORDER BY display_order;
SELECT id, name, field, cooperation_status, is_active FROM partners ORDER BY name;
SELECT id, name, academic_year, semester, start_date, end_date FROM periods ORDER BY start_date DESC;


-- 3a. Berita (announcements type = news)
SELECT id, title, status, cover_image, published_at, created_by
FROM announcements
WHERE type = 'news'
ORDER BY published_at DESC NULLS LAST;

-- 3b. Lowongan dan status buka/tutup
SELECT a.id AS announcement_id, v.id AS vacancy_id, a.title, p.name AS mitra,
       t.name AS bentuk_kegiatan, v.registration_type, v.external_url,
       v.registration_start_date, v.registration_deadline, v.quota,
       CASE
           WHEN a.status <> 'published' OR NOT a.is_active THEN 'Tidak tampil'
           WHEN v.is_closed THEN 'Ditutup manual'
           WHEN v.registration_deadline < CURRENT_DATE THEN 'Lewat batas'
           WHEN v.registration_start_date > CURRENT_DATE THEN 'Belum dibuka'
           ELSE 'Dibuka'
       END AS status_lowongan
FROM vacancy_details v
JOIN announcements a ON a.id = v.announcement_id
JOIN partners p ON p.id = v.partner_id
JOIN activity_types t ON t.id = v.activity_type_id
ORDER BY a.created_at DESC;

-- 3c. Isian tambahan per lowongan
SELECT a.title AS lowongan, f.display_order, f.label, f.field_type, f.is_required
FROM vacancy_additional_fields f
JOIN vacancy_details v ON v.id = f.vacancy_id
JOIN announcements a ON a.id = v.announcement_id
ORDER BY a.title, f.display_order;


-- 4a. Pendaftar (status: registered -> processing -> passed / failed)
SELECT ap.id, ap.nim, ap.name, sp.code AS prodi, ap.semester, ap.gpa,
       ap.phone, ap.email, a.title AS lowongan, ap.status,
       ap.additional_answers, ap.created_at AS waktu_daftar, ap.processed_at
FROM applications ap
JOIN vacancy_details v ON v.id = ap.vacancy_id
JOIN announcements a ON a.id = v.announcement_id
LEFT JOIN study_programs sp ON sp.id = ap.study_program_id
ORDER BY ap.created_at DESC;

-- 4b. Jumlah pendaftar per lowongan dan status
SELECT a.title AS lowongan,
       COUNT(ap.id) AS total,
       COUNT(*) FILTER (WHERE ap.status = 'registered') AS terdaftar,
       COUNT(*) FILTER (WHERE ap.status = 'processing') AS proses,
       COUNT(*) FILTER (WHERE ap.status = 'passed')     AS lulus,
       COUNT(*) FILTER (WHERE ap.status = 'failed')     AS tidak_lulus
FROM vacancy_details v
JOIN announcements a ON a.id = v.announcement_id
LEFT JOIN applications ap ON ap.vacancy_id = v.id
WHERE v.registration_type = 'internal'
GROUP BY a.id, a.title
ORDER BY total DESC;


-- 5a. Mahasiswa aktif PDLK (status: active / inactive / completed)
SELECT pp.id, pp.nim, pp.name, COALESCE(sp.code, '(prodi kosong)') AS prodi,
       t.name AS kegiatan, p.name AS mitra, pp.source, pp.status,
       pp.stage, pp.work_mode, pp.inactive_reason
FROM pdlk_participants pp
LEFT JOIN study_programs sp ON sp.id = pp.study_program_id
LEFT JOIN activity_types t ON t.id = pp.activity_type_id
LEFT JOIN partners p ON p.id = pp.partner_id
ORDER BY pp.name;

-- 5b. Prodi masih kosong (tidak tampil di halaman koordinator)
SELECT id, nim, name, status FROM pdlk_participants WHERE study_program_id IS NULL;

-- 5c. Rekap per prodi
SELECT COALESCE(sp.code, '(kosong)') AS prodi,
       COUNT(*) AS total,
       COUNT(*) FILTER (WHERE pp.status = 'active')    AS aktif,
       COUNT(*) FILTER (WHERE pp.status = 'completed') AS selesai,
       COUNT(*) FILTER (WHERE pp.status = 'inactive')  AS tidak_aktif
FROM pdlk_participants pp
LEFT JOIN study_programs sp ON sp.id = pp.study_program_id
GROUP BY sp.code
ORDER BY prodi;

-- 5d. Riwayat perubahan status
SELECT h.created_at AS waktu, pp.nim, pp.name, h.old_status, h.new_status,
       h.reason, u.username AS diubah_oleh
FROM participant_status_histories h
JOIN pdlk_participants pp ON pp.id = h.participant_id
LEFT JOIN users u ON u.id = h.created_by
ORDER BY h.created_at DESC;


-- 6. Alur: pendaftaran passed -> otomatis menjadi mahasiswa aktif
SELECT ap.nim, ap.name, a.title AS lowongan, ap.status AS status_pendaftaran,
       pp.id AS id_peserta, pp.status AS status_peserta
FROM applications ap
JOIN vacancy_details v ON v.id = ap.vacancy_id
JOIN announcements a ON a.id = v.announcement_id
LEFT JOIN pdlk_participants pp ON pp.application_id = ap.id
ORDER BY ap.created_at DESC;
-- Benar bila setiap baris 'passed' memiliki id_peserta, baris lain kosong.


-- 7. Monev: mahasiswa aktif yang belum mengirim monev tengah / akhir
SELECT pp.nim, pp.name, COALESCE(sp.code, '-') AS prodi,
       CASE WHEN mid.id IS NULL THEN 'Belum' ELSE 'Sudah' END AS monev_tengah,
       CASE WHEN fin.id IS NULL THEN 'Belum' ELSE 'Sudah' END AS monev_akhir
FROM pdlk_participants pp
LEFT JOIN study_programs sp ON sp.id = pp.study_program_id
LEFT JOIN LATERAL (
    SELECT r.id FROM monev_responses r
    JOIN monev_schedules s ON s.id = r.schedule_id
    JOIN monev_questionnaires q ON q.id = s.questionnaire_id
    WHERE r.participant_id = pp.id AND q.type = 'mid' AND r.status = 'submitted'
    LIMIT 1
) mid ON TRUE
LEFT JOIN LATERAL (
    SELECT r.id FROM monev_responses r
    JOIN monev_schedules s ON s.id = r.schedule_id
    JOIN monev_questionnaires q ON q.id = s.questionnaire_id
    WHERE r.participant_id = pp.id AND q.type = 'final' AND r.status = 'submitted'
    LIMIT 1
) fin ON TRUE
WHERE pp.status = 'active'
ORDER BY prodi, pp.name;
