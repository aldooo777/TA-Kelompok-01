-- =====================================================================
-- SKEMA DATABASE SISTEM INFORMASI PDLK - UPT PP-ESTEM IT DEL
-- Database : monitoring_pdlk (PostgreSQL 16)
-- Dijalankan MANUAL di pgAdmin 4:
--   klik kanan monitoring_pdlk > Query Tool > Open File > pilih file ini
--   > tekan F5 (jalankan seluruh isi file, tanpa memblok).
--
-- Konvensi tim: SETIAP tabel PDLK wajib memiliki kolom
--   submitted_at TIMESTAMP,
--   created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--   updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--   created_by   BIGINT REFERENCES users(id)
--
-- Tabel bawaan Laravel (users, sessions, cache, jobs, migrations, dst.)
-- tetap dibuat oleh: php artisan migrate
-- Tabel users hanya DIPERLUAS di sini (ALTER), tidak dibuat ulang.
--
-- PERINGATAN: bagian 1 menghapus tabel PDLK lama beserta isinya.
-- Backup dulu database sebelum menjalankan file ini.
-- =====================================================================

BEGIN;

-- ---------------------------------------------------------------------
-- 1. Bersihkan tabel PDLK lama (yang sebelumnya dibuat lewat migration)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS
    monev_jawaban, monev_pengisian, kuesioner_pertanyaan, kuesioner_monev,
    bukti_pelaksanaan, progres_pelaksanaan, riwayat_status_peserta,
    peserta_pdlk, pendaftaran, lowongan_field_tambahan, lowongan,
    postingan, mitra, jenis_kegiatan_pdlk, user_roles, roles,
    registrations
CASCADE;

ALTER TABLE users DROP COLUMN IF EXISTS program_studi_id;
DROP TABLE IF EXISTS program_studi CASCADE;

-- Catatan migration lama dihapus agar "php artisan migrate" tidak
-- mencoba membuat ulang tabel PDLK.
DELETE FROM migrations
WHERE migration LIKE '2026_10_20_%registrations%'
   OR migration LIKE '2026_11_12_%peserta_pdlk%'
   OR migration LIKE '2026_11_13_%';

-- ---------------------------------------------------------------------
-- 2. Fungsi trigger: updated_at otomatis saat baris diubah
--    (juga berlaku bila data diubah langsung dari pgAdmin)
-- ---------------------------------------------------------------------
CREATE OR REPLACE FUNCTION set_updated_at() RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at := CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- ---------------------------------------------------------------------
-- 3. MASTER DATA
-- ---------------------------------------------------------------------
CREATE TABLE program_studi (
    id           BIGSERIAL PRIMARY KEY,
    kode         VARCHAR(20)  NOT NULL UNIQUE,
    nama         VARCHAR(150) NOT NULL,
    jenjang      VARCHAR(10),
    submitted_at TIMESTAMP,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by   BIGINT REFERENCES users(id) ON DELETE SET NULL
);

-- Perluasan tabel users bawaan Laravel: identitas dari CIS.
-- Password CIS TIDAK disimpan.
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS cis_user_id      BIGINT UNIQUE,
    ADD COLUMN IF NOT EXISTS username         VARCHAR(100) UNIQUE,
    ADD COLUMN IF NOT EXISTS nim              VARCHAR(30),
    ADD COLUMN IF NOT EXISTS cis_role         VARCHAR(50),
    ADD COLUMN IF NOT EXISTS cis_jabatan      JSONB,
    ADD COLUMN IF NOT EXISTS program_studi_id BIGINT REFERENCES program_studi(id) ON DELETE SET NULL,
    ADD COLUMN IF NOT EXISTS last_login_at    TIMESTAMP,
    ADD COLUMN IF NOT EXISTS submitted_at     TIMESTAMP,
    ADD COLUMN IF NOT EXISTS created_by       BIGINT REFERENCES users(id) ON DELETE SET NULL;

ALTER TABLE users ALTER COLUMN password   DROP NOT NULL;
ALTER TABLE users ALTER COLUMN created_at SET DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE users ALTER COLUMN updated_at SET DEFAULT CURRENT_TIMESTAMP;
CREATE INDEX IF NOT EXISTS users_nim_index ON users (nim);

CREATE TABLE roles (
    id           BIGSERIAL PRIMARY KEY,
    kode         VARCHAR(50)  NOT NULL UNIQUE,
    nama         VARCHAR(100) NOT NULL,
    submitted_at TIMESTAMP,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by   BIGINT REFERENCES users(id) ON DELETE SET NULL
);

-- Satu akun dapat memiliki lebih dari satu peran.
-- program_studi_id = cakupan koordinator; NULL = seluruh institusi.
CREATE TABLE user_roles (
    id               BIGSERIAL PRIMARY KEY,
    user_id          BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    role_id          BIGINT NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    program_studi_id BIGINT REFERENCES program_studi(id) ON DELETE SET NULL,
    submitted_at     TIMESTAMP,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by       BIGINT REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT user_roles_unik UNIQUE NULLS NOT DISTINCT (user_id, role_id, program_studi_id)
);

CREATE TABLE jenis_kegiatan_pdlk (
    id           BIGSERIAL PRIMARY KEY,
    slug         VARCHAR(50)  NOT NULL UNIQUE,
    nama         VARCHAR(100) NOT NULL,
    deskripsi    TEXT,
    urutan       SMALLINT NOT NULL DEFAULT 0,
    submitted_at TIMESTAMP,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by   BIGINT REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE mitra (
    id           BIGSERIAL PRIMARY KEY,
    nama         VARCHAR(200) NOT NULL UNIQUE,
    bidang       VARCHAR(150),
    alamat       VARCHAR(255),
    website      VARCHAR(255),
    submitted_at TIMESTAMP,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by   BIGINT REFERENCES users(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------------------
-- 4. INFORMASI PUBLIK DAN LOWONGAN
-- ---------------------------------------------------------------------
-- Berita / pengumuman / kegiatan: foto + narasi, dikelola Staf UPT.
CREATE TABLE postingan (
    id           BIGSERIAL PRIMARY KEY,
    jenis        VARCHAR(20)  NOT NULL DEFAULT 'berita'
                 CHECK (jenis IN ('berita', 'pengumuman', 'kegiatan')),
    judul        VARCHAR(200) NOT NULL,
    slug         VARCHAR(220) NOT NULL UNIQUE,
    narasi       TEXT NOT NULL,
    foto_path    VARCHAR(255),
    is_published BOOLEAN NOT NULL DEFAULT TRUE,
    published_at TIMESTAMP,
    submitted_at TIMESTAMP,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by   BIGINT REFERENCES users(id) ON DELETE SET NULL
);
CREATE INDEX postingan_terbit_index ON postingan (is_published, published_at);

CREATE TABLE lowongan (
    id                BIGSERIAL PRIMARY KEY,
    judul             VARCHAR(200) NOT NULL,
    slug              VARCHAR(220) NOT NULL UNIQUE,
    mitra_id          BIGINT NOT NULL REFERENCES mitra(id) ON DELETE RESTRICT,
    jenis_kegiatan_id BIGINT NOT NULL REFERENCES jenis_kegiatan_pdlk(id) ON DELETE RESTRICT,
    posisi            VARCHAR(150),
    lokasi            VARCHAR(150),
    deskripsi         TEXT NOT NULL,
    kriteria          TEXT NOT NULL,
    kuota             INTEGER CHECK (kuota IS NULL OR kuota > 0),
    tanggal_buka      DATE,
    tanggal_tutup     DATE,
    poster_path       VARCHAR(255),
    is_published      BOOLEAN NOT NULL DEFAULT TRUE,
    submitted_at      TIMESTAMP,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by        BIGINT REFERENCES users(id) ON DELETE SET NULL,
    CHECK (tanggal_tutup IS NULL OR tanggal_buka IS NULL OR tanggal_tutup >= tanggal_buka)
);
CREATE INDEX lowongan_terbit_index ON lowongan (is_published, tanggal_tutup);

-- Isian tambahan (TIDAK wajib) sesuai permintaan mitra, per lowongan.
CREATE TABLE lowongan_field_tambahan (
    id           BIGSERIAL PRIMARY KEY,
    lowongan_id  BIGINT NOT NULL REFERENCES lowongan(id) ON DELETE CASCADE,
    label        VARCHAR(150) NOT NULL,
    tipe         VARCHAR(20)  NOT NULL DEFAULT 'text'
                 CHECK (tipe IN ('text', 'textarea', 'number', 'url')),
    urutan       SMALLINT NOT NULL DEFAULT 0,
    submitted_at TIMESTAMP,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by   BIGINT REFERENCES users(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------------------
-- 5. PENDAFTARAN
--    Formulir satu format untuk semua lowongan.
--    Status: terdaftar -> proses -> lulus / tidak_lulus
--    submitted_at = waktu mahasiswa mengirim formulir.
-- ---------------------------------------------------------------------
CREATE TABLE pendaftaran (
    id                BIGSERIAL PRIMARY KEY,
    lowongan_id       BIGINT NOT NULL REFERENCES lowongan(id) ON DELETE RESTRICT,
    user_id           BIGINT REFERENCES users(id) ON DELETE SET NULL,
    nim               VARCHAR(30)  NOT NULL,
    nama              VARCHAR(150) NOT NULL,
    program_studi_id  BIGINT NOT NULL REFERENCES program_studi(id) ON DELETE RESTRICT,
    semester          SMALLINT NOT NULL CHECK (semester BETWEEN 1 AND 14),
    ipk               NUMERIC(3,2) NOT NULL CHECK (ipk BETWEEN 0 AND 4),
    no_hp             VARCHAR(20)  NOT NULL,
    email             VARCHAR(150) NOT NULL,
    dokumen_path      VARCHAR(255) NOT NULL,   -- CV, portofolio, surat keterangan, transkrip/KHS dalam 1 PDF
    dokumen_nama_asli VARCHAR(255) NOT NULL,
    jawaban_tambahan  JSONB,
    status            VARCHAR(20) NOT NULL DEFAULT 'terdaftar'
                      CHECK (status IN ('terdaftar', 'proses', 'lulus', 'tidak_lulus')),
    catatan           TEXT,
    diproses_oleh     BIGINT REFERENCES users(id) ON DELETE SET NULL,
    diproses_at       TIMESTAMP,
    submitted_at      TIMESTAMP,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by        BIGINT REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT pendaftaran_unik UNIQUE (lowongan_id, nim)
);
CREATE INDEX pendaftaran_nim_index ON pendaftaran (nim);
CREATE INDEX pendaftaran_status_index ON pendaftaran (status);

-- ---------------------------------------------------------------------
-- 6. MAHASISWA AKTIF PDLK
--    sumber 'lowongan' : otomatis saat pendaftaran lulus
--    sumber 'mandiri'  : diinput koordinator PDLK prodi / Staf UPT
--    status: aktif | tidak_aktif (hanya force majeure) | selesai
-- ---------------------------------------------------------------------
CREATE TABLE peserta_pdlk (
    id                 BIGSERIAL PRIMARY KEY,
    nim                VARCHAR(50)  NOT NULL,
    nama               VARCHAR(150) NOT NULL,
    status             VARCHAR(20)  NOT NULL DEFAULT 'aktif'
                       CHECK (status IN ('aktif', 'tidak_aktif', 'selesai')),
    pendaftaran_id     BIGINT UNIQUE REFERENCES pendaftaran(id) ON DELETE SET NULL,
    sumber             VARCHAR(20) NOT NULL DEFAULT 'mandiri'
                       CHECK (sumber IN ('lowongan', 'mandiri')),
    user_id            BIGINT REFERENCES users(id) ON DELETE SET NULL,
    program_studi_id   BIGINT REFERENCES program_studi(id) ON DELETE SET NULL,
    jenis_kegiatan_id  BIGINT REFERENCES jenis_kegiatan_pdlk(id) ON DELETE SET NULL,
    mitra_id           BIGINT REFERENCES mitra(id) ON DELETE SET NULL,
    posisi             VARCHAR(150),
    tanggal_mulai      DATE,
    tanggal_selesai    DATE,
    status_pelaksanaan VARCHAR(30)
                       CHECK (status_pelaksanaan IN ('onboarding', 'berjalan', 'selesai_kegiatan')),
    mode_pelaksanaan   VARCHAR(20)
                       CHECK (mode_pelaksanaan IN ('onsite', 'remote', 'hybrid')),
    lokasi             VARCHAR(200),
    alasan_tidak_aktif TEXT,
    submitted_at       TIMESTAMP,
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by         BIGINT REFERENCES users(id) ON DELETE SET NULL
);
CREATE INDEX peserta_pdlk_nim_index ON peserta_pdlk (nim);
CREATE INDEX peserta_pdlk_status_index ON peserta_pdlk (status);

-- created_by = pengguna yang mengubah status.
CREATE TABLE riwayat_status_peserta (
    id              BIGSERIAL PRIMARY KEY,
    peserta_pdlk_id BIGINT NOT NULL REFERENCES peserta_pdlk(id) ON DELETE CASCADE,
    status_lama     VARCHAR(20),
    status_baru     VARCHAR(20) NOT NULL,
    alasan          TEXT,
    submitted_at    TIMESTAMP,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by      BIGINT REFERENCES users(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------------------
-- 7. SELAMA PELAKSANAAN
-- ---------------------------------------------------------------------
CREATE TABLE progres_pelaksanaan (
    id                 BIGSERIAL PRIMARY KEY,
    peserta_pdlk_id    BIGINT NOT NULL REFERENCES peserta_pdlk(id) ON DELETE CASCADE,
    status_pelaksanaan VARCHAR(30) NOT NULL
                       CHECK (status_pelaksanaan IN ('onboarding', 'berjalan', 'selesai_kegiatan')),
    mode_pelaksanaan   VARCHAR(20) NOT NULL
                       CHECK (mode_pelaksanaan IN ('onsite', 'remote', 'hybrid')),
    lokasi             VARCHAR(200),
    catatan            TEXT,
    submitted_at       TIMESTAMP,
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by         BIGINT REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE bukti_pelaksanaan (
    id               BIGSERIAL PRIMARY KEY,
    peserta_pdlk_id  BIGINT NOT NULL REFERENCES peserta_pdlk(id) ON DELETE CASCADE,
    keterangan       VARCHAR(255) NOT NULL,
    tanggal_kegiatan DATE,
    file_path        VARCHAR(255) NOT NULL,
    submitted_at     TIMESTAMP,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by       BIGINT REFERENCES users(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------------------
-- 8. MONEV TENGAH DAN AKHIR
-- ---------------------------------------------------------------------
CREATE TABLE kuesioner_monev (
    id           BIGSERIAL PRIMARY KEY,
    jenis        VARCHAR(10)  NOT NULL CHECK (jenis IN ('tengah', 'akhir')),
    judul        VARCHAR(200) NOT NULL,
    deskripsi    TEXT,
    is_active    BOOLEAN NOT NULL DEFAULT TRUE,
    submitted_at TIMESTAMP,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by   BIGINT REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE kuesioner_pertanyaan (
    id                 BIGSERIAL PRIMARY KEY,
    kuesioner_monev_id BIGINT NOT NULL REFERENCES kuesioner_monev(id) ON DELETE CASCADE,
    pertanyaan         TEXT NOT NULL,
    tipe               VARCHAR(20) NOT NULL DEFAULT 'textarea'
                       CHECK (tipe IN ('text', 'textarea', 'skala', 'pilihan')),
    opsi               JSONB,
    wajib              BOOLEAN NOT NULL DEFAULT TRUE,
    urutan             SMALLINT NOT NULL DEFAULT 0,
    submitted_at       TIMESTAMP,
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by         BIGINT REFERENCES users(id) ON DELETE SET NULL
);

-- submitted_at = waktu mahasiswa mengirim monev.
CREATE TABLE monev_pengisian (
    id                 BIGSERIAL PRIMARY KEY,
    peserta_pdlk_id    BIGINT NOT NULL REFERENCES peserta_pdlk(id) ON DELETE CASCADE,
    kuesioner_monev_id BIGINT NOT NULL REFERENCES kuesioner_monev(id) ON DELETE RESTRICT,
    jenis              VARCHAR(10) NOT NULL CHECK (jenis IN ('tengah', 'akhir')),
    submitted_at       TIMESTAMP,
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by         BIGINT REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT monev_pengisian_unik UNIQUE (peserta_pdlk_id, jenis)
);

CREATE TABLE monev_jawaban (
    id                      BIGSERIAL PRIMARY KEY,
    monev_pengisian_id      BIGINT NOT NULL REFERENCES monev_pengisian(id) ON DELETE CASCADE,
    kuesioner_pertanyaan_id BIGINT NOT NULL REFERENCES kuesioner_pertanyaan(id) ON DELETE RESTRICT,
    jawaban                 TEXT,
    submitted_at            TIMESTAMP,
    created_at              TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at              TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by              BIGINT REFERENCES users(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------------------
-- 9. Pasang trigger updated_at ke semua tabel PDLK (+ users)
-- ---------------------------------------------------------------------
DO $$
DECLARE t TEXT;
BEGIN
    FOREACH t IN ARRAY ARRAY[
        'users', 'program_studi', 'roles', 'user_roles', 'jenis_kegiatan_pdlk', 'mitra',
        'postingan', 'lowongan', 'lowongan_field_tambahan', 'pendaftaran',
        'peserta_pdlk', 'riwayat_status_peserta', 'progres_pelaksanaan',
        'bukti_pelaksanaan', 'kuesioner_monev', 'kuesioner_pertanyaan',
        'monev_pengisian', 'monev_jawaban'
    ] LOOP
        EXECUTE format('DROP TRIGGER IF EXISTS trg_%1$s_updated_at ON %1$I', t);
        EXECUTE format(
            'CREATE TRIGGER trg_%1$s_updated_at BEFORE UPDATE ON %1$I
             FOR EACH ROW EXECUTE FUNCTION set_updated_at()', t);
    END LOOP;
END $$;

-- ---------------------------------------------------------------------
-- 10. DATA REFERENSI AWAL
-- ---------------------------------------------------------------------
INSERT INTO roles (kode, nama, submitted_at) VALUES
    ('mahasiswa',              'Mahasiswa',                      CURRENT_TIMESTAMP),
    ('staf_upt',               'Staf UPT PP-ESTEM',              CURRENT_TIMESTAMP),
    ('koordinator_pdlk_prodi', 'Koordinator PDLK Program Studi', CURRENT_TIMESTAMP),
    ('koordinator_prodi',      'Koordinator Program Studi',      CURRENT_TIMESTAMP);

INSERT INTO jenis_kegiatan_pdlk (slug, nama, deskripsi, urutan, submitted_at) VALUES
    ('magang',             'Magang',                   'Pembelajaran di industri atau lembaga yang relevan dengan program studi.', 1, CURRENT_TIMESTAMP),
    ('kerja-praktik',      'Kerja Praktik',            'Penerapan ilmu dan keterampilan perkuliahan di lingkungan kerja nyata.',   2, CURRENT_TIMESTAMP),
    ('penelitian',         'Penelitian / Riset',       'Kegiatan penelitian non-skripsi bagi mahasiswa yang berminat meneliti.',   3, CURRENT_TIMESTAMP),
    ('pertukaran-pelajar', 'Pertukaran Pelajar',       'Perkuliahan di perguruan tinggi lain, di dalam maupun luar negeri.',       4, CURRENT_TIMESTAMP),
    ('kkn',                'Kuliah Kerja Nyata (KKN)', 'Membangun desa bersama masyarakat selama satu sampai dua semester.',       5, CURRENT_TIMESTAMP),
    ('studi-independen',   'Studi Independen',         'Mewujudkan karya atau produk inovatif yang dapat dilombakan.',             6, CURRENT_TIMESTAMP),
    ('kewirausahaan',      'Wirausaha',                'Pengembangan usaha mahasiswa secara terbimbing.',                          7, CURRENT_TIMESTAMP);

-- Periksa kembali nama dan kode prodi sesuai data resmi IT Del.
INSERT INTO program_studi (kode, nama, jenjang, submitted_at) VALUES
    ('D3TI',   'Teknologi Informasi',                'D3', CURRENT_TIMESTAMP),
    ('D3TK',   'Teknologi Komputer',                 'D3', CURRENT_TIMESTAMP),
    ('D4TRPL', 'Teknologi Rekayasa Perangkat Lunak', 'D4', CURRENT_TIMESTAMP),
    ('S1IF',   'Informatika',                        'S1', CURRENT_TIMESTAMP),
    ('S1SI',   'Sistem Informasi',                   'S1', CURRENT_TIMESTAMP),
    ('S1TE',   'Teknik Elektro',                     'S1', CURRENT_TIMESTAMP),
    ('S1MR',   'Manajemen Rekayasa',                 'S1', CURRENT_TIMESTAMP),
    ('S1TB',   'Teknik Bioproses',                   'S1', CURRENT_TIMESTAMP),
    ('S1BT',   'Bioteknologi',                       'S1', CURRENT_TIMESTAMP),
    ('S1MT',   'Teknik Metalurgi',                   'S1', CURRENT_TIMESTAMP);

-- ---------------------------------------------------------------------
-- 11. HAK AKSES AKUN APLIKASI
--     File ini biasanya dijalankan di pgAdmin sebagai user postgres,
--     sehingga tabel menjadi milik postgres dan Laravel (monitoring_app)
--     ditolak: "permission denied for table ...".
--     Jadikan monitoring_app pemilik semua tabel PDLK (sequence ikut).
--     Lewati otomatis bila role monitoring_app tidak ada / sudah pemilik.
-- ---------------------------------------------------------------------
DO $$
DECLARE r RECORD;
BEGIN
    IF EXISTS (SELECT 1 FROM pg_roles WHERE rolname = 'monitoring_app')
       AND current_user <> 'monitoring_app' THEN
        FOR r IN SELECT tablename FROM pg_tables WHERE schemaname = 'public' LOOP
            EXECUTE format('ALTER TABLE public.%I OWNER TO monitoring_app', r.tablename);
        END LOOP;
        ALTER FUNCTION set_updated_at() OWNER TO monitoring_app;
    END IF;
END $$;

COMMIT;

-- Cek hasil: semua tabel PDLK harus memiliki 4 kolom audit (hasil = 0 baris)
SELECT t.table_name, k.kolom AS kolom_hilang
FROM information_schema.tables t
CROSS JOIN (VALUES ('submitted_at'), ('created_at'), ('updated_at'), ('created_by')) AS k(kolom)
WHERE t.table_schema = 'public'
  AND t.table_name NOT IN ('sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs',
                           'migrations', 'password_reset_tokens', 'personal_access_tokens')
  AND NOT EXISTS (
      SELECT 1 FROM information_schema.columns c
      WHERE c.table_schema = 'public' AND c.table_name = t.table_name AND c.column_name = k.kolom
  );
