<?php

namespace Database\Seeders;

use App\Models\JenisKegiatan;
use App\Models\Lowongan;
use App\Models\Mitra;
use App\Models\Postingan;
use Illuminate\Database\Seeder;

/**
 * Data contoh untuk demo / Sprint Review (bukan data asli).
 *   php artisan db:seed --class=DemoPdlkSeeder
 */
class DemoPdlkSeeder extends Seeder
{
    public function run(): void
    {
        $jenis = JenisKegiatan::pluck('id', 'slug');

        $contoh = [
            ['Magang Software Engineer', 'PT Contoh Teknologi Nusantara', 'magang', 'Backend Developer', 'Jakarta / Hybrid',
                "Mahasiswa akan terlibat dalam pengembangan layanan backend dan API.",
                "- Mahasiswa aktif semester 5 ke atas\n- IPK minimal 3.00\n- Memahami dasar pemrograman web", 30],
            ['Kerja Praktik Jaringan', 'Dinas Kominfo Contoh', 'kerja-praktik', 'Teknisi Jaringan', 'Balige',
                "Membantu pemeliharaan jaringan dan layanan TI pemerintah daerah.",
                "- Mahasiswa D3/D4\n- Memahami dasar jaringan komputer", 14],
            ['Studi Independen Data Science', 'Lembaga Pelatihan Contoh', 'studi-independen', null, 'Remote',
                "Program pembelajaran data science selama satu semester.",
                "- Semester 4 ke atas\n- Berminat pada analisis data", 21],
        ];

        foreach ($contoh as [$judul, $mitra, $slugJenis, $posisi, $lokasi, $deskripsi, $kriteria, $hari]) {
            Lowongan::firstOrCreate(['judul' => $judul], [
                'mitra_id' => Mitra::dariNama($mitra)->id,
                'jenis_kegiatan_id' => $jenis[$slugJenis],
                'posisi' => $posisi,
                'lokasi' => $lokasi,
                'deskripsi' => $deskripsi,
                'kriteria' => $kriteria,
                'tanggal_buka' => now()->subDays(3),
                'tanggal_tutup' => now()->addDays($hari),
                'is_published' => true,
            ]);
        }

        Postingan::firstOrCreate(['judul' => 'Pembukaan Pendaftaran PDLK Semester Genap'], [
            'jenis' => 'pengumuman',
            'narasi' => "Pendaftaran PDLK semester genap telah dibuka.\nSilakan baca kriteria setiap lowongan sebelum mendaftar melalui akun CIS.",
        ]);

        Postingan::firstOrCreate(['judul' => 'Pembekalan Peserta Magang'], [
            'jenis' => 'kegiatan',
            'narasi' => 'UPT PP-ESTEM mengadakan pembekalan bagi mahasiswa yang akan melaksanakan magang.',
        ]);
    }
}
