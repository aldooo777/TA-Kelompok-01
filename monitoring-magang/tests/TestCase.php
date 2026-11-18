<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

/**
 * Hanya ada SATU database (monitoring_pdlk). Agar data asli tidak tersentuh,
 * pengujian berjalan di schema sementara "pengujian" di database yang sama
 * (DB_SEARCH_PATH=pengujian di phpunit.xml). Schema ini dibuat dari
 * database/sql/skema_pdlk.sql saat tes mulai dan dihapus setelah tes selesai.
 * Setiap tes berjalan dalam transaksi yang di-rollback (DatabaseTransactions).
 */
abstract class TestCase extends BaseTestCase
{
    private static bool $skemaSiap = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (!self::$skemaSiap) {
            $this->siapkanSkemaPengujian();
            self::$skemaSiap = true;

            // Hapus schema sementara setelah semua tes selesai (koneksi PDO terpisah,
            // karena aplikasi Laravel sudah ditutup pada saat ini).
            $c = config('database.connections.pgsql');
            register_shutdown_function(function () use ($c) {
                try {
                    $pdo = new \PDO("pgsql:host={$c['host']};port={$c['port']};dbname={$c['database']}", $c['username'], $c['password']);
                    $pdo->exec('DROP SCHEMA IF EXISTS pengujian CASCADE');
                } catch (\Throwable $e) {
                    // abaikan: schema dibuat ulang pada pengujian berikutnya
                }
            });
        }
    }

    private function siapkanSkemaPengujian(): void
    {
        if (config('database.connections.pgsql.search_path') !== 'pengujian') {
            throw new \RuntimeException('Pengamanan: tes harus memakai DB_SEARCH_PATH=pengujian agar data asli tidak terhapus.');
        }

        DB::unprepared('DROP SCHEMA IF EXISTS pengujian CASCADE; CREATE SCHEMA pengujian;');

        if (DB::selectOne('SELECT current_schema() AS s')->s !== 'pengujian') {
            throw new \RuntimeException('Pengamanan: schema aktif bukan "pengujian".');
        }

        DB::unprepared(file_get_contents(database_path('sql/skema_pdlk.sql')));
    }
}
