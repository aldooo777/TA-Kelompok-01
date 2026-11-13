<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    /**
     * Tabel PDLK dibuat manual dari database/sql/01_skema_pdlk.sql (bukan
     * migration). Setelah RefreshDatabase menjalankan migration bawaan
     * Laravel, skema PDLK dimuat dari file yang sama dengan yang dijalankan
     * di pgAdmin, sehingga pengujian memakai struktur tabel yang identik.
     *
     * Pengujian memakai database terpisah: monitoring_pdlk_test (lihat phpunit.xml).
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (! \Illuminate\Support\Facades\Schema::hasTable('mitra')) {
            $this->muatSkemaPdlk();
        }
    }

    private function muatSkemaPdlk(): void
    {
        $sql = file_get_contents(database_path('sql/01_skema_pdlk.sql'));

        // BEGIN/COMMIT di file tidak dipakai karena pengujian berjalan di dalam transaksi.
        $sql = preg_replace('/^\s*(BEGIN|COMMIT);\s*$/m', '', $sql);

        DB::unprepared($sql);
    }
}
