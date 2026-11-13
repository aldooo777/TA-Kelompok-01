-- =====================================================================
-- CEK DATA SISTEM INFORMASI PDLK  (database: monitoring_pdlk)
-- Cara pakai di pgAdmin 4:
--   klik kanan monitoring_pdlk > Query Tool > ikon folder (Open File)
--   > pilih file ini > blok satu query > tekan F5.
-- Semua query di file ini hanya MEMBACA data (SELECT), aman dijalankan.
-- =====================================================================


-- ---------------------------------------------------------------------
-- 0. Daftar tabel dan jumlah barisnya
-- ---------------------------------------------------------------------
SELECT 'program_studi' AS tabel, COUNT(*) AS jumlah FROM program_studi
UNION ALL SELECT 'users', COUNT(*) FROM users
UNION ALL SELECT 'roles', COUNT(*) FROM roles
UNION ALL SELECT 'user_roles', COUNT(*) FROM user_roles
UNION ALL SELECT 'jenis_kegiatan_pdlk', COUNT(*) FROM jenis_kegiatan_pdlk
UNION ALL SELECT 'mitra', COUNT(*) FROM mitra
UNION ALL SELECT 'postingan', COUNT(*) FROM postingan
UNION ALL SELECT 'lowongan', COUNT(*) FROM lowongan
UNION ALL SELECT 'lowongan_field_tambahan', COUNT(*) FROM lowongan_field_tambahan
UNION ALL SELECT 'pendaftaran', COUNT(*) FROM pendaftaran
UNION ALL SELECT 'peserta_pdlk', COUNT(*) FROM peserta_pdlk
UNION ALL SELECT 'riwayat_status_peserta', COUNT(*) FROM riwayat_status_peserta
UNION ALL SELECT 'progres_pelaksanaan', COUNT(*) FROM progres_pelaksanaan
UNION ALL SELECT 'bukti_pelaksanaan', COUNT(*) FROM bukti_pelaksanaan
UNION ALL SELECT 'kuesioner_monev', COUNT(*) FROM kuesioner_monev
UNION ALL SELECT 'monev_pengisian', COUNT(*) FROM monev_pengisian;


-- ---------------------------------------------------------------------
-- 1. Pengguna dan peran  (halaman: login, menu sesuai peran)
-- ---------------------------------------------------------------------
-- 1a. Akun yang pernah login lewat CIS
SELECT id, username, name AS nama, nim, cis_role, last_login_at
FROM users
ORDER BY last_login_at DESC NULLS LAST;

-- 1b. Peran PDLK setiap akun (Staf UPT / Koordinator)
SELECT u.username, u.name AS nama, r.nama AS peran,
       COALESCE(pr.kode, 'Semua prodi') AS cakupan_prodi
FROM user_roles ur
JOIN users u ON u.id = ur.user_id
JOIN roles r ON r.id = ur.role_id
LEFT JOIN program_studi pr ON pr.id = ur.program_studi_id
ORDER BY u.username;


-- ---------------------------------------------------------------------
-- 2. Data master
-- ---------------------------------------------------------------------
SELECT id, kode, jenjang, nama FROM program_studi ORDER BY jenjang, nama;
SELECT id, slug, nama FROM jenis_kegiatan_pdlk ORDER BY urutan;
SELECT id, nama, bidang, alamat FROM mitra ORDER BY nama;


-- ---------------------------------------------------------------------
-- 3. Informasi publik  (halaman: Beranda, Berita, Lowongan)
-- ---------------------------------------------------------------------
-- 3a. Berita / pengumuman / kegiatan
SELECT id, jenis, judul, foto_path,
       CASE WHEN is_published THEN 'Terbit' ELSE 'Draf' END AS status,
       published_at
FROM postingan
ORDER BY published_at DESC NULLS LAST;

-- 3b. Lowongan beserta status buka/tutup
SELECT l.id, l.judul, m.nama AS mitra, j.nama AS bentuk_kegiatan,
       l.tanggal_buka, l.tanggal_tutup, l.kuota,
       CASE
           WHEN NOT l.is_published THEN 'Disembunyikan'
           WHEN l.tanggal_tutup < CURRENT_DATE THEN 'Ditutup'
           WHEN l.tanggal_buka > CURRENT_DATE THEN 'Belum dibuka'
           ELSE 'Dibuka'
       END AS status
FROM lowongan l
JOIN mitra m ON m.id = l.mitra_id
JOIN jenis_kegiatan_pdlk j ON j.id = l.jenis_kegiatan_id
ORDER BY l.created_at DESC;

-- 3c. Isian tambahan (tidak wajib) per lowongan
SELECT l.judul AS lowongan, f.urutan, f.label, f.tipe
FROM lowongan_field_tambahan f
JOIN lowongan l ON l.id = f.lowongan_id
ORDER BY l.judul, f.urutan;


-- ---------------------------------------------------------------------
-- 4. Pendaftaran  (halaman: Pendaftaran Saya, Kelola Pendaftar)
--    Status: terdaftar -> proses -> lulus / tidak_lulus
-- ---------------------------------------------------------------------
-- 4a. Daftar pendaftar
SELECT p.id, p.nim, p.nama, pr.kode AS prodi, p.semester, p.ipk,
       p.no_hp, p.email, l.judul AS lowongan, m.nama AS mitra,
       p.status, p.jawaban_tambahan, p.created_at AS tanggal_daftar
FROM pendaftaran p
JOIN lowongan l ON l.id = p.lowongan_id
JOIN mitra m ON m.id = l.mitra_id
JOIN program_studi pr ON pr.id = p.program_studi_id
ORDER BY p.created_at DESC;

-- 4b. Jumlah pendaftar per lowongan dan status
SELECT l.judul AS lowongan,
       COUNT(p.id) AS total,
       COUNT(*) FILTER (WHERE p.status = 'terdaftar')   AS terdaftar,
       COUNT(*) FILTER (WHERE p.status = 'proses')      AS proses,
       COUNT(*) FILTER (WHERE p.status = 'lulus')       AS lulus,
       COUNT(*) FILTER (WHERE p.status = 'tidak_lulus') AS tidak_lulus
FROM lowongan l
LEFT JOIN pendaftaran p ON p.lowongan_id = l.id
GROUP BY l.id, l.judul
ORDER BY total DESC;


-- ---------------------------------------------------------------------
-- 5. Mahasiswa aktif PDLK  (halaman: Mahasiswa Aktif, Dashboard)
--    Status: aktif | tidak_aktif (force majeure) | selesai
--    Sumber: lowongan (lulus seleksi) | mandiri (diinput koordinator/UPT)
-- ---------------------------------------------------------------------
-- 5a. Semua mahasiswa aktif PDLK
SELECT ps.id, ps.nim, ps.nama,
       COALESCE(pr.kode, '(prodi kosong)') AS prodi,
       j.nama AS kegiatan, m.nama AS mitra,
       ps.sumber, ps.status, ps.alasan_tidak_aktif
FROM peserta_pdlk ps
LEFT JOIN program_studi pr ON pr.id = ps.program_studi_id
LEFT JOIN jenis_kegiatan_pdlk j ON j.id = ps.jenis_kegiatan_id
LEFT JOIN mitra m ON m.id = ps.mitra_id
ORDER BY ps.nama;

-- 5b. Mahasiswa yang prodinya masih kosong
--     (tidak akan tampil di halaman koordinator prodi)
SELECT id, nim, nama, status FROM peserta_pdlk WHERE program_studi_id IS NULL;

-- 5c. Rekap per prodi
SELECT COALESCE(pr.kode, '(kosong)') AS prodi,
       COUNT(*) AS total,
       COUNT(*) FILTER (WHERE ps.status = 'aktif')       AS aktif,
       COUNT(*) FILTER (WHERE ps.status = 'selesai')     AS selesai,
       COUNT(*) FILTER (WHERE ps.status = 'tidak_aktif') AS tidak_aktif
FROM peserta_pdlk ps
LEFT JOIN program_studi pr ON pr.id = ps.program_studi_id
GROUP BY pr.kode
ORDER BY prodi;

-- 5d. Riwayat perubahan status
SELECT r.created_at AS waktu, ps.nim, ps.nama,
       r.status_lama, r.status_baru, r.alasan, u.username AS diubah_oleh
FROM riwayat_status_peserta r
JOIN peserta_pdlk ps ON ps.id = r.peserta_pdlk_id
LEFT JOIN users u ON u.id = r.created_by
ORDER BY r.created_at DESC;


-- ---------------------------------------------------------------------
-- 6. Alur lengkap: pendaftaran yang lulus -> otomatis jadi mahasiswa aktif
-- ---------------------------------------------------------------------
SELECT p.nim, p.nama, l.judul AS lowongan, p.status AS status_pendaftaran,
       ps.id AS id_peserta, ps.status AS status_peserta
FROM pendaftaran p
JOIN lowongan l ON l.id = p.lowongan_id
LEFT JOIN peserta_pdlk ps ON ps.pendaftaran_id = p.id
ORDER BY p.created_at DESC;
-- Hasil yang benar: setiap baris 'lulus' memiliki id_peserta,
-- baris lain (terdaftar/proses/tidak_lulus) id_peserta-nya kosong.


-- ---------------------------------------------------------------------
-- 7. Monev (Sprint 3 - tabel sudah ada, fitur belum dibuat)
--    Mahasiswa aktif yang belum mengisi monev tengah / akhir
-- ---------------------------------------------------------------------
SELECT ps.nim, ps.nama, COALESCE(pr.kode, '-') AS prodi,
       CASE WHEN mt.id IS NULL THEN 'Belum' ELSE 'Sudah' END AS monev_tengah,
       CASE WHEN ma.id IS NULL THEN 'Belum' ELSE 'Sudah' END AS monev_akhir
FROM peserta_pdlk ps
LEFT JOIN program_studi pr ON pr.id = ps.program_studi_id
LEFT JOIN monev_pengisian mt ON mt.peserta_pdlk_id = ps.id AND mt.jenis = 'tengah'
LEFT JOIN monev_pengisian ma ON ma.peserta_pdlk_id = ps.id AND ma.jenis = 'akhir'
WHERE ps.status = 'aktif'
ORDER BY prodi, ps.nama;
