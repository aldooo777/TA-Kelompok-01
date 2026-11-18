<?php

namespace App\Console\Commands;

use App\Services\CisService;
use App\Support\Prodi;
use App\Support\Sql;
use Illuminate\Console\Command;

/**
 * Mengisi prodi mahasiswa aktif yang masih kosong berdasarkan data CIS,
 * agar tampil di halaman koordinator.
 *
 *   php artisan pdlk:lengkapi-prodi
 *   php artisan pdlk:lengkapi-prodi --nim=42324032 --prodi=D3TI   (isi manual)
 */
class LengkapiProdi extends Command
{
    protected $signature = 'pdlk:lengkapi-prodi
        {--nim= : Hanya untuk NIM tertentu}
        {--prodi= : Kode prodi untuk diisi manual (dipakai bersama --nim)}';

    protected $description = 'Mengisi program studi mahasiswa aktif PDLK yang masih kosong';

    public function handle(CisService $cis): int
    {
        if ($this->option('prodi')) {
            $prodi = Sql::one('SELECT id, level, name FROM study_programs WHERE code = ?', [strtoupper($this->option('prodi'))]);

            if (!$prodi || !$this->option('nim')) {
                $this->error('Gunakan --nim=NIM --prodi=KODE. Kode tersedia: ' . Sql::all('SELECT code FROM study_programs ORDER BY code')->pluck('code')->implode(', '));

                return self::FAILURE;
            }

            $n = \DB::update('UPDATE pdlk_participants SET study_program_id = ? WHERE nim = ?', [$prodi->id, $this->option('nim')]);
            $this->info("{$n} data diisi prodi " . Prodi::label($prodi) . '.');

            return self::SUCCESS;
        }

        $daftar = Sql::all(
            'SELECT id, nim, name FROM pdlk_participants WHERE study_program_id IS NULL' . ($this->option('nim') ? ' AND nim = ?' : ''),
            $this->option('nim') ? [$this->option('nim')] : []
        );

        if ($daftar->isEmpty()) {
            $this->info('Semua mahasiswa aktif sudah memiliki prodi.');

            return self::SUCCESS;
        }

        foreach ($daftar as $p) {
            try {
                $mhs = $cis->findStudentByNim($p->nim);
            } catch (\Throwable $e) {
                $this->warn("{$p->nim} {$p->name}: CIS tidak dapat diakses ({$e->getMessage()}).");
                continue;
            }

            $teks = $mhs['prodi'] ?? null;
            $prodiId = Prodi::dariTeksCis($teks);

            if ($prodiId) {
                Sql::update('pdlk_participants', $p->id, ['study_program_id' => $prodiId]);
                $this->info("{$p->nim} {$p->name}: " . Prodi::label(Sql::one('SELECT level, name FROM study_programs WHERE id = ?', [$prodiId])));
            } else {
                $this->warn("{$p->nim} {$p->name}: prodi CIS \"" . ($teks ?? '-') . '" tidak cocok. Isi manual dengan --nim=' . $p->nim . ' --prodi=KODE');
            }
        }

        return self::SUCCESS;
    }
}
