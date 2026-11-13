<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Rename tabel registrations menjadi peserta_pdlk
|--------------------------------------------------------------------------
| Berdasarkan hasil bimbingan UPT PP-ESTEM, tabel ini menyimpan data
| mahasiswa yang SUDAH diterima sebagai peserta PDLK, bukan pelamar.
|
| Tabel di-rename (bukan drop & create) sehingga seluruh data lama tetap
| dipertahankan. Pada PostgreSQL, nama sequence, primary key, dan index
| ikut di-rename agar konsisten dengan nama tabel baru.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('registrations', 'peserta_pdlk');

        if (DB::getDriverName() === 'pgsql') {
            $this->renamePgsqlObjects('registrations', 'peserta_pdlk');
        }
    }

    public function down(): void
    {
        Schema::rename('peserta_pdlk', 'registrations');

        if (DB::getDriverName() === 'pgsql') {
            $this->renamePgsqlObjects('peserta_pdlk', 'registrations');
        }
    }

    /**
     * Menyesuaikan nama sequence, primary key, dan index PostgreSQL.
     * IF EXISTS dipakai agar migration tetap aman bila nama objek berbeda.
     */
    private function renamePgsqlObjects(string $from, string $to): void
    {
        DB::statement("ALTER SEQUENCE IF EXISTS {$from}_id_seq RENAME TO {$to}_id_seq");
        DB::statement("ALTER INDEX IF EXISTS {$from}_pkey RENAME TO {$to}_pkey");
        DB::statement("ALTER INDEX IF EXISTS {$from}_mahasiswa_id_index RENAME TO {$to}_mahasiswa_id_index");
        DB::statement("ALTER INDEX IF EXISTS {$from}_status_index RENAME TO {$to}_status_index");
    }
};
