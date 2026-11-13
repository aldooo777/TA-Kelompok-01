<?php

namespace App\Console\Commands;

use App\Models\PesertaPdlk;
use App\Models\ProgramStudi;
use App\Services\CisService;
use Illuminate\Console\Command;

/**
 * Mengisi prodi mahasiswa aktif yang masih kosong (data lama / input manual)
 * berdasarkan data mahasiswa di CIS, agar tampil di halaman koordinator.
 *
 *   php artisan pdlk:lengkapi-prodi
 *   php artisan pdlk:lengkapi-prodi --nim=42324032 --prodi=D4TRPL   (isi manual)
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
            $prodi = ProgramStudi::where('kode', strtoupper($this->option('prodi')))->first();

            if (!$prodi || !$this->option('nim')) {
                $this->error('Gunakan --nim=NIM --prodi=KODE. Kode tersedia: ' . ProgramStudi::pluck('kode')->implode(', '));

                return self::FAILURE;
            }

            $n = PesertaPdlk::where('nim', $this->option('nim'))->update(['program_studi_id' => $prodi->id]);
            $this->info("{$n} data diisi prodi {$prodi->label}.");

            return self::SUCCESS;
        }

        $daftar = PesertaPdlk::whereNull('program_studi_id')
            ->when($this->option('nim'), fn ($q, $nim) => $q->where('nim', $nim))
            ->get();

        if ($daftar->isEmpty()) {
            $this->info('Semua mahasiswa aktif sudah memiliki prodi.');

            return self::SUCCESS;
        }

        foreach ($daftar as $p) {
            try {
                $mhs = $cis->findStudentByNim($p->nim);
            } catch (\Throwable $e) {
                $this->warn("{$p->nim} {$p->nama}: CIS tidak dapat diakses ({$e->getMessage()}).");
                continue;
            }

            $teks = $mhs['prodi'] ?? null;
            $prodi = ProgramStudi::dariTeksCis($teks);

            if ($prodi) {
                $p->update(['program_studi_id' => $prodi->id]);
                $this->info("{$p->nim} {$p->nama}: {$prodi->label}");
            } else {
                $this->warn("{$p->nim} {$p->nama}: prodi CIS \"" . ($teks ?? '-') . '" tidak cocok. Isi manual dengan --nim=' . $p->nim . ' --prodi=KODE');
            }
        }

        return self::SUCCESS;
    }
}
