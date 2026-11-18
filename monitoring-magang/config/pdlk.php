<?php

return [

    /*
    | Sprint yang sedang aktif (lihat Tabel 3.3 Rencana Sprint pada laporan).
    | 1 = PB-01–PB-04: halaman guest, lowongan, login CIS & hak akses, pendaftaran
    | 2 = + PB-05–PB-09: progres pendaftaran, status, mahasiswa aktif, progres pelaksanaan
    | 3 = + PB-10–PB-14: kuesioner monev, pemantauan prodi, rekap, status selesai
    | Fitur sprint berikutnya sudah ada di kode, hanya disembunyikan.
    */
    'sprint' => (int) env('PDLK_SPRINT', 1),

];
