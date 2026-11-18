# Sistem Informasi PDLK – UPT PP-ESTEM IT Del

Tugas Akhir Kelompok 01 D3TI'24 · Laravel 12 · PHP 8.2 · PostgreSQL 16 · Login API CIS

## Menjalankan

```
php artisan serve
```

Buka http://127.0.0.1:8000. Halaman awal adalah beranda publik PDLK; tombol **Login** memakai akun CIS.

## Database

Hanya ada satu database, `monitoring_pdlk`, berisi **27 tabel**. Semua tabel dibuat dari satu query:

| File | Isi |
|---|---|
| `database/sql/skema_pdlk.sql` | Query pembuat 27 tabel + data referensi (peran, prodi, 7 bentuk PDLK). **Menghapus dan membuat ulang semua tabel beserta isinya.** |
| `database/sql/cek_data_pdlk.sql` | Query SELECT untuk memeriksa isi data |

Cara pakai: pgAdmin → klik kanan `monitoring_pdlk` → Query Tool → Open File → `skema_pdlk.sql` → F5.
Tidak memakai `php artisan migrate`. Perubahan struktur tabel ditulis langsung di `skema_pdlk.sql`.

Isi konten (berita, lowongan, galeri, data master) dilakukan lewat menu aplikasi.
Semua query CRUD memakai SQL mentah melalui `App\Support\Sql`.

## Sprint

Fitur dibuka bertahap mengikuti Tabel 3.3 Rencana Sprint di laporan, diatur oleh `PDLK_SPRINT` di `.env`:

| Nilai | Fitur yang tampil |
|---|---|
| `1` | PB-01 halaman guest · PB-02 lowongan · PB-03 login CIS & hak akses · PB-04 pendaftaran |
| `2` | + PB-05 progres pendaftaran · PB-06 status pendaftaran · PB-07/08 mahasiswa aktif · PB-09 progres pelaksanaan |
| `3` | + PB-10 kuesioner monev · PB-11 pengisian monev · PB-12 pemantauan prodi · PB-13 rekap · PB-14 status selesai |

Kode Sprint 2–3 sudah ada; menaikkan nilainya langsung membuka menu tersebut.
Setelah mengubah `.env`, jalankan `php artisan optimize:clear`.

## Peran

Mahasiswa dikenali dari login CIS. Peran pengelola diberikan lewat menu **Pengguna & Peran**
(login sebagai Staf UPT) atau perintah:

```
php artisan pdlk:beri-peran <username> upt_staff
php artisan pdlk:beri-peran <username> pdlk_coordinator --prodi=D3TI
php artisan pdlk:beri-peran <username> program_head --prodi=D3TI
```

## Pengujian

```
php artisan test
```

Tes berjalan di schema sementara `pengujian` di dalam `monitoring_pdlk` lalu dihapus otomatis,
sehingga data asli tidak tersentuh. Akun `monitoring_app` perlu izin
`GRANT CREATE ON DATABASE monitoring_pdlk TO monitoring_app;`.
