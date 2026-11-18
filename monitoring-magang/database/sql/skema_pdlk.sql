-- =====================================================================
-- SKEMA DATABASE SISTEM INFORMASI PDLK - UPT PP-ESTEM IT DEL
-- Satu-satunya query pembuat database (27 tabel + data referensi awal).
--
-- Cara pakai (pgAdmin 4): klik kanan database monitoring_pdlk > Query Tool >
--   Open File > pilih file ini > F5 (tanpa memblok).
--
-- Session & cache Laravel memakai file (SESSION_DRIVER=file, CACHE_STORE=file),
-- jadi tidak ada tabel bawaan Laravel dan tidak ada php artisan migrate.
--
-- PERINGATAN: menghapus seluruh tabel lama BESERTA ISINYA, termasuk users
-- dan peran pengguna. Backup database terlebih dahulu bila datanya perlu.
-- =====================================================================

SET TIME ZONE 'Asia/Jakarta';

BEGIN;

-- 1. Hapus tabel lama: skema Indonesia sebelumnya, tabel bawaan Laravel
--    (tidak dipakai lagi), dan tabel skema ini bila ada
DROP TABLE IF EXISTS
    notifications, submission_histories, activity_submissions,
    sessions, cache, cache_locks, jobs, job_batches, failed_jobs,
    migrations, password_reset_tokens, personal_access_tokens,
    monev_jawaban, monev_pengisian, kuesioner_pertanyaan, kuesioner_monev,
    bukti_pelaksanaan, progres_pelaksanaan, riwayat_status_peserta,
    peserta_pdlk, pendaftaran, lowongan_field_tambahan, lowongan,
    postingan, mitra, jenis_kegiatan_pdlk, program_studi, registrations,
    monev_answers, monev_responses, monev_schedules, monev_questions, monev_questionnaires, participant_status_histories, pdlk_participants, applications, announcement_result_items, announcement_attachments, vacancy_additional_fields, vacancy_details, announcements, gallery_items, galleries, contacts, organization_positions, partners, activity_types, periods, user_roles, users, study_programs, roles
CASCADE;
DROP FUNCTION IF EXISTS set_updated_at() CASCADE;


-- 2. Tabel
CREATE TABLE roles (
    id BIGSERIAL PRIMARY KEY,
    code VARCHAR UNIQUE,
    name VARCHAR,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE study_programs (
    id BIGSERIAL PRIMARY KEY,
    code VARCHAR UNIQUE,
    name VARCHAR,
    level VARCHAR,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    cis_user_id BIGINT UNIQUE,
    username VARCHAR UNIQUE,
    nim VARCHAR,
    name VARCHAR,
    email VARCHAR UNIQUE,
    study_program_id BIGINT,
    cis_role VARCHAR,
    cis_position JSONB,
    last_login_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE user_roles (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT,
    role_id BIGINT,
    study_program_id BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE periods (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR,
    academic_year VARCHAR,
    semester VARCHAR,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE activity_types (
    id BIGSERIAL PRIMARY KEY,
    slug VARCHAR UNIQUE,
    name VARCHAR,
    summary VARCHAR,
    description TEXT,
    duration_text VARCHAR,
    objectives TEXT,
    icon VARCHAR,
    image VARCHAR,
    display_order INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE partners (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR UNIQUE,
    field VARCHAR,
    address VARCHAR,
    website VARCHAR,
    pic_name VARCHAR,
    pic_contact VARCHAR,
    cooperation_status VARCHAR CHECK (cooperation_status IN ('cooperating','non_cooperating','in_process')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE organization_positions (
    id BIGSERIAL PRIMARY KEY,
    position_name VARCHAR,
    description TEXT,
    officer_name VARCHAR,
    display_order INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE contacts (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR,
    contact_type VARCHAR CHECK (contact_type IN ('email','whatsapp','instagram','phone','address','website','other')),
    contact_value VARCHAR,
    icon VARCHAR,
    image VARCHAR,
    display_order INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE galleries (
    id BIGSERIAL PRIMARY KEY,
    title VARCHAR,
    description TEXT,
    cover_image VARCHAR,
    event_date DATE,
    status VARCHAR CHECK (status IN ('draft','published')) DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE gallery_items (
    id BIGSERIAL PRIMARY KEY,
    gallery_id BIGINT,
    file_path VARCHAR,
    caption VARCHAR,
    media_type VARCHAR CHECK (media_type IN ('image','video')),
    display_order INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE announcements (
    id BIGSERIAL PRIMARY KEY,
    type VARCHAR CHECK (type IN ('news','vacancy','result')),
    title VARCHAR,
    slug VARCHAR UNIQUE,
    summary VARCHAR,
    content TEXT,
    cover_image VARCHAR,
    status VARCHAR CHECK (status IN ('draft','published','archived')) DEFAULT 'draft',
    published_at TIMESTAMP,
    expired_at TIMESTAMP,
    source_announcement_id BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE vacancy_details (
    id BIGSERIAL PRIMARY KEY,
    announcement_id BIGINT UNIQUE,
    partner_id BIGINT,
    activity_type_id BIGINT,
    period_id BIGINT,
    position VARCHAR,
    location VARCHAR,
    quota INTEGER,
    criteria TEXT,
    registration_type VARCHAR CHECK (registration_type IN ('internal','external')) DEFAULT 'internal',
    external_url VARCHAR,
    registration_start_date DATE,
    registration_deadline DATE,
    is_closed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE vacancy_additional_fields (
    id BIGSERIAL PRIMARY KEY,
    vacancy_id BIGINT,
    label VARCHAR,
    field_type VARCHAR CHECK (field_type IN ('text','textarea','number','url')),
    is_required BOOLEAN DEFAULT FALSE,
    display_order INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE announcement_attachments (
    id BIGSERIAL PRIMARY KEY,
    announcement_id BIGINT,
    file_path VARCHAR,
    file_name VARCHAR,
    file_size BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE announcement_result_items (
    id BIGSERIAL PRIMARY KEY,
    announcement_id BIGINT,
    student_name VARCHAR,
    nim VARCHAR,
    study_program_id BIGINT,
    display_order INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE applications (
    id BIGSERIAL PRIMARY KEY,
    vacancy_id BIGINT,
    user_id BIGINT,
    nim VARCHAR,
    name VARCHAR,
    study_program_id BIGINT,
    semester SMALLINT,
    gpa NUMERIC(3,2),
    phone VARCHAR,
    email VARCHAR,
    document_path VARCHAR,
    document_original_name VARCHAR,
    additional_answers JSONB,
    status VARCHAR CHECK (status IN ('registered','processing','passed','failed')) DEFAULT 'registered',
    notes TEXT,
    processed_by BIGINT,
    processed_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE pdlk_participants (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT,
    nim VARCHAR,
    name VARCHAR,
    study_program_id BIGINT,
    period_id BIGINT,
    activity_type_id BIGINT,
    partner_id BIGINT,
    application_id BIGINT UNIQUE,
    source VARCHAR CHECK (source IN ('application','manual')) DEFAULT 'manual',
    program_category VARCHAR CHECK (program_category IN ('flagship','independent')),
    position VARCHAR,
    mentor_name VARCHAR,
    advisor_name VARCHAR,
    start_date DATE,
    end_date DATE,
    status VARCHAR CHECK (status IN ('active','inactive','completed')) DEFAULT 'active',
    stage VARCHAR CHECK (stage IN ('onboarding','ongoing')),
    work_mode VARCHAR CHECK (work_mode IN ('onsite','online','remote')),
    location VARCHAR,
    inactive_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE participant_status_histories (
    id BIGSERIAL PRIMARY KEY,
    participant_id BIGINT,
    old_status VARCHAR CHECK (old_status IN ('active','inactive','completed')),
    new_status VARCHAR CHECK (new_status IN ('active','inactive','completed')),
    reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE monev_questionnaires (
    id BIGSERIAL PRIMARY KEY,
    type VARCHAR CHECK (type IN ('mid','final')),
    title VARCHAR,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE monev_questions (
    id BIGSERIAL PRIMARY KEY,
    questionnaire_id BIGINT,
    section_title VARCHAR,
    question_text TEXT,
    question_type VARCHAR CHECK (question_type IN ('text','textarea','scale','choice','file')),
    options JSONB,
    is_required BOOLEAN DEFAULT FALSE,
    display_order INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE monev_schedules (
    id BIGSERIAL PRIMARY KEY,
    questionnaire_id BIGINT,
    period_id BIGINT,
    open_at TIMESTAMP,
    deadline_at TIMESTAMP,
    is_closed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE monev_responses (
    id BIGSERIAL PRIMARY KEY,
    participant_id BIGINT,
    schedule_id BIGINT,
    status VARCHAR CHECK (status IN ('draft','submitted')) DEFAULT 'draft',
    submitted_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE monev_answers (
    id BIGSERIAL PRIMARY KEY,
    response_id BIGINT,
    question_id BIGINT,
    answer_text TEXT,
    answer_number INTEGER,
    file_path VARCHAR,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE
);

-- 3. Index
ALTER TABLE user_roles ADD CONSTRAINT user_roles_user_id_role_id_study_program_id_unique UNIQUE NULLS NOT DISTINCT (user_id, role_id, study_program_id);
ALTER TABLE applications ADD CONSTRAINT applications_vacancy_id_nim_unique UNIQUE (vacancy_id, nim);
CREATE INDEX applications_status_index ON applications (status);
CREATE INDEX pdlk_participants_nim_index ON pdlk_participants (nim);
CREATE INDEX pdlk_participants_status_index ON pdlk_participants (status);
CREATE INDEX pdlk_participants_user_id_period_id_index ON pdlk_participants (user_id, period_id);
ALTER TABLE monev_schedules ADD CONSTRAINT monev_schedules_questionnaire_id_period_id_unique UNIQUE (questionnaire_id, period_id);
ALTER TABLE monev_responses ADD CONSTRAINT monev_responses_participant_id_schedule_id_unique UNIQUE (participant_id, schedule_id);
ALTER TABLE monev_answers ADD CONSTRAINT monev_answers_response_id_question_id_unique UNIQUE (response_id, question_id);

-- 4. Relasi (foreign key)
ALTER TABLE roles ADD CONSTRAINT roles_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE roles ADD CONSTRAINT roles_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE study_programs ADD CONSTRAINT study_programs_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE study_programs ADD CONSTRAINT study_programs_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE users ADD CONSTRAINT users_study_program_id_fkey FOREIGN KEY (study_program_id) REFERENCES study_programs(id);
ALTER TABLE users ADD CONSTRAINT users_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE users ADD CONSTRAINT users_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE user_roles ADD CONSTRAINT user_roles_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE user_roles ADD CONSTRAINT user_roles_role_id_fkey FOREIGN KEY (role_id) REFERENCES roles(id);
ALTER TABLE user_roles ADD CONSTRAINT user_roles_study_program_id_fkey FOREIGN KEY (study_program_id) REFERENCES study_programs(id);
ALTER TABLE user_roles ADD CONSTRAINT user_roles_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE user_roles ADD CONSTRAINT user_roles_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE periods ADD CONSTRAINT periods_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE periods ADD CONSTRAINT periods_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE activity_types ADD CONSTRAINT activity_types_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE activity_types ADD CONSTRAINT activity_types_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE partners ADD CONSTRAINT partners_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE partners ADD CONSTRAINT partners_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE organization_positions ADD CONSTRAINT organization_positions_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE organization_positions ADD CONSTRAINT organization_positions_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE contacts ADD CONSTRAINT contacts_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE contacts ADD CONSTRAINT contacts_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE galleries ADD CONSTRAINT galleries_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE galleries ADD CONSTRAINT galleries_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE gallery_items ADD CONSTRAINT gallery_items_gallery_id_fkey FOREIGN KEY (gallery_id) REFERENCES galleries(id) ON DELETE CASCADE;
ALTER TABLE gallery_items ADD CONSTRAINT gallery_items_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE gallery_items ADD CONSTRAINT gallery_items_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE announcements ADD CONSTRAINT announcements_source_announcement_id_fkey FOREIGN KEY (source_announcement_id) REFERENCES announcements(id);
ALTER TABLE announcements ADD CONSTRAINT announcements_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE announcements ADD CONSTRAINT announcements_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE vacancy_details ADD CONSTRAINT vacancy_details_announcement_id_fkey FOREIGN KEY (announcement_id) REFERENCES announcements(id) ON DELETE CASCADE;
ALTER TABLE vacancy_details ADD CONSTRAINT vacancy_details_partner_id_fkey FOREIGN KEY (partner_id) REFERENCES partners(id);
ALTER TABLE vacancy_details ADD CONSTRAINT vacancy_details_activity_type_id_fkey FOREIGN KEY (activity_type_id) REFERENCES activity_types(id);
ALTER TABLE vacancy_details ADD CONSTRAINT vacancy_details_period_id_fkey FOREIGN KEY (period_id) REFERENCES periods(id);
ALTER TABLE vacancy_details ADD CONSTRAINT vacancy_details_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE vacancy_details ADD CONSTRAINT vacancy_details_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE vacancy_additional_fields ADD CONSTRAINT vacancy_additional_fields_vacancy_id_fkey FOREIGN KEY (vacancy_id) REFERENCES vacancy_details(id) ON DELETE CASCADE;
ALTER TABLE vacancy_additional_fields ADD CONSTRAINT vacancy_additional_fields_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE vacancy_additional_fields ADD CONSTRAINT vacancy_additional_fields_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE announcement_attachments ADD CONSTRAINT announcement_attachments_announcement_id_fkey FOREIGN KEY (announcement_id) REFERENCES announcements(id) ON DELETE CASCADE;
ALTER TABLE announcement_attachments ADD CONSTRAINT announcement_attachments_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE announcement_attachments ADD CONSTRAINT announcement_attachments_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE announcement_result_items ADD CONSTRAINT announcement_result_items_announcement_id_fkey FOREIGN KEY (announcement_id) REFERENCES announcements(id) ON DELETE CASCADE;
ALTER TABLE announcement_result_items ADD CONSTRAINT announcement_result_items_study_program_id_fkey FOREIGN KEY (study_program_id) REFERENCES study_programs(id);
ALTER TABLE announcement_result_items ADD CONSTRAINT announcement_result_items_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE announcement_result_items ADD CONSTRAINT announcement_result_items_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE applications ADD CONSTRAINT applications_vacancy_id_fkey FOREIGN KEY (vacancy_id) REFERENCES vacancy_details(id);
ALTER TABLE applications ADD CONSTRAINT applications_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE applications ADD CONSTRAINT applications_study_program_id_fkey FOREIGN KEY (study_program_id) REFERENCES study_programs(id);
ALTER TABLE applications ADD CONSTRAINT applications_processed_by_fkey FOREIGN KEY (processed_by) REFERENCES users(id);
ALTER TABLE applications ADD CONSTRAINT applications_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE applications ADD CONSTRAINT applications_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE pdlk_participants ADD CONSTRAINT pdlk_participants_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE pdlk_participants ADD CONSTRAINT pdlk_participants_study_program_id_fkey FOREIGN KEY (study_program_id) REFERENCES study_programs(id);
ALTER TABLE pdlk_participants ADD CONSTRAINT pdlk_participants_period_id_fkey FOREIGN KEY (period_id) REFERENCES periods(id);
ALTER TABLE pdlk_participants ADD CONSTRAINT pdlk_participants_activity_type_id_fkey FOREIGN KEY (activity_type_id) REFERENCES activity_types(id);
ALTER TABLE pdlk_participants ADD CONSTRAINT pdlk_participants_partner_id_fkey FOREIGN KEY (partner_id) REFERENCES partners(id);
ALTER TABLE pdlk_participants ADD CONSTRAINT pdlk_participants_application_id_fkey FOREIGN KEY (application_id) REFERENCES applications(id);
ALTER TABLE pdlk_participants ADD CONSTRAINT pdlk_participants_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE pdlk_participants ADD CONSTRAINT pdlk_participants_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE participant_status_histories ADD CONSTRAINT participant_status_histories_participant_id_fkey FOREIGN KEY (participant_id) REFERENCES pdlk_participants(id) ON DELETE CASCADE;
ALTER TABLE participant_status_histories ADD CONSTRAINT participant_status_histories_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE participant_status_histories ADD CONSTRAINT participant_status_histories_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE monev_questionnaires ADD CONSTRAINT monev_questionnaires_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE monev_questionnaires ADD CONSTRAINT monev_questionnaires_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE monev_questions ADD CONSTRAINT monev_questions_questionnaire_id_fkey FOREIGN KEY (questionnaire_id) REFERENCES monev_questionnaires(id) ON DELETE CASCADE;
ALTER TABLE monev_questions ADD CONSTRAINT monev_questions_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE monev_questions ADD CONSTRAINT monev_questions_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE monev_schedules ADD CONSTRAINT monev_schedules_questionnaire_id_fkey FOREIGN KEY (questionnaire_id) REFERENCES monev_questionnaires(id);
ALTER TABLE monev_schedules ADD CONSTRAINT monev_schedules_period_id_fkey FOREIGN KEY (period_id) REFERENCES periods(id);
ALTER TABLE monev_schedules ADD CONSTRAINT monev_schedules_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE monev_schedules ADD CONSTRAINT monev_schedules_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE monev_responses ADD CONSTRAINT monev_responses_participant_id_fkey FOREIGN KEY (participant_id) REFERENCES pdlk_participants(id) ON DELETE CASCADE;
ALTER TABLE monev_responses ADD CONSTRAINT monev_responses_schedule_id_fkey FOREIGN KEY (schedule_id) REFERENCES monev_schedules(id);
ALTER TABLE monev_responses ADD CONSTRAINT monev_responses_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE monev_responses ADD CONSTRAINT monev_responses_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);
ALTER TABLE monev_answers ADD CONSTRAINT monev_answers_response_id_fkey FOREIGN KEY (response_id) REFERENCES monev_responses(id) ON DELETE CASCADE;
ALTER TABLE monev_answers ADD CONSTRAINT monev_answers_question_id_fkey FOREIGN KEY (question_id) REFERENCES monev_questions(id);
ALTER TABLE monev_answers ADD CONSTRAINT monev_answers_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id);
ALTER TABLE monev_answers ADD CONSTRAINT monev_answers_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id);

-- 5. Keterangan kolom
COMMENT ON COLUMN roles.code IS 'student, upt_staff, pdlk_coordinator, program_head';
COMMENT ON COLUMN study_programs.level IS 'D3, D4, S1';
COMMENT ON COLUMN users.nim IS 'Kosong untuk staf/dosen';
COMMENT ON COLUMN users.study_program_id IS 'Prodi asal (mahasiswa)';
COMMENT ON COLUMN users.cis_role IS 'Role bawaan CIS, hanya referensi';
COMMENT ON COLUMN users.cis_position IS 'Jabatan dari CIS, membantu staf menetapkan role';
COMMENT ON COLUMN user_roles.study_program_id IS 'Cakupan koordinator/kaprodi. NULL = seluruh institusi';
COMMENT ON COLUMN partners.field IS 'Bidang usaha';
COMMENT ON COLUMN announcements.source_announcement_id IS 'Hanya type=result: acuan ke lowongan asal';
COMMENT ON COLUMN vacancy_details.external_url IS 'Terisi jika registration_type = external';
COMMENT ON COLUMN vacancy_details.is_closed IS 'Tutup manual sebelum deadline';
COMMENT ON COLUMN applications.document_path IS 'Berkas persyaratan dalam 1 PDF: KTM, KTP, CV, portofolio, transkrip nilai, surat pengantar';
COMMENT ON COLUMN applications.additional_answers IS 'Jawaban field tambahan lowongan';
COMMENT ON COLUMN applications.created_at IS 'Sekaligus waktu pendaftaran dikirim';
COMMENT ON COLUMN pdlk_participants.application_id IS 'Terisi jika berasal dari pendaftaran internal yang Lulus';
COMMENT ON COLUMN pdlk_participants.source IS 'manual = diinput Staf UPT / Koordinator (lihat created_by)';
COMMENT ON COLUMN pdlk_participants.program_category IS 'Menunggu konfirmasi UPT';
COMMENT ON COLUMN pdlk_participants.advisor_name IS 'Dosen pembimbing (teks)';
COMMENT ON COLUMN pdlk_participants.stage IS 'Diperbarui mahasiswa';
COMMENT ON COLUMN pdlk_participants.work_mode IS 'Diperbarui mahasiswa';
COMMENT ON COLUMN participant_status_histories.created_at IS 'Waktu perubahan status';
COMMENT ON COLUMN participant_status_histories.created_by IS 'Yang mengubah status';
COMMENT ON COLUMN monev_questions.section_title IS 'Pengelompokan bagian, tanpa tabel terpisah';
COMMENT ON COLUMN monev_questions.options IS 'Pilihan jawaban untuk scale/choice';
COMMENT ON COLUMN monev_responses.submitted_at IS 'Data bisnis: dipakai membandingkan dengan tenggat';
COMMENT ON COLUMN monev_answers.answer_number IS 'Nilai skala, untuk dirata-rata';
COMMENT ON COLUMN monev_answers.file_path IS 'Bukti pelaksanaan (question_type = file)';

-- 6. Data referensi awal
INSERT INTO roles (code, name) VALUES
    ('student',          'Mahasiswa'),
    ('upt_staff',        'Staf UPT PP-ESTEM'),
    ('pdlk_coordinator', 'Koordinator PDLK Program Studi'),
    ('program_head',     'Koordinator Program Studi');

INSERT INTO activity_types (slug, name, summary, display_order) VALUES
    ('magang',             'Magang',                   'Pembelajaran di industri atau lembaga yang relevan dengan program studi.', 1),
    ('kerja-praktik',      'Kerja Praktik',            'Penerapan ilmu dan keterampilan perkuliahan di lingkungan kerja nyata.',   2),
    ('penelitian',         'Penelitian / Riset',       'Kegiatan penelitian non-skripsi bagi mahasiswa yang berminat meneliti.',   3),
    ('pertukaran-pelajar', 'Pertukaran Pelajar',       'Perkuliahan di perguruan tinggi lain, di dalam maupun luar negeri.',       4),
    ('kkn',                'Kuliah Kerja Nyata (KKN)', 'Membangun desa bersama masyarakat selama satu sampai dua semester.',       5),
    ('studi-independen',   'Studi Independen',         'Mewujudkan karya atau produk inovatif yang dapat dilombakan.',             6),
    ('kewirausahaan',      'Wirausaha',                'Pengembangan usaha mahasiswa secara terbimbing.',                          7);

-- Periksa kembali nama dan kode prodi sesuai data resmi IT Del.
INSERT INTO study_programs (code, name, level) VALUES
    ('D3TI',   'Teknologi Informasi',                'D3'),
    ('D3TK',   'Teknologi Komputer',                 'D3'),
    ('D4TRPL', 'Teknologi Rekayasa Perangkat Lunak', 'D4'),
    ('S1IF',   'Informatika',                        'S1'),
    ('S1SI',   'Sistem Informasi',                   'S1'),
    ('S1TE',   'Teknik Elektro',                     'S1'),
    ('S1MR',   'Manajemen Rekayasa',                 'S1'),
    ('S1TB',   'Teknik Bioproses',                   'S1'),
    ('S1BT',   'Bioteknologi',                       'S1'),
    ('S1MT',   'Teknik Metalurgi',                   'S1');

-- 7. Tabel alur verifikasi, jalur mandiri, dan notifikasi

-- 7.1. Kontak pembimbing lapangan pada data mahasiswa aktif
ALTER TABLE pdlk_participants ADD COLUMN IF NOT EXISTS mentor_contact VARCHAR;
COMMENT ON COLUMN pdlk_participants.mentor_contact IS 'Kontak pembimbing lapangan (HP/email)';

-- 7.2. Pengajuan data kegiatan
--    submission_type:
--      after_selection = melengkapi data setelah lulus seleksi lowongan
--                        (diverifikasi staf UPT saja)
--      independent     = jalur mandiri (diverifikasi koordinator PDLK prodi,
--                        lalu staf UPT)
--    status:
--      draft -> pending_coordinator -> rejected_coordinator | pending_upt
--            -> rejected_upt | approved
--      Pengajuan yang ditolak UPT dan dikirim ulang kembali ke pending_coordinator
--      (jalur mandiri) atau pending_upt (setelah seleksi).
CREATE TABLE IF NOT EXISTS activity_submissions (
    id BIGSERIAL PRIMARY KEY,
    submission_type VARCHAR NOT NULL CHECK (submission_type IN ('after_selection','independent')),
    user_id BIGINT NOT NULL,
    application_id BIGINT,
    participant_id BIGINT,
    nim VARCHAR NOT NULL,
    name VARCHAR NOT NULL,
    study_program_id BIGINT,
    period_id BIGINT,
    activity_type_id BIGINT,
    partner_id BIGINT,
    partner_address VARCHAR,
    position VARCHAR,
    job_description TEXT,
    location VARCHAR,
    work_mode VARCHAR CHECK (work_mode IN ('onsite','online','remote')),
    start_date DATE,
    end_date DATE,
    mentor_name VARCHAR,
    mentor_contact VARCHAR,
    advisor_name VARCHAR,
    document_path VARCHAR,
    document_original_name VARCHAR,
    status VARCHAR NOT NULL DEFAULT 'draft'
        CHECK (status IN ('draft','pending_coordinator','rejected_coordinator','pending_upt','rejected_upt','approved')),
    last_reason TEXT,
    submitted_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE,
    CONSTRAINT activity_submissions_tanggal_check CHECK (end_date IS NULL OR start_date IS NULL OR end_date >= start_date),
    CONSTRAINT activity_submissions_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT activity_submissions_application_id_fkey FOREIGN KEY (application_id) REFERENCES applications(id),
    CONSTRAINT activity_submissions_participant_id_fkey FOREIGN KEY (participant_id) REFERENCES pdlk_participants(id),
    CONSTRAINT activity_submissions_study_program_id_fkey FOREIGN KEY (study_program_id) REFERENCES study_programs(id),
    CONSTRAINT activity_submissions_period_id_fkey FOREIGN KEY (period_id) REFERENCES periods(id),
    CONSTRAINT activity_submissions_activity_type_id_fkey FOREIGN KEY (activity_type_id) REFERENCES activity_types(id),
    CONSTRAINT activity_submissions_partner_id_fkey FOREIGN KEY (partner_id) REFERENCES partners(id),
    CONSTRAINT activity_submissions_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id),
    CONSTRAINT activity_submissions_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id)
);
CREATE INDEX IF NOT EXISTS activity_submissions_status_index ON activity_submissions (status);
CREATE INDEX IF NOT EXISTS activity_submissions_nim_index ON activity_submissions (nim);
-- Satu pengajuan data kegiatan per pendaftaran yang lulus
CREATE UNIQUE INDEX IF NOT EXISTS activity_submissions_application_id_unique
    ON activity_submissions (application_id) WHERE application_id IS NOT NULL;

COMMENT ON COLUMN activity_submissions.application_id IS 'Terisi untuk after_selection: pendaftaran yang lulus';
COMMENT ON COLUMN activity_submissions.participant_id IS 'Terisi setelah disetujui UPT: data mahasiswa aktif yang dibuat/diperbarui';
COMMENT ON COLUMN activity_submissions.last_reason IS 'Alasan penolakan terakhir (riwayat lengkap di submission_histories)';
COMMENT ON COLUMN activity_submissions.submitted_at IS 'Waktu kirim/kirim ulang terakhir';

-- 7.3. Riwayat pengajuan: tidak pernah dihapus, termasuk riwayat penolakan
CREATE TABLE IF NOT EXISTS submission_histories (
    id BIGSERIAL PRIMARY KEY,
    submission_id BIGINT NOT NULL,
    action VARCHAR NOT NULL CHECK (action IN ('submitted','resubmitted','approved','rejected')),
    actor_role VARCHAR CHECK (actor_role IN ('student','pdlk_coordinator','upt_staff')),
    old_status VARCHAR,
    new_status VARCHAR,
    reason TEXT,
    notes TEXT,
    data_snapshot JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE,
    CONSTRAINT submission_histories_alasan_check CHECK (action <> 'rejected' OR NULLIF(TRIM(reason), '') IS NOT NULL),
    CONSTRAINT submission_histories_submission_id_fkey FOREIGN KEY (submission_id) REFERENCES activity_submissions(id) ON DELETE CASCADE,
    CONSTRAINT submission_histories_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id),
    CONSTRAINT submission_histories_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id)
);
CREATE INDEX IF NOT EXISTS submission_histories_submission_id_index ON submission_histories (submission_id);

COMMENT ON COLUMN submission_histories.created_at IS 'Waktu verifikasi/pengiriman';
COMMENT ON COLUMN submission_histories.created_by IS 'Pengguna yang mengirim atau memverifikasi';
COMMENT ON COLUMN submission_histories.reason IS 'Wajib diisi bila action = rejected';
COMMENT ON COLUMN submission_histories.data_snapshot IS 'Salinan data pengajuan saat itu (riwayat perubahan data)';

-- 7.4. Notifikasi dashboard
CREATE TABLE IF NOT EXISTS notifications (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL,
    type VARCHAR NOT NULL CHECK (type IN (
        'selection_result','data_requested','submission_approved','submission_rejected',
        'submission_forwarded','monev_reminder','other')),
    title VARCHAR NOT NULL,
    message TEXT,
    url VARCHAR,
    read_at TIMESTAMP,
    emailed_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT,
    is_active BOOLEAN DEFAULT TRUE,
    CONSTRAINT notifications_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT notifications_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id),
    CONSTRAINT notifications_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id)
);
CREATE INDEX IF NOT EXISTS notifications_user_id_read_at_index ON notifications (user_id, read_at);

COMMENT ON COLUMN notifications.emailed_at IS 'Terisi bila notifikasi juga dikirim ke email (hasil seleksi)';

-- 8. Hak akses akun aplikasi: jadikan monitoring_app pemilik semua tabel
--    (file ini biasanya dijalankan sebagai postgres di pgAdmin)
DO $$
DECLARE r RECORD;
BEGIN
    IF EXISTS (SELECT 1 FROM pg_roles WHERE rolname = 'monitoring_app')
       AND current_user <> 'monitoring_app' THEN
        FOR r IN SELECT tablename FROM pg_tables WHERE schemaname = current_schema() LOOP
            EXECUTE format('ALTER TABLE %I OWNER TO monitoring_app', r.tablename);
        END LOOP;
    END IF;
END $$;

COMMIT;

-- Cek: harus tepat 27 tabel
SELECT COUNT(*) AS jumlah_tabel
FROM information_schema.tables
WHERE table_schema = current_schema() AND table_type = 'BASE TABLE';
