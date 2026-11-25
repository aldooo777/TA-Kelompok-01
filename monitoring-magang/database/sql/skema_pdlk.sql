-- =====================================================================
-- SKEMA DATABASE SISTEM INFORMASI PDLK - UPT PP-ESTEM IT DEL (MySQL / MariaDB)
-- Satu-satunya query pembuat database (27 tabel + data referensi awal).
--
-- Cara pakai (XAMPP / phpMyAdmin):
--   1. Buat database monitoring_pdlk dengan collation utf8mb4_unicode_ci.
--   2. Klik database monitoring_pdlk di panel kiri (WAJIB dipilih dulu).
--   3. Tab Import > pilih file ini > Import.
--
-- Session & cache Laravel memakai file (SESSION_DRIVER=file, CACHE_STORE=file),
-- jadi tidak ada tabel bawaan Laravel dan tidak ada php artisan migrate.
--
-- PERINGATAN: menghapus seluruh tabel lama BESERTA ISINYA, termasuk users
-- dan peran pengguna. Backup (Export) database terlebih dahulu bila perlu.
-- =====================================================================

SET NAMES utf8mb4;
SET time_zone = '+07:00';
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Hapus tabel lama
DROP TABLE IF EXISTS
    notifications, submission_histories, activity_submissions, sessions, cache, cache_locks,
    jobs, job_batches, failed_jobs, migrations, password_reset_tokens, personal_access_tokens,
    monev_jawaban, monev_pengisian, kuesioner_pertanyaan, kuesioner_monev, bukti_pelaksanaan, progres_pelaksanaan,
    riwayat_status_peserta, peserta_pdlk, pendaftaran, lowongan_field_tambahan, lowongan, postingan,
    mitra, jenis_kegiatan_pdlk, program_studi, registrations, monev_answers, monev_responses,
    monev_schedules, monev_questions, monev_questionnaires, participant_status_histories, pdlk_participants, applications,
    announcement_result_items, announcement_attachments, vacancy_additional_fields, vacancy_details, announcements, gallery_items,
    galleries, contacts, organization_positions, partners, activity_types, periods,
    user_roles, users, study_programs, roles;


-- 2. Tabel
CREATE TABLE `roles` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(255) UNIQUE COMMENT 'student, upt_staff, pdlk_coordinator, program_head',
    `name` VARCHAR(255),
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `study_programs` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(255) UNIQUE,
    `name` VARCHAR(255),
    `level` VARCHAR(255) COMMENT 'D3, D4, S1',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `cis_user_id` BIGINT UNIQUE,
    `username` VARCHAR(255) UNIQUE,
    `nim` VARCHAR(255) COMMENT 'Kosong untuk staf/dosen',
    `name` VARCHAR(255),
    `email` VARCHAR(255) UNIQUE,
    `study_program_id` BIGINT COMMENT 'Prodi asal (mahasiswa)',
    `cis_role` VARCHAR(255) COMMENT 'Role bawaan CIS, hanya referensi',
    `cis_position` JSON COMMENT 'Jabatan dari CIS, membantu staf menetapkan role',
    `last_login_at` DATETIME,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_roles` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT,
    `role_id` BIGINT,
    `study_program_id` BIGINT COMMENT 'Cakupan koordinator/kaprodi. NULL = seluruh institusi',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `periods` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255),
    `academic_year` VARCHAR(255),
    `semester` VARCHAR(255),
    `start_date` DATE,
    `end_date` DATE,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `activity_types` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(255) UNIQUE,
    `name` VARCHAR(255),
    `summary` VARCHAR(255),
    `description` TEXT,
    `duration_text` VARCHAR(255),
    `objectives` TEXT,
    `icon` VARCHAR(255),
    `image` VARCHAR(255),
    `display_order` INTEGER,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `partners` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) UNIQUE,
    `field` VARCHAR(255) COMMENT 'Bidang usaha',
    `address` VARCHAR(255),
    `website` VARCHAR(255),
    `pic_name` VARCHAR(255),
    `pic_contact` VARCHAR(255),
    `cooperation_status` VARCHAR(255) CHECK (cooperation_status IN ('cooperating','non_cooperating','in_process')),
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `organization_positions` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `position_name` VARCHAR(255),
    `description` TEXT,
    `officer_name` VARCHAR(255),
    `display_order` INTEGER,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contacts` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255),
    `contact_type` VARCHAR(255) CHECK (contact_type IN ('email','whatsapp','instagram','phone','address','website','other')),
    `contact_value` VARCHAR(255),
    `icon` VARCHAR(255),
    `image` VARCHAR(255),
    `display_order` INTEGER,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `galleries` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255),
    `description` TEXT,
    `cover_image` VARCHAR(255),
    `event_date` DATE,
    `status` VARCHAR(255) DEFAULT 'draft' CHECK (status IN ('draft','published')),
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gallery_items` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `gallery_id` BIGINT,
    `file_path` VARCHAR(255),
    `caption` VARCHAR(255),
    `media_type` VARCHAR(255) CHECK (media_type IN ('image','video')),
    `display_order` INTEGER,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `announcements` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `type` VARCHAR(255) CHECK (type IN ('news','vacancy','result')),
    `title` VARCHAR(255),
    `slug` VARCHAR(255) UNIQUE,
    `summary` VARCHAR(255),
    `content` TEXT,
    `cover_image` VARCHAR(255),
    `status` VARCHAR(255) DEFAULT 'draft' CHECK (status IN ('draft','published','archived')),
    `published_at` DATETIME,
    `expired_at` DATETIME,
    `source_announcement_id` BIGINT COMMENT 'Hanya type=result: acuan ke lowongan asal',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `vacancy_details` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `announcement_id` BIGINT UNIQUE,
    `partner_id` BIGINT,
    `activity_type_id` BIGINT,
    `period_id` BIGINT,
    `position` VARCHAR(255),
    `location` VARCHAR(255),
    `quota` INTEGER,
    `criteria` TEXT,
    `registration_type` VARCHAR(255) DEFAULT 'internal' CHECK (registration_type IN ('internal','external')),
    `external_url` VARCHAR(255) COMMENT 'Terisi jika registration_type = external',
    `registration_start_date` DATE,
    `registration_deadline` DATE,
    `is_closed` BOOLEAN DEFAULT FALSE COMMENT 'Tutup manual sebelum deadline',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `vacancy_additional_fields` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `vacancy_id` BIGINT,
    `label` VARCHAR(255),
    `field_type` VARCHAR(255) CHECK (field_type IN ('text','textarea','number','url')),
    `is_required` BOOLEAN DEFAULT FALSE,
    `display_order` INTEGER,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `announcement_attachments` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `announcement_id` BIGINT,
    `file_path` VARCHAR(255),
    `file_name` VARCHAR(255),
    `file_size` BIGINT,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `announcement_result_items` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `announcement_id` BIGINT,
    `student_name` VARCHAR(255),
    `nim` VARCHAR(255),
    `study_program_id` BIGINT,
    `display_order` INTEGER,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `applications` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `vacancy_id` BIGINT,
    `user_id` BIGINT,
    `nim` VARCHAR(255),
    `name` VARCHAR(255),
    `study_program_id` BIGINT,
    `semester` SMALLINT,
    `gpa` DECIMAL(3,2),
    `phone` VARCHAR(255),
    `email` VARCHAR(255),
    `document_path` VARCHAR(255) COMMENT 'Berkas persyaratan dalam 1 PDF: KTM, KTP, CV, portofolio, transkrip nilai, surat pengantar',
    `document_original_name` VARCHAR(255),
    `additional_answers` JSON COMMENT 'Jawaban field tambahan lowongan',
    `status` VARCHAR(255) DEFAULT 'registered' CHECK (status IN ('registered','processing','passed','failed')),
    `notes` TEXT,
    `processed_by` BIGINT,
    `processed_at` DATETIME,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Sekaligus waktu pendaftaran dikirim',
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pdlk_participants` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT,
    `nim` VARCHAR(255),
    `name` VARCHAR(255),
    `study_program_id` BIGINT,
    `period_id` BIGINT,
    `activity_type_id` BIGINT,
    `partner_id` BIGINT,
    `application_id` BIGINT UNIQUE COMMENT 'Terisi jika berasal dari pendaftaran internal yang Lulus',
    `source` VARCHAR(255) DEFAULT 'manual' COMMENT 'manual = diinput Staf UPT / Koordinator (lihat created_by)' CHECK (source IN ('application','manual')),
    `program_category` VARCHAR(255) COMMENT 'Menunggu konfirmasi UPT' CHECK (program_category IN ('flagship','independent')),
    `position` VARCHAR(255),
    `mentor_name` VARCHAR(255),
    `advisor_name` VARCHAR(255) COMMENT 'Dosen pembimbing (teks)',
    `start_date` DATE,
    `end_date` DATE,
    `status` VARCHAR(255) DEFAULT 'active' CHECK (status IN ('active','inactive','completed')),
    `stage` VARCHAR(255) COMMENT 'Diperbarui mahasiswa' CHECK (stage IN ('onboarding','ongoing')),
    `work_mode` VARCHAR(255) COMMENT 'Diperbarui mahasiswa' CHECK (work_mode IN ('onsite','online','remote')),
    `location` VARCHAR(255),
    `inactive_reason` TEXT,
    `mentor_contact` VARCHAR(255) COMMENT 'Kontak pembimbing lapangan (HP/email)',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `participant_status_histories` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `participant_id` BIGINT,
    `old_status` VARCHAR(255) CHECK (old_status IN ('active','inactive','completed')),
    `new_status` VARCHAR(255) CHECK (new_status IN ('active','inactive','completed')),
    `reason` TEXT,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu perubahan status',
    `created_by` BIGINT COMMENT 'Yang mengubah status',
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `monev_questionnaires` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `type` VARCHAR(255) CHECK (type IN ('mid','final')),
    `title` VARCHAR(255),
    `description` TEXT,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `monev_questions` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `questionnaire_id` BIGINT,
    `section_title` VARCHAR(255) COMMENT 'Pengelompokan bagian, tanpa tabel terpisah',
    `question_text` TEXT,
    `question_type` VARCHAR(255) CHECK (question_type IN ('text','textarea','scale','choice','file')),
    `options` JSON COMMENT 'Pilihan jawaban untuk scale/choice',
    `is_required` BOOLEAN DEFAULT FALSE,
    `display_order` INTEGER,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `monev_schedules` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `questionnaire_id` BIGINT,
    `period_id` BIGINT,
    `open_at` DATETIME,
    `deadline_at` DATETIME,
    `is_closed` BOOLEAN DEFAULT FALSE,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `monev_responses` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `participant_id` BIGINT,
    `schedule_id` BIGINT,
    `status` VARCHAR(255) DEFAULT 'draft' CHECK (status IN ('draft','submitted')),
    `submitted_at` DATETIME COMMENT 'Data bisnis: dipakai membandingkan dengan tenggat',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `monev_answers` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `response_id` BIGINT,
    `question_id` BIGINT,
    `answer_text` TEXT,
    `answer_number` INTEGER COMMENT 'Nilai skala, untuk dirata-rata',
    `file_path` VARCHAR(255) COMMENT 'Bukti pelaksanaan (question_type = file)',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `activity_submissions` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `submission_type` VARCHAR(255) NOT NULL CHECK (submission_type IN ('after_selection','independent')),
    `user_id` BIGINT NOT NULL,
    `application_id` BIGINT COMMENT 'Terisi untuk after_selection: pendaftaran yang lulus',
    `participant_id` BIGINT COMMENT 'Terisi setelah disetujui UPT: data mahasiswa aktif yang dibuat/diperbarui',
    `nim` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `study_program_id` BIGINT,
    `period_id` BIGINT,
    `activity_type_id` BIGINT,
    `partner_id` BIGINT,
    `partner_address` VARCHAR(255),
    `position` VARCHAR(255),
    `job_description` TEXT,
    `location` VARCHAR(255),
    `work_mode` VARCHAR(255) CHECK (work_mode IN ('onsite','online','remote')),
    `start_date` DATE,
    `end_date` DATE,
    `mentor_name` VARCHAR(255),
    `mentor_contact` VARCHAR(255),
    `advisor_name` VARCHAR(255),
    `document_path` VARCHAR(255),
    `document_original_name` VARCHAR(255),
    `status` VARCHAR(255) NOT NULL DEFAULT 'draft' CHECK (status IN ('draft','pending_coordinator','rejected_coordinator','pending_upt','rejected_upt','approved')),
    `last_reason` TEXT COMMENT 'Alasan penolakan terakhir (riwayat lengkap di submission_histories)',
    `submitted_at` DATETIME COMMENT 'Waktu kirim/kirim ulang terakhir',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `submission_histories` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `submission_id` BIGINT NOT NULL,
    `action` VARCHAR(255) NOT NULL CHECK (action IN ('submitted','resubmitted','approved','rejected')),
    `actor_role` VARCHAR(255) CHECK (actor_role IN ('student','pdlk_coordinator','upt_staff')),
    `old_status` VARCHAR(255),
    `new_status` VARCHAR(255),
    `reason` TEXT COMMENT 'Wajib diisi bila action = rejected',
    `notes` TEXT,
    `data_snapshot` JSON COMMENT 'Salinan data pengajuan saat itu (riwayat perubahan data)',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu verifikasi/pengiriman',
    `created_by` BIGINT COMMENT 'Pengguna yang mengirim atau memverifikasi',
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE,
    CONSTRAINT submission_histories_alasan_check CHECK (action <> 'rejected' OR NULLIF(TRIM(reason), '') IS NOT NULL),
    CONSTRAINT submission_histories_submission_id_fkey FOREIGN KEY (submission_id) REFERENCES activity_submissions(id) ON DELETE CASCADE,
    CONSTRAINT submission_histories_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id),
    CONSTRAINT submission_histories_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notifications` (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT NOT NULL,
    `type` VARCHAR(255) NOT NULL CHECK (type IN ( 'selection_result','data_requested','submission_approved','submission_rejected', 'submission_forwarded','monev_reminder','other')),
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT,
    `url` VARCHAR(255),
    `read_at` DATETIME,
    `emailed_at` DATETIME COMMENT 'Terisi bila notifikasi juga dikirim ke email (hasil seleksi)',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` BIGINT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_by` BIGINT,
    `is_active` BOOLEAN DEFAULT TRUE,
    CONSTRAINT notifications_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT notifications_created_by_fkey FOREIGN KEY (created_by) REFERENCES users(id),
    CONSTRAINT notifications_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Index dan relasi
ALTER TABLE user_roles ADD CONSTRAINT user_roles_user_id_role_id_study_program_id_unique UNIQUE (user_id, role_id, study_program_id);
ALTER TABLE applications ADD CONSTRAINT applications_vacancy_id_nim_unique UNIQUE (vacancy_id, nim);
CREATE INDEX applications_status_index ON applications (status);
CREATE INDEX pdlk_participants_nim_index ON pdlk_participants (nim);
CREATE INDEX pdlk_participants_status_index ON pdlk_participants (status);
CREATE INDEX pdlk_participants_user_id_period_id_index ON pdlk_participants (user_id, period_id);
ALTER TABLE monev_schedules ADD CONSTRAINT monev_schedules_questionnaire_id_period_id_unique UNIQUE (questionnaire_id, period_id);
ALTER TABLE monev_responses ADD CONSTRAINT monev_responses_participant_id_schedule_id_unique UNIQUE (participant_id, schedule_id);
ALTER TABLE monev_answers ADD CONSTRAINT monev_answers_response_id_question_id_unique UNIQUE (response_id, question_id);
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
CREATE INDEX activity_submissions_status_index ON activity_submissions (status);
CREATE INDEX activity_submissions_nim_index ON activity_submissions (nim);
CREATE INDEX submission_histories_submission_id_index ON submission_histories (submission_id);
CREATE INDEX notifications_user_id_read_at_index ON notifications (user_id, read_at);
CREATE UNIQUE INDEX activity_submissions_application_id_unique ON activity_submissions (application_id);  -- NULL boleh lebih dari satu

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

SET FOREIGN_KEY_CHECKS = 1;

-- Cek: harus tepat 27 tabel
SELECT COUNT(*) AS jumlah_tabel
FROM information_schema.tables
WHERE table_schema = DATABASE() AND table_type = 'BASE TABLE';
